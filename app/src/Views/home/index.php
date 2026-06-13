<section class="hero">
    <div class="container">
        <h1>Apprenez à votre rythme</h1>
        <p>Des cours en ligne de qualité pour développer vos compétences</p>
        <a href="<?= Router::url('/courses') ?>" class="btn btn-primary btn-lg">Explorer les cours</a>
    </div>
</section>

<section class="stats-section">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <span class="stat-number"><?= h($stats['courses']) ?></span>
                <span class="stat-label">Cours</span>
            </div>
            <div class="stat-card">
                <span class="stat-number"><?= h($stats['students']) ?></span>
                <span class="stat-label">Étudiants</span>
            </div>
            <div class="stat-card">
                <span class="stat-number"><?= h($stats['categories']) ?></span>
                <span class="stat-label">Catégories</span>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <h2 class="section-title">Catégories</h2>
        <div class="categories-grid">
            <?php foreach ($categories as $cat): ?>
                <a href="<?= Router::url('/courses?category=' . $cat['slug']) ?>" class="category-card">
                    <h3><?= h($cat['name']) ?></h3>
                    <p><?= h($cat['description']) ?></p>
                    <span class="category-count"><?= h($cat['course_count']) ?> cours</span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <h2 class="section-title">Cours populaires</h2>
        <?php if (empty($courses)): ?>
            <p class="text-muted">Aucun cours disponible pour le moment.</p>
        <?php else: ?>
            <div class="courses-grid">
                <?php foreach ($courses as $course): ?>
                    <div class="card course-card">
                        <div class="card-body">
                            <span class="card-badge"><?= h($course['category_name'] ?? 'Non catégorisé') ?></span>
                            <h3><?= h($course['title']) ?></h3>
                            <p><?= h(substr($course['description'] ?? '', 0, 120)) ?></p>
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
</section>
