<div class="container">
    <h1>Mon Profil</h1>

    <div class="profile-layout" style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">
        <div class="card">
            <div class="card-header"><h3>Informations personnelles</h3></div>
            <div class="card-body">
                <form action="<?= Router::url('/profile') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label for="full_name">Nom complet</label>
                        <input type="text" name="full_name" id="full_name" class="form-control" value="<?= h($user['full_name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="bio">Bio</label>
                        <textarea name="bio" id="bio" class="form-control" rows="4"><?= h($user['bio'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="avatar">URL de l'avatar</label>
                        <input type="text" name="avatar" id="avatar" class="form-control" value="<?= h($user['avatar'] ?? '') ?>" placeholder="https://example.com/avatar.jpg">
                    </div>
                    <button type="submit" class="btn btn-primary">Sauvegarder</button>
                </form>
            </div>
        </div>

        <div>
            <div class="card" style="margin-bottom:1rem;">
                <div class="card-header"><h3>Statistiques</h3></div>
                <div class="card-body" style="text-align:center;">
                    <div class="stats-grid">
                        <div class="stat-card"><span class="stat-number"><?= count($enrollments) ?></span><span class="stat-label">Inscriptions</span></div>
                        <div class="stat-card"><span class="stat-number"><?= count($certificates) ?></span><span class="stat-label">Certificats</span></div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3>Thème</h3></div>
                <div class="card-body" style="text-align:center;">
                    <p>Thème actuel : <strong><?= h($user['theme'] ?? 'dark') ?></strong></p>
                    <a href="<?= Router::url('/profile/theme') ?>" class="btn btn-outline">
                        Changer en mode <?= ($user['theme'] ?? 'dark') === 'dark' ? 'clair' : 'sombre' ?>
                    </a>
                </div>
            </div>

            <?php if (!empty($certificates)): ?>
            <div class="card" style="margin-top:1rem;">
                <div class="card-header"><h3>Mes certificats</h3></div>
                <div class="card-body">
                    <?php foreach ($certificates as $cert): ?>
                        <div style="padding:0.5rem 0;border-bottom:1px solid var(--border);">
                            <strong><?= h($cert['course_title']) ?></strong><br>
                            <span style="color:var(--text-muted);font-size:0.875rem;">
                                Code: <?= h($cert['certificate_code']) ?> - <?= h(date('d/m/Y', strtotime($cert['issued_at']))) ?>
                            </span>
                            <a href="<?= Router::url('/certificate/' . $cert['course_id']) ?>" class="btn btn-sm btn-outline" style="float:right;">Voir</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
