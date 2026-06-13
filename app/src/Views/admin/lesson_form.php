<div class="card">
    <div class="card-header">
        <h3><?= $lesson ? 'Modifier' : 'Créer' ?> une leçon</h3>
    </div>
    <div class="card-body">
        <form action="<?= Router::url($lesson ? '/admin/lessons/edit/' . $lesson['id'] : '/admin/lessons/create') ?>" method="POST" class="form">
            <?= csrf_field() ?>
            <input type="hidden" name="module_id" value="<?= h($module['id']) ?>">
            <div class="form-group">
                <label for="title">Titre</label>
                <input type="text" name="title" id="title" class="form-control" value="<?= h($lesson['title'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="video_url">URL de la vidéo</label>
                <input type="text" name="video_url" id="video_url" class="form-control" value="<?= h($lesson['video_url'] ?? '') ?>" placeholder="https://www.youtube.com/embed/...">
            </div>
            <div class="form-group">
                <label for="duration">Durée (minutes)</label>
                <input type="number" name="duration" id="duration" class="form-control" value="<?= h($lesson['duration'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="sort_order">Ordre</label>
                <input type="number" name="sort_order" id="sort_order" class="form-control" value="<?= h($lesson['sort_order'] ?? '0') ?>">
            </div>
            <div class="form-group">
                <label for="content">Contenu</label>
                <textarea name="content" id="content" class="form-control" rows="12"><?= h($lesson['content'] ?? '') ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Sauvegarder</button>
            <a href="<?= Router::url('/admin/modules/' . $module['id'] . '/lessons') ?>" class="btn btn-outline">Annuler</a>
        </form>
    </div>
</div>
