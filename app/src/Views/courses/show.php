<div class="container">
    <div class="course-detail">
        <div class="course-header">
            <h1><?= h($course['title']) ?></h1>
            <div class="course-meta">
                <span>Catégorie : <?= h($course['category_name'] ?? 'Non catégorisé') ?></span>
                <span>Instructeur : <?= h($course['instructor_name']) ?></span>
                <span><?= h($studentCount) ?> étudiant(s) inscrit(s)</span>
            </div>
            <p><?= h($course['description']) ?></p>

            <?php if ($isEnrolled): ?>
                <a href="<?= Router::url('/courses/' . $course['slug'] . '/lessons/' . ($lessons[0]['id'] ?? '')) ?>" class="btn btn-primary">Continuer le cours</a>
            <?php else: ?>
                <form action="<?= Router::url('/courses/' . $course['slug'] . '/enroll') ?>" method="POST">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-primary btn-lg">S'inscrire au cours</button>
                </form>
            <?php endif; ?>
        </div>

        <div class="course-content">
            <h2>Contenu du cours</h2>
            <?php if (empty($modules)): ?>
                <p class="text-muted">Aucun module pour le moment.</p>
            <?php else: ?>
                <div class="accordion">
                    <?php foreach ($modules as $module): ?>
                        <?php $moduleLessons = Lesson::findByModule($module['id']); ?>
                        <div class="accordion-item">
                            <button class="accordion-header">
                                <?= h($module['title']) ?>
                                <span><?= count($moduleLessons) ?> leçon(s)</span>
                            </button>
                            <div class="accordion-body">
                                <?php if (empty($moduleLessons)): ?>
                                    <p class="text-muted">Aucune leçon.</p>
                                <?php else: ?>
                                    <ul class="lesson-list">
                                        <?php foreach ($moduleLessons as $lesson): ?>
                                            <li>
                                                <?php if ($isEnrolled): ?>
                                                    <a href="<?= Router::url('/courses/' . $course['slug'] . '/lessons/' . $lesson['id']) ?>">
                                                        <?= h($lesson['title']) ?>
                                                    </a>
                                                <?php else: ?>
                                                    <span><?= h($lesson['title']) ?></span>
                                                <?php endif; ?>
                                                <?php if ($lesson['duration']): ?>
                                                    <span class="lesson-duration"><?= h($lesson['duration']) ?> min</span>
                                                <?php endif; ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
