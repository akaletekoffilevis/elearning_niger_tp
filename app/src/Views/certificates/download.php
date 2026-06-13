<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificat - eLearning Niger</title>
    <link rel="stylesheet" href="<?= Router::url('/css/app.css') ?>">
    <style>
        @media print { body { -webkit-print-color-adjust: exact; print-color-adjust: exact; } }
        body { background: var(--bg); display:flex;align-items:center;justify-content:center;min-height:100vh;flex-direction:column; }
        .certificate {
            max-width:800px;width:100%;margin:2rem auto;
            background: var(--bg-card);border: 3px solid var(--primary);
            border-radius: 12px;padding: 3rem;text-align:center;
            position:relative;
        }
        .certificate::before {
            content: '';position:absolute;top:10px;left:10px;right:10px;bottom:10px;
            border: 1px solid var(--primary);border-radius: 8px;pointer-events:none;
        }
        .cert-logo { font-size:2rem;font-weight:700;color:var(--primary);margin-bottom:1rem; }
        .cert-title { font-size:1.5rem;text-transform:uppercase;letter-spacing:0.1em;color:var(--text-muted);margin-bottom:2rem; }
        .cert-name { font-size:2rem;font-weight:700;margin-bottom:0.5rem; }
        .cert-course { font-size:1.25rem;color:var(--primary);margin-bottom:2rem; }
        .cert-code { font-size:0.875rem;color:var(--text-muted);margin-bottom:2rem; }
        .cert-date { font-size:0.875rem;color:var(--text-muted); }
        .cert-actions { margin-top:2rem;display:flex;gap:1rem;justify-content:center; }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="cert-logo">eLearning Niger</div>
        <div class="cert-title">Certificat de Réussite</div>
        <div class="cert-name"><?= h($user['full_name']) ?></div>
        <p style="color:var(--text-muted);margin-bottom:1rem;">A terminé avec succès le cours</p>
        <div class="cert-course"><?= h($course['title']) ?></div>
        <div class="cert-code">Code: <?= h($cert['certificate_code']) ?></div>
        <div class="cert-date">Délivré le <?= h(date('d F Y', strtotime($cert['issued_at']))) ?></div>
    </div>
    <div class="cert-actions">
        <button onclick="window.print()" class="btn btn-primary">Imprimer</button>
        <a href="<?= Router::url('/dashboard') ?>" class="btn btn-outline">Retour</a>
    </div>
</body>
</html>
