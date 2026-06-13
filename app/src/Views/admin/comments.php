<div class="card">
    <div class="card-header"><h3>Commentaires</h3></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th>ID</th><th>Utilisateur</th><th>Leçon</th><th>Contenu</th><th>Date</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach ($comments as $c): ?>
                    <tr>
                        <td><?= $c['id'] ?></td>
                        <td><?= h($c['full_name']) ?></td>
                        <td><?= h($c['lesson_title']) ?></td>
                        <td><?= h(substr($c['content'], 0, 80)) ?></td>
                        <td><?= time_ago($c['created_at']) ?></td>
                        <td class="actions">
                            <a href="<?= Router::url('/admin/comments/delete/' . $c['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ce commentaire ?')">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
