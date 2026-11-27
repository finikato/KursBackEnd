<?php
use Core\Session;
use Models\User;
use Models\GameMailSystem;

class MailController {
    private GameMailSystem $mailSystem;

    public function __construct() {
        $this->mailSystem = new GameMailSystem();
        if (!Session::get('user_id')) {
            header('Location: /auth/login'); 
            exit;
        }
    }

    public function index() {
        $userId = Session::get('user_id');
        $u = User::getById($userId);
        $claimMessage = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['claim_reward'])) {
            $res = $this->mailSystem->claimReward((int)$_POST['message_id'], $userId);
            $claimMessage = "<div class='alert alert-" . (isset($res['error']) ? 'error' : 'success') . "'>" . ($res['error'] ?? $res['success'] ?? '') . "</div>";
            $u = User::getById($userId);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_mail'])) {
            $rewardIds = $_POST['reward_ids'] ?? [];
            if (empty($rewardIds)) {
                $claimMessage = "<div class='alert alert-error'>Выберите хотя бы одну награду</div>";
            } else {
                $this->mailSystem->sendSystemMail($userId, $_POST['subject'], $_POST['body'], array_map('intval', $rewardIds));
                header('Location: /mail'); 
                exit;
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_player_mail'])) {
            $search = trim($_POST['target_search'] ?? '');
            if (empty($search)) {
                $claimMessage = "<div class='alert alert-error'>Введите Email или ID игрока</div>";
            } else {
                $target = $this->mailSystem->findPlayerByIdOrEmail($search);
                if (!$target) {
                    $claimMessage = "<div class='alert alert-error'>Игрок не найден</div>";
                } else {
                    $this->mailSystem->sendPlayerMail($userId, (int)$target['id'], $_POST['subject'], $_POST['body']);
                    $claimMessage = "<div class='alert alert-success'>Письмо отправлено игроку <b>{$target['name']}</b></div>";
                }
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_mail'])) {
            $res = $this->mailSystem->deletePlayerMail((int)$_POST['message_id'], $userId);
            if ($res) {
                header('Location: /mail'); 
                exit;
            } else {
                $claimMessage = "<div class='alert alert-error'>Не удалось удалить сообщение</div>";
            }
        }

        $inbox = $this->mailSystem->getInbox($userId);
        $currentMailId = isset($_GET['id']) ? (int)$_GET['id'] : (!empty($inbox) ? $inbox[0]['id'] : 0);
        $currentMail = null;
        $currentRewards = [];

        foreach ($inbox as $msg) {
            if ($msg['id'] == $currentMailId) { 
                $currentMail = $msg; 
                $currentRewards = $this->mailSystem->getMessageRewards($currentMailId); 
                break; 
            }
        }

        $rewards = $this->mailSystem->getAvailableRewards();
        
        require __DIR__ . '/../Views/mail/index.php';
    }
}