<?php
namespace Models;

use Core\Database;

class GameMailSystem {
    private \mysqli $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAvailableRewards(): array {
        $check = $this->db->query("SELECT COUNT(*) as cnt FROM reward_templates");
        if ($check->fetch_assoc()['cnt'] == 0) {
            $this->db->query("INSERT INTO reward_templates (id, name, type, amount) VALUES 
                (1, 'Сундук с золотом', 'gold', 500),
                (2, 'Мешок золота', 'gold', 1500),
                (3, 'Опыт', 'exp', 100),
                (4, 'Эпический предмет', 'item', 1),
                (5, 'Зелье здоровья', 'item', 5)
            ");
        }
        return $this->db->query("SELECT * FROM reward_templates ORDER BY id ASC")->fetch_all(MYSQLI_ASSOC);
    }

    public function findPlayerByIdOrEmail(string $search): ?array {
        if (is_numeric($search)) {
            $stmt = $this->db->prepare("SELECT id, name, email FROM users WHERE id = ? LIMIT 1");
            $stmt->bind_param('i', $search);
        } else {
            $stmt = $this->db->prepare("SELECT id, name, email FROM users WHERE email = ? LIMIT 1");
            $stmt->bind_param('s', $search);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public function sendSystemMail(int $receiverId, string $subject, string $body, array $rewardIds): bool {
        $stmt = $this->db->prepare("INSERT INTO mail_messages (sender_id, receiver_id, subject, body) VALUES (NULL, ?, ?, ?)");
        $stmt->bind_param('iss', $receiverId, $subject, $body);
        
        if (!$stmt->execute()) {
            return false;
        }
        
        $messageId = $this->db->insert_id;
        $stmt2 = $this->db->prepare("INSERT INTO mail_attachments (message_id, reward_id) VALUES (?, ?)");
        
        foreach ($rewardIds as $rewardId) {
            $id = (int)$rewardId;
            $stmt2->bind_param('ii', $messageId, $id);
            $stmt2->execute();
        }
        return true;
    }

    public function sendPlayerMail(int $senderId, int $receiverId, string $subject, string $body): bool {
        $stmt = $this->db->prepare("INSERT INTO mail_messages (sender_id, receiver_id, subject, body) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('iiss', $senderId, $receiverId, $subject, $body);
        return $stmt->execute();
    }

    public function getInbox(int $userId): array {
        $stmt = $this->db->prepare("
            SELECT m.*, u.name as sender_name, u.email as sender_email, u.avatar as sender_avatar,
                   (SELECT GROUP_CONCAT(CONCAT(r.name, ' (', r.amount, ')')) FROM mail_attachments a 
                    JOIN reward_templates r ON a.reward_id = r.id WHERE a.message_id = m.id) as rewards_list
            FROM mail_messages m
            LEFT JOIN users u ON m.sender_id = u.id
            WHERE m.receiver_id = ? AND m.is_claimed = FALSE
            ORDER BY m.created_at DESC
        ");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function claimReward(int $messageId, int $userId): array {
        $stmt = $this->db->prepare("SELECT * FROM mail_messages WHERE id = ? AND receiver_id = ? AND is_claimed = FALSE");
        $stmt->bind_param('ii', $messageId, $userId);
        $stmt->execute();
        if (!$stmt->get_result()->fetch_assoc()) return ['error' => 'Награда уже получена'];

        $stmtR = $this->db->prepare("SELECT r.* FROM reward_templates r JOIN mail_attachments a ON r.id = a.reward_id WHERE a.message_id = ?");
        $stmtR->bind_param('i', $messageId);
        $stmtR->execute();
        $rewards = $stmtR->get_result()->fetch_all(MYSQLI_ASSOC);
        
        $this->db->begin_transaction();
        try {
            $totalGold = 0; $totalExp = 0; $items = [];
            foreach ($rewards as $r) {
                if ($r['type'] === 'gold') $totalGold += $r['amount'];
                if ($r['type'] === 'exp') $totalExp += $r['amount'];
                if ($r['type'] === 'item') $items[] = $r['name'];
            }
            if ($totalGold > 0) {
                $stmtG = $this->db->prepare("UPDATE users SET gold = gold + ? WHERE id = ?");
                $stmtG->bind_param('ii', $totalGold, $userId);
                $stmtG->execute();
            }
            if ($totalExp > 0) {
                $stmtU = $this->db->prepare("SELECT level, exp FROM users WHERE id = ?");
                $stmtU->bind_param('i', $userId);
                $stmtU->execute();
                $ud = $stmtU->get_result()->fetch_assoc();
                $lvl = (int)$ud['level']; $exp = (int)$ud['exp'] + $totalExp;
                $needed = round(100 * pow(1.2, $lvl - 1));
                while ($exp >= $needed) {
                    $exp -= $needed; $lvl++;
                    $needed = round(100 * pow(1.2, $lvl - 1));
                }
                $stmtUpd = $this->db->prepare("UPDATE users SET level = ?, exp = ? WHERE id = ?");
                $stmtUpd->bind_param('iii', $lvl, $exp, $userId);
                $stmtUpd->execute();
            }
            if (!empty($items)) {
                foreach ($rewards as $r) {
                    if ($r['type'] === 'item') {
                        $stmtInv = $this->db->prepare("
                            INSERT INTO user_inventory (user_id, item_name, quantity) 
                            VALUES (?, ?, ?) 
                            ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)
                        ");
                        $stmtInv->bind_param('isi', $userId, $r['name'], $r['amount']);
                        $stmtInv->execute();
                    }
                }
            }
            $stmtC = $this->db->prepare("UPDATE mail_messages SET is_claimed = TRUE, is_read = TRUE WHERE id = ?");
            $stmtC->bind_param('i', $messageId);
            $stmtC->execute();
            $this->db->commit();
            return ['success' => 'Награда получена'];
        } catch (\Exception $e) {
            $this->db->rollback();
            return ['error' => 'Ошибка начисления'];
        }
    }
    public function getMessageRewards(int $messageId): array {
        $stmt = $this->db->prepare("SELECT r.name, r.amount, r.type FROM reward_templates r JOIN mail_attachments a ON r.id = a.reward_id WHERE a.message_id = ?");
        $stmt->bind_param('i', $messageId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    public function getUserInventory(int $userId): array {
        $stmt = $this->db->prepare("SELECT item_name, quantity FROM user_inventory WHERE user_id = ? ORDER BY item_name ASC");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}