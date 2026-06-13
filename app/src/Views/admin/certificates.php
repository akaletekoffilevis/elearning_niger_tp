<div class="card">
    <div class="card-header"><h3>Certificats délivrés</h3></div>
    <div class="card-body">
        <?php if (empty($certificates)): ?>
            <p class="text-muted">Aucun certificat délivré.</p>
        <?php else: ?>
            <table class="table">
                <thead><tr><th>ID</th><th>Étudiant</th><th>Email</th><th>Cours</th><th>Code</th><th>Date</th></tr></thead>
                <tbody>
                    <?php foreach ($certificates as $c): ?>
                        <tr>
                            <td><?= $c['id'] ?></td>
                            <td><?= h($c['full_name']) ?></td>
                            <td><?= h($c['email']) ?></td>
                            <td><?= h($c['course_title']) ?></td>
                            <td><code><?= h($c['certificate_code']) ?></code></td>
                            <td><?= h(date('d/m/Y', strtotime($c['issued_at']))) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
