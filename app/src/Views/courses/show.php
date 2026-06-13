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
                <a href="<?= Router::url('/courses/' . $course['slug'] . '/lessons/' . (!empty($lessons) ? ($lessons[0]['id'] ?? '') : '')) ?>" class="btn btn-primary">Continuer le cours</a>
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

        <?php
        $reviews = Review::findByCourse($course['id']);
        $avgRating = Review::getAverage($course['id']);
        $ratingCount = Review::count($course['id']);
        $userReview = Auth::check() ? Review::findByUserCourse((int) $_SESSION['user_id'], $course['id']) : null;
        ?>

        <div class="course-reviews" style="margin-top:2rem;">
            <h2>Avis des étudiants</h2>

            <?php if ($ratingCount > 0): ?>
                <div class="rating-summary" style="display:flex;align-items:center;gap:1rem;margin:1rem 0;">
                    <div style="font-size:2.5rem;font-weight:700;color:var(--primary);"><?= number_format($avgRating, 1) ?></div>
                    <div>
                        <div class="stars" style="font-size:1.5rem;">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <span style="color:<?= $i <= round($avgRating) ? 'var(--primary)' : 'var(--border)' ?>">★</span>
                            <?php endfor; ?>
                        </div>
                        <small style="color:var(--text-muted);"><?= $ratingCount ?> avis</small>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (Auth::check() && $isEnrolled): ?>
                <div class="card" style="margin-bottom:1rem;">
                    <div class="card-body">
                        <h3><?= $userReview ? 'Modifier votre avis' : 'Donner votre avis' ?></h3>
                        <form action="<?= Router::url('/courses/' . $course['slug'] . '/review') ?>" method="POST">
                            <?= csrf_field() ?>
                            <div class="form-group">
                                <label>Note</label>
                                <div class="star-rating" style="font-size:2rem;">
                                    <?php for ($i = 5; $i >= 1; $i--): ?>
                                        <input type="radio" name="rating" id="star<?= $i ?>" value="<?= $i ?>" <?= ($userReview['rating'] ?? 0) == $i ? 'checked' : '' ?> style="display:none;">
                                        <label for="star<?= $i ?>" style="cursor:pointer;color:var(--border);" onclick="document.getElementById('star<?= $i ?>').checked=true;this.parentElement.querySelectorAll('label').forEach(function(l){l.style.color='var(--border)'});for(var s=5;s>=<?= $i ?>;s--){document.querySelector('label[for=star'+s+']').style.color='var(--primary)'}">★</label>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="review_comment">Commentaire (optionnel)</label>
                                <textarea name="comment" id="review_comment" class="form-control" rows="3"><?= h($userReview['comment'] ?? '') ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Envoyer</button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($reviews)): ?>
                <?php foreach ($reviews as $r): ?>
                    <div class="card" style="margin-bottom:0.75rem;">
                        <div class="card-body">
                            <div style="display:flex;justify-content:space-between;align-items:center;">
                                <strong><?= h($r['full_name']) ?></strong>
                                <div class="stars" style="color:var(--primary);">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <span style="color:<?= $i <= (int)$r['rating'] ? 'var(--primary)' : 'var(--border)' ?>">★</span>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <?php if ($r['comment']): ?>
                                <p style="margin-top:0.5rem;color:var(--text-muted);font-size:0.875rem;"><?= h($r['comment']) ?></p>
                            <?php endif; ?>
                            <small style="color:var(--text-muted);"><?= time_ago($r['created_at']) ?></small>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
