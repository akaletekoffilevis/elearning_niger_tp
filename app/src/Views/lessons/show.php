<div class="container lesson-layout">
    <aside class="lesson-sidebar">
        <h3>Progression</h3>
        <div class="progress-bar">
            <div class="progress-fill" style="width: <?= progress_percent(count(array_filter($allLessons, fn($l) => Enrollment::isLessonCompleted($_SESSION['user_id'], $l['id']))), count($allLessons)) ?>%"></div>
        </div>
        <p class="progress-text">
            <?= count(array_filter($allLessons, fn($l) => Enrollment::isLessonCompleted($_SESSION['user_id'], $l['id']))) ?>/<?= count($allLessons) ?> leçons
        </p>
        <nav class="lesson-nav">
            <?php foreach ($allLessons as $l): ?>
                <a href="<?= Router::url('/courses/' . $course['slug'] . '/lessons/' . $l['id']) ?>"
                   class="lesson-nav-item <?= $l['id'] == $lesson['id'] ? 'active' : '' ?>
                          <?= Enrollment::isLessonCompleted($_SESSION['user_id'], $l['id']) ? 'completed' : '' ?>">
                    <?= h($l['title']) ?>
                </a>
            <?php endforeach; ?>
        </nav>
    </aside>

    <div class="lesson-content">
        <h1><?= h($lesson['title']) ?></h1>
        <p class="lesson-meta">Module: <?= h($lesson['module_title']) ?></p>

        <?php if ($lesson['video_url']): ?>
            <div class="video-container">
                <iframe src="<?= h($lesson['video_url']) ?>" frameborder="0" allowfullscreen></iframe>
            </div>
        <?php endif; ?>

        <div class="lesson-text">
            <?= nl2br(h($lesson['content'])) ?>
        </div>

        <?php if ($quiz && !empty($questions)): ?>
            <div class="quiz-section">
                <h2>Quiz : <?= h($quiz['title']) ?></h2>
                <?php if ($userScore): ?>
                    <div class="quiz-result <?= $userScore['passed'] ? 'passed' : 'failed' ?>">
                        Score : <?= h($userScore['score']) ?>/<?= h($userScore['total']) ?>
                        (<?= h($userScore['passed'] ? 'Réussi' : 'Échoué') ?>)
                    </div>
                <?php endif; ?>
                <form action="<?= Router::url('/courses/' . $course['slug'] . '/lessons/' . $lesson['id'] . '/quiz') ?>" method="POST" class="quiz-form">
                    <?= csrf_field() ?>
                    <?php foreach ($questions as $q): ?>
                        <div class="quiz-question">
                            <p><strong><?= h($q['question']) ?></strong></p>
                            <?php foreach ($q['options'] as $opt): ?>
                                <label class="quiz-option">
                                    <input type="radio" name="question_<?= $q['id'] ?>" value="<?= $opt['id'] ?>">
                                    <?= h($opt['text']) ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                    <button type="submit" class="btn btn-primary quiz-submit">Soumettre le quiz</button>
                </form>
            </div>
        <?php endif; ?>

        <div class="lesson-buttons">
            <?php if ($prevLesson): ?>
                <a href="<?= Router::url('/courses/' . $course['slug'] . '/lessons/' . $prevLesson['id']) ?>" class="btn btn-outline">&larr; Précédent</a>
            <?php endif; ?>
            <?php if ($nextLesson): ?>
                <a href="<?= Router::url('/courses/' . $course['slug'] . '/lessons/' . $nextLesson['id']) ?>" class="btn btn-primary">Suivant &rarr;</a>
            <?php endif; ?>
        </div>
    </div>
</div>
