<div class="container">
    <h1>Tableau de bord</h1>
    <p>Bienvenue, <?= h($user['full_name']) ?></p>

    <?php if (empty($enrollments)): ?>
        <div class="empty-state">
            <p>Vous n'êtes inscrit à aucun cours.</p>
            <a href="<?= Router::url('/courses') ?>" class="btn btn-primary">Parcourir les cours</a>
        </div>
    <?php else: ?>
        <div class="courses-grid">
            <?php foreach ($enrollments as $enrollment): ?>
                <div class="card course-card">
                    <div class="card-body">
                        <h3><?= h($enrollment['title']) ?></h3>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?= progress_percent($enrollment['completed_lessons'], $enrollment['total_lessons']) ?>%"></div>
                        </div>
                        <p class="progress-text">
                            <?= h($enrollment['completed_lessons']) ?>/<?= h($enrollment['total_lessons']) ?> leçons
                        </p>
                        <a href="<?= Router::url('/courses/' . $enrollment['slug']) ?>" class="btn btn-primary btn-sm">Continuer</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
