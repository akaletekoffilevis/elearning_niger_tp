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

        <?php if (!$quiz): ?>
            <form action="<?= Router::url('/courses/' . $course['slug'] . '/lessons/' . $lesson['id'] . '/complete') ?>" method="POST" style="display:inline;">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-primary" <?= $isCompleted ? 'disabled' : '' ?>>
                    <?= $isCompleted ? 'Terminée ✓' : 'Marquer comme terminée' ?>
                </button>
            </form>
        <?php endif; ?>

        <?php $comments = Comment::all($lesson['id']); ?>
        <div class="comments-section" style="margin-top:2rem;padding-top:1.5rem;border-top:1px solid var(--border);">
            <h2>Commentaires (<?= count($comments) ?>)</h2>

            <?php if (Auth::check()): ?>
                <form action="<?= Router::url('/courses/' . $course['slug'] . '/lessons/' . $lesson['id'] . '/comments') ?>" method="POST" style="margin:1rem 0;">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <textarea name="content" class="form-control" rows="3" placeholder="Ajouter un commentaire..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">Commenter</button>
                </form>
            <?php endif; ?>

            <?php if (empty($comments)): ?>
                <p class="text-muted">Aucun commentaire. Soyez le premier !</p>
            <?php else: ?>
                <?php foreach ($comments as $c): ?>
                    <div class="comment" style="padding:1rem;background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius);margin-bottom:0.75rem;">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.5rem;">
                            <strong><?= h($c['full_name']) ?></strong>
                            <small style="color:var(--text-muted);"><?= time_ago($c['created_at']) ?></small>
                        </div>
                        <p style="font-size:0.875rem;"><?= h($c['content']) ?></p>
                        <?php if (Auth::isAdmin() || (int)$_SESSION['user_id'] === (int)$c['user_id']): ?>
                            <form action="<?= Router::url('/comments/delete/' . $c['id']) ?>" method="POST" style="margin-top:0.5rem;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="slug" value="<?= h($course['slug']) ?>">
                                <input type="hidden" name="lesson_id" value="<?= $lesson['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ce commentaire ?')">Supprimer</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
