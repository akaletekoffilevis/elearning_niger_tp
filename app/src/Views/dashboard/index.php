<div class="container">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
        <h1>Tableau de bord</h1>
        <a href="<?= Router::url('/profile') ?>" class="btn btn-outline btn-sm">Mon profil</a>
    </div>
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

    <?php $userCerts = Certificate::findByUser($user['id']); ?>
    <?php if (!empty($userCerts)): ?>
        <h2 style="margin-top:2rem;">Mes certificats</h2>
        <div class="courses-grid">
            <?php foreach ($userCerts as $cert): ?>
                <div class="card course-card">
                    <div class="card-body" style="text-align:center;">
                        <div style="font-size:3rem;margin-bottom:0.5rem;">🎓</div>
                        <h3><?= h($cert['course_title']) ?></h3>
                        <p style="font-size:0.8125rem;color:var(--text-muted);">Code: <?= h($cert['certificate_code']) ?></p>
                        <a href="<?= Router::url('/certificate/' . $cert['course_id']) ?>" class="btn btn-primary btn-sm">Voir le certificat</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
