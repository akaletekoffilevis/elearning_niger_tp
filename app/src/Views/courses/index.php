<div class="container">
    <h1>Nos cours</h1>

    <form class="search-bar" method="GET" action="<?= Router::url('/courses') ?>">
        <input type="text" name="search" class="form-control" placeholder="Rechercher un cours..." value="<?= h($search) ?>">
        <select name="category" class="form-control">
            <option value="">Toutes les catégories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= h($cat['slug']) ?>" <?= $selectedCategory === $cat['slug'] ? 'selected' : '' ?>>
                    <?= h($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary">Filtrer</button>
    </form>

    <?php if (empty($courses)): ?>
        <p class="text-muted">Aucun cours trouvé.</p>
    <?php else: ?>
        <div class="courses-grid">
            <?php foreach ($courses as $course): ?>
                <div class="card course-card">
                    <div class="card-body">
                        <span class="card-badge"><?= h($course['category_name'] ?? 'Non catégorisé') ?></span>
                        <h3><?= h($course['title']) ?></h3>
                        <p><?= h(substr($course['description'] ?? '', 0, 150)) ?></p>
                        <div class="card-meta">
                            <span>Par <?= h($course['instructor_name']) ?></span>
                        </div>
                        <a href="<?= Router::url('/courses/' . $course['slug']) ?>" class="btn btn-primary btn-sm">Voir le cours</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
