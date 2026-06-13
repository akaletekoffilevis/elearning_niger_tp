<div class="stats-grid">
    <div class="stat-card">
        <span class="stat-number"><?= h($stats['users']) ?></span>
        <span class="stat-label">Utilisateurs</span>
    </div>
    <div class="stat-card">
        <span class="stat-number"><?= h($stats['courses']) ?></span>
        <span class="stat-label">Cours total</span>
    </div>
    <div class="stat-card">
        <span class="stat-number"><?= h($stats['published']) ?></span>
        <span class="stat-label">Publiés</span>
    </div>
    <div class="stat-card">
        <span class="stat-number"><?= h($stats['enrollments']) ?></span>
        <span class="stat-label">Inscriptions</span>
    </div>
</div>

<div class="admin-grid">
    <div class="card">
        <div class="card-header">
            <h3>Derniers utilisateurs</h3>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentUsers as $u): ?>
                        <tr>
                            <td><?= h($u['full_name']) ?></td>
                            <td><?= h($u['email']) ?></td>
                            <td><?= h($u['role']) ?></td>
                            <td><?= h(date('d/m/Y', strtotime($u['created_at']))) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>Dernières inscriptions</h3>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Étudiant</th>
                        <th>Cours</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentEnrollments as $e): ?>
                        <tr>
                            <td><?= h($e['full_name']) ?></td>
                            <td><?= h($e['course_title']) ?></td>
                            <td><?= h(date('d/m/Y', strtotime($e['enrolled_at']))) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
