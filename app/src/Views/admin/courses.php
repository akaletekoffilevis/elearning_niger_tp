<div class="card">
    <div class="card-header">
        <h3>Cours</h3>
        <a href="<?= Router::url('/admin/courses/create') ?>" class="btn btn-primary btn-sm">Ajouter un cours</a>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Titre</th>
                    <th>Catégorie</th>
                    <th>Instructeur</th>
                    <th>Statut</th>
                    <th>Étudiants</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($courses as $c): ?>
                    <tr>
                        <td><?= h($c['id']) ?></td>
                        <td><?= h($c['title']) ?></td>
                        <td><?= h($c['category_name'] ?? '-') ?></td>
                        <td><?= h($c['instructor_name']) ?></td>
                        <td><span class="badge badge-<?= $c['status'] ?>"><?= h($c['status']) ?></span></td>
                        <td><?= h($c['student_count']) ?></td>
                        <td class="actions">
                            <a href="<?= Router::url('/admin/courses/edit/' . $c['id']) ?>" class="btn btn-sm btn-outline">Modifier</a>
                            <a href="<?= Router::url('/admin/courses/delete/' . $c['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ce cours ?')">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
