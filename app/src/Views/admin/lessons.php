<div class="card">
    <div class="card-header">
        <h3>Leçons du module : <?= h($module['title']) ?></h3>
        <a href="<?= Router::url('/admin/modules/' . $module['id'] . '/lessons/create') ?>" class="btn btn-primary btn-sm">Ajouter une leçon</a>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Titre</th>
                    <th>Durée</th>
                    <th>Ordre</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($lessons)): ?>
                    <tr><td colspan="5" class="text-muted">Aucune leçon.</td></tr>
                <?php else: ?>
                    <?php foreach ($lessons as $l): ?>
                        <tr>
                            <td><?= h($l['id']) ?></td>
                            <td><?= h($l['title']) ?></td>
                            <td><?= $l['duration'] ? h($l['duration']) . ' min' : '-' ?></td>
                            <td><?= h($l['sort_order']) ?></td>
                            <td class="actions">
                                <a href="<?= Router::url('/admin/lessons/edit/' . $l['id']) ?>" class="btn btn-sm btn-outline">Modifier</a>
                                <a href="<?= Router::url('/admin/lessons/delete/' . $l['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ?')">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <a href="<?= Router::url('/admin/courses/edit/' . $module['course_id']) ?>" class="btn btn-outline">&larr; Retour au cours</a>
    </div>
</div>
