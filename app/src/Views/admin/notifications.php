<div class="card" style="margin-bottom:1.5rem;">
    <div class="card-header"><h3>Envoyer une notification</h3></div>
    <div class="card-body">
        <form action="<?= Router::url('/admin/notifications/send') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="type">Type</label>
                <select name="type" id="type" class="form-control">
                    <option value="info">Information</option>
                    <option value="success">Succès</option>
                    <option value="warning">Avertissement</option>
                </select>
            </div>
            <div class="form-group">
                <label for="user_id">Destinataire</label>
                <select name="user_id" id="user_id" class="form-control">
                    <option value="all">Tous les utilisateurs</option>
                    <?php
                    $allUsers = User::all();
                    foreach ($allUsers as $u): ?>
                        <option value="<?= $u['id'] ?>"><?= h($u['full_name']) ?> (<?= h($u['email']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="message">Message</label>
                <textarea name="message" id="message" class="form-control" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Envoyer</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Historique des notifications</h3></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th>ID</th><th>Utilisateur</th><th>Type</th><th>Message</th><th>Lue</th><th>Date</th></tr></thead>
            <tbody>
                <?php foreach ($notifications as $n): ?>
                    <tr>
                        <td><?= $n['id'] ?></td>
                        <td><?= h($n['user_name']) ?></td>
                        <td><span class="badge badge-<?= $n['type'] ?>"><?= h($n['type']) ?></span></td>
                        <td><?= h(substr($n['message'], 0, 50)) ?></td>
                        <td><?= $n['is_read'] ? 'Oui' : 'Non' ?></td>
                        <td><?= h(date('d/m/Y', strtotime($n['created_at']))) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
