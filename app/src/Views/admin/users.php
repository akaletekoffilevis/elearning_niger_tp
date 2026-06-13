<div class="card">
    <div class="card-header">
        <h3>Utilisateurs</h3>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Pseudo</th>
                    <th>Rôle</th>
                    <th>Inscrit le</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= h($u['id']) ?></td>
                        <td><?= h($u['full_name']) ?></td>
                        <td><?= h($u['email']) ?></td>
                        <td><?= h($u['username']) ?></td>
                        <td><span class="badge badge-<?= $u['role'] ?>"><?= h($u['role']) ?></span></td>
                        <td><?= h(date('d/m/Y', strtotime($u['created_at']))) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
