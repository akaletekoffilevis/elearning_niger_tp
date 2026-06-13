<div class="card">
    <div class="card-header">
        <h3><?= $course ? 'Modifier' : 'Créer' ?> un cours</h3>
    </div>
    <div class="card-body">
        <form action="<?= Router::url($course ? '/admin/courses/edit/' . $course['id'] : '/admin/courses/create') ?>" method="POST" class="form">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="title">Titre</label>
                <input type="text" name="title" id="title" class="form-control" value="<?= h($course['title'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="slug">Slug (laissez vide pour auto-génération)</label>
                <input type="text" name="slug" id="slug" class="form-control" value="<?= h($course['slug'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="category_id">Catégorie</label>
                <select name="category_id" id="category_id" class="form-control">
                    <option value="">Sélectionner</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($course['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                            <?= h($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" class="form-control" rows="4"><?= h($course['description'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label for="content">Contenu</label>
                <textarea name="content" id="content" class="form-control" rows="8"><?= h($course['content'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label for="thumbnail">URL de l'image</label>
                <input type="text" name="thumbnail" id="thumbnail" class="form-control" value="<?= h($course['thumbnail'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="status">Statut</label>
                <select name="status" id="status" class="form-control">
                    <option value="draft" <?= ($course['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Brouillon</option>
                    <option value="published" <?= ($course['status'] ?? '') === 'published' ? 'selected' : '' ?>>Publié</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Sauvegarder</button>
            <a href="<?= Router::url('/admin/courses') ?>" class="btn btn-outline">Annuler</a>
        </form>

        <?php if ($course): ?>
            <hr>
            <h3>Modules</h3>
            <?php $modules = Module::findByCourse($course['id']); ?>
            <?php if (empty($modules)): ?>
                <p class="text-muted">Aucun module.</p>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr><th>Titre</th><th>Ordre</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($modules as $m): ?>
                            <tr>
                                <td><?= h($m['title']) ?></td>
                                <td><?= h($m['sort_order']) ?></td>
                                <td class="actions">
                                    <a href="<?= Router::url('/admin/modules/' . $m['id'] . '/lessons') ?>" class="btn btn-sm btn-outline">Leçons</a>
                                    <a href="<?= Router::url('/admin/modules/delete/' . $m['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ce module ?')">Supprimer</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
            <form action="<?= Router::url('/admin/courses/' . $course['id'] . '/modules') ?>" method="POST" class="form-inline">
                <?= csrf_field() ?>
                <input type="text" name="title" class="form-control" placeholder="Titre du module" required>
                <input type="number" name="sort_order" class="form-control" placeholder="Ordre" value="0">
                <button type="submit" class="btn btn-primary btn-sm">Ajouter</button>
            </form>
        <?php endif; ?>
    </div>
</div>
