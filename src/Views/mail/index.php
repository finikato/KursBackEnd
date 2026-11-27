<?php 
$pageTitle = 'Почта';
ob_start(); 
?>

<div class="player-card">
    <div class="player-info">
        <h1>Привет, <span><?= htmlspecialchars($u['name']) ?></span></h1>
        <p>Уровень <?= $u['level'] ?? 1 ?> · Игрок</p>
    </div>
    <div class="stats">
        <?= $u['gold'] ?? 0 ?> Золота
    </div>
</div>

<?= $claimMessage ?>

<div class="mail-layout">
    
    <div class="mail-list">
        <?php if (empty($inbox)): ?>
            <div class="empty-box" style="padding: 30px;">Нет писем</div>
        <?php else: ?>
            <?php foreach ($inbox as $msg): ?>
                <a href="/mail?id=<?= $msg['id'] ?>" class="mail-item <?= ($msg['id'] == $currentMailId) ? 'active' : '' ?> <?= $msg['rewards_list'] ? 'unread' : '' ?>">
                    <div class="mail-item-title"><?= htmlspecialchars($msg['subject']) ?></div>
                    <div class="mail-item-sender">
                        <?= $msg['sender_id'] ? htmlspecialchars($msg['sender_name']) : 'Система' ?>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="mail-content">
        <?php if ($currentMail): ?>
            <div class="mail-content-header">
                <div class="mail-content-title"><?= htmlspecialchars($currentMail['subject']) ?></div>
                <div class="mail-content-meta">
                    <?php if ($currentMail['sender_id']): ?>
                        <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 10px;">
                            <?php if (!empty($currentMail['sender_avatar']) && file_exists('/var/www/html/' . $currentMail['sender_avatar'])): ?>
                                <img src="/<?= htmlspecialchars($currentMail['sender_avatar']) ?>" alt="Avatar" style="width: 72px; height: 72px; border-radius: 50%; object-fit: cover; border: 3px solid #2575fc; flex-shrink: 0;">
                            <?php else: ?>
                                <div style="width: 72px; height: 72px; background: #2575fc; border-radius: 50%; flex-shrink: 0;"></div>
                            <?php endif; ?>
                            
                            <div style="display: flex; flex-direction: column; justify-content: center;">
                                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 5px;">
                                    <a href="/profile?id=<?= $currentMail['sender_id'] ?>" style="font-weight: 700; font-size: 1.2rem; color: #fff; text-decoration: none;">
                                        <?= htmlspecialchars($currentMail['sender_name']) ?>
                                    </a>
                                    <span style="font-size: 0.85rem; color: #cfc8dc;">(ID: #<?= $currentMail['sender_id'] ?>)</span>
                                </div>
                                <div style="font-size: 0.9rem; color: #8ef3b5;"><?= htmlspecialchars($currentMail['sender_email']) ?></div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div>От: Система</div>
                    <?php endif; ?>
                    
                    <div style="margin-top: 10px; font-size: 0.8rem; color: #cfc8dc; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 10px;">
                        <?= $currentMail['created_at'] ?>
                    </div>
                </div>
            </div>

            <div class="mail-content-body">
                <?= nl2br(htmlspecialchars($currentMail['body'])) ?>
            </div>

            <?php if (!empty($currentRewards)): ?>
                <div style="margin-top: 30px;">
                    <h4 style="color: #cfc8dc; margin-bottom: 10px; font-weight: 500;">Вложения:</h4>
                    <div class="mail-rewards">
                        <?php foreach ($currentRewards as $rw): 
                            $typeClass = 'reward-item'; 
                            $nameLower = mb_strtolower($rw['name']);
                            
                            if ($rw['type'] === 'gold' || strpos($nameLower, 'золот') !== false) {
                                $typeClass = 'reward-gold';
                            } elseif ($rw['type'] === 'exp') {
                                $typeClass = 'reward-exp';
                            } elseif (strpos($nameLower, 'зелье') !== false) {
                                $typeClass = 'reward-potion';
                            }
                        ?>
                            <div class="reward-box <?= $typeClass ?>">
                                <div class="reward-count">x<?= $rw['amount'] ?></div>
                                <div class="reward-name"><?= htmlspecialchars($rw['name']) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="mail-actions" style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <?php if (!empty($currentRewards)): ?>
                        <?php if (!$currentMail['is_claimed']): ?>
                            <form method="POST" action="/mail" style="margin:0;">
                                <input type="hidden" name="message_id" value="<?= $currentMail['id'] ?>">
                                <button type="submit" name="claim_reward" class="btn-receive">
                                    Получить
                                </button>
                            </form>
                        <?php else: ?>
                            <span class="status-claimed">Награда уже получена</span>
                        <?php endif; ?>
                    <?php else: ?>
                        <span class="status-claimed" style="color: #cfc8dc; border-color: rgba(255,255,255,0.2); background: rgba(255,255,255,0.05);">
                            Сообщение прочитано
                        </span>
                    <?php endif; ?>
                </div>

                <?php if ($currentMail['sender_id']): ?>
                    <form method="POST" action="/mail" style="margin:0;" onsubmit="return confirm('Вы уверены, что хотите удалить это письмо?');">
                        <input type="hidden" name="message_id" value="<?= $currentMail['id'] ?>">
                        <button type="submit" name="delete_mail" style="background: rgba(255, 75, 43, 0.2); color: #ffb4a1; border: 1px solid rgba(255, 75, 43, 0.4); padding: 10px 20px; border-radius: 25px; cursor: pointer; transition: 0.3s; font-weight: 600;">
                            Удалить
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="empty-mail">
                <p>Выберите письмо слева</p>
            </div>
        <?php endif; ?>
    </div>

</div>

<details style="margin-top: 30px;">
    <summary>Генератор наград (Система)</summary>
    <div class="mailbox" style="margin-top: 15px; padding: 20px;">
        <form method="POST" action="/mail" class="gen-form">
            <div style="display: flex; flex-direction: column; gap: 15px;">
                <div>
                    <label style="display:block; margin-bottom:5px; color:#cfc8dc;">Тема письма:</label>
                    <select name="subject" required>
                        <option value="Награда за вход!">Награда за вход!</option>
                        <option value="Компенсация за техработы">Компенсация за техработы</option>
                        <option value="Промокод активирован">Промокод активирован</option>
                        <option value="Подарок от разработчиков">Подарок от разработчиков</option>
                    </select>
                </div>

                <div>
                    <label style="display:block; margin-bottom:10px; color:#cfc8dc;">Выберите награды:</label>
                    <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                        <?php foreach ($rewards as $r): ?>
                            <label style="display: flex; align-items: center; gap: 8px; background: rgba(0,0,0,0.3); padding: 8px 12px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1); cursor: pointer; color: #fff;">
                                <input type="checkbox" name="reward_ids[]" value="<?= $r['id'] ?>">
                                <?= htmlspecialchars($r['name']) ?> (<?= $r['amount'] ?>)
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div>
                    <label style="display:block; margin-bottom:5px; color:#cfc8dc;">Текст письма:</label>
                    <textarea name="body" rows="3">Путешественник! Вот награда, которую вы заслужили.</textarea>
                </div>
            </div>
            <button type="submit" name="send_mail" class="btn-submit" style="margin-top: 15px;">Отправить награду</button>
        </form>
    </div>
</details>

<details style="margin-top: 15px;">
    <summary>Написать игроку (Игрок)</summary>
    <div class="mailbox" style="margin-top: 15px; padding: 20px;">
        <form method="POST" action="/mail" class="gen-form">
            <div style="display: flex; flex-direction: column; gap: 15px;">
                <div>
                    <label style="display:block; margin-bottom:5px; color:#cfc8dc;">Кому (Email или ID игрока):</label>
                    <input type="text" name="target_search" placeholder="example@mail.ru или 123" required>
                </div>

                <div>
                    <label style="display:block; margin-bottom:5px; color:#cfc8dc;">Тема:</label>
                    <input type="text" name="subject" placeholder="Привет!" required>
                </div>

                <div>
                    <label style="display:block; margin-bottom:5px; color:#cfc8dc;">Текст письма:</label>
                    <textarea name="body" rows="3" placeholder="Текст сообщения..."></textarea>
                </div>
            </div>
            <button type="submit" name="send_player_mail" class="btn-submit" style="margin-top: 15px;">Отправить игроку</button>
        </form>
    </div>
</details>

<?php 
$content = ob_get_clean(); 
require __DIR__ . '/../layout.php'; 
?>