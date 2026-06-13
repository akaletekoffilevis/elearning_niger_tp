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
        <h1>Résultats du Quiz</h1>
        <p>Leçon: <?= h($lesson['title']) ?></p>

        <div class="quiz-result <?= $attempt['passed'] ? 'passed' : 'failed' ?>" style="text-align:center;padding:1.5rem;">
            <div style="font-size:3rem;font-weight:700;"><?= h($attempt['score']) ?>/<?= h($attempt['total']) ?></div>
            <div style="font-size:1.25rem;"><?= h(round(($attempt['score']/$attempt['total'])*100)) ?>%</div>
            <div class="progress-bar" style="max-width:300px;margin:1rem auto;">
                <div class="progress-fill" style="width:<?= h(round(($attempt['score']/$attempt['total'])*100)) ?>%"></div>
            </div>
            <div style="font-size:1.5rem;font-weight:600;">
                <?= $attempt['passed'] ? 'Réussi !' : 'Échoué' ?>
            </div>
        </div>

        <h2>Détail des questions</h2>
        <?php foreach ($questions as $q): ?>
            <?php $ans = $answers[$q['id']] ?? null; ?>
            <div class="quiz-question" style="border:1px solid var(--border);border-radius:var(--radius);padding:1rem;margin-bottom:1rem;background:var(--bg-card);">
                <p><strong>Question: <?= h($q['question']) ?></strong></p>
                <?php foreach ($q['options'] as $opt): ?>
                    <?php
                        $isSelected = $ans && (int)$ans['option_id'] === (int)$opt['id'];
                        $isCorrect = (bool)$opt['is_correct'];
                        $class = $isCorrect ? 'correct' : ($isSelected ? 'wrong' : '');
                    ?>
                    <div class="quiz-option <?= $class ?>"
                         style="<?= $isCorrect ? 'border-color:var(--success);background:rgba(16,185,129,0.1);' : ($isSelected && !$isCorrect ? 'border-color:var(--danger);background:rgba(239,68,68,0.1);' : '') ?>">
                        <?php if ($isCorrect): ?><span style="color:var(--success);margin-right:0.5rem;">✓</span>
                        <?php elseif ($isSelected): ?><span style="color:var(--danger);margin-right:0.5rem;">✗</span>
                        <?php else: ?><span style="margin-right:0.5rem;">&nbsp;</span><?php endif; ?>
                        <?= h($opt['text']) ?>
                        <?php if ($isCorrect): ?><span style="float:right;color:var(--success);font-size:0.75rem;">Bonne réponse</span><?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>

        <div class="lesson-buttons">
            <a href="<?= Router::url('/courses/' . $course['slug'] . '/lessons/' . $lesson['id']) ?>" class="btn btn-outline">&larr; Retour à la leçon</a>
            <?php
                $nextLesson = null;
                $currentIndex = array_search($lesson['id'], array_column($allLessons, 'id'));
                if ($currentIndex !== false && $currentIndex < count($allLessons) - 1) {
                    $nextLesson = $allLessons[$currentIndex + 1];
                }
            ?>
            <?php if ($nextLesson): ?>
                <a href="<?= Router::url('/courses/' . $course['slug'] . '/lessons/' . $nextLesson['id']) ?>" class="btn btn-primary">Suivant &rarr;</a>
            <?php endif; ?>
        </div>
    </div>
</div>
