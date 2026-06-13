<div class="card">
    <div class="card-header">
        <h3>Catégories</h3>
    </div>
    <div class="card-body">
        <form action="<?= Router::url('/admin/categories/save') ?>" method="POST" class="form-inline">
            <?= csrf_field() ?>
            <input type="text" name="name" class="form-control" placeholder="Nom" required>
            <input type="text" name="slug" class="form-control" placeholder="Slug">
            <input type="text" name="description" class="form-control" placeholder="Description">
            <button type="submit" class="btn btn-primary btn-sm">Ajouter</button>
        </form>

        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Slug</th>
                    <th>Cours</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><?= h($cat['id']) ?></td>
                        <td><?= h($cat['name']) ?></td>
                        <td><?= h($cat['slug']) ?></td>
                        <td><?= h($cat['course_count']) ?></td>
                        <td class="actions">
                            <a href="<?= Router::url('/admin/categories/delete/' . $cat['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ?')">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
