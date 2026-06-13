<div class="container">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
        <h1>Notifications</h1>
        <a href="<?= Router::url('/notifications/read-all') ?>" class="btn btn-sm btn-outline">Tout marquer comme lu</a>
    </div>

    <?php if (empty($notifications)): ?>
        <div class="empty-state"><p>Aucune notification.</p></div>
    <?php else: ?>
        <?php foreach ($notifications as $n): ?>
            <div class="card" style="margin-bottom:0.75rem;<?= $n['is_read'] ? '' : 'border-color:var(--primary);' ?>">
                <div class="card-body" style="display:flex;justify-content:space-between;align-items:center;">
                    <div>
                        <p><?= h($n['message']) ?></p>
                        <small style="color:var(--text-muted);"><?= time_ago($n['created_at']) ?></small>
                    </div>
                    <div style="display:flex;gap:0.5rem;align-items:center;">
                        <?php if (!$n['is_read']): ?>
                            <a href="<?= Router::url('/notifications/read/' . $n['id']) ?>" class="btn btn-sm btn-outline">Marquer lu</a>
                        <?php endif; ?>
                        <?php if ($n['link']): ?>
                            <a href="<?= Router::url($n['link']) ?>" class="btn btn-sm btn-primary">Voir</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
