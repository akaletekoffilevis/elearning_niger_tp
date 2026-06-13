<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - eLearning Niger</title>
    <link rel="stylesheet" href="<?= Router::url('/css/app.css') ?>">
</head>
<body>
    <div class="admin-layout">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="<?= Router::url('/admin') ?>" class="sidebar-brand">Admin</a>
                <button class="sidebar-close" id="sidebarClose">&times;</button>
            </div>
            <nav class="sidebar-nav">
                <a href="<?= Router::url('/admin') ?>">Tableau de bord</a>
                <a href="<?= Router::url('/admin/courses') ?>">Cours</a>
                <a href="<?= Router::url('/admin/categories') ?>">Catégories</a>
                <a href="<?= Router::url('/admin/users') ?>">Utilisateurs</a>
                <a href="<?= Router::url('/admin/comments') ?>">Commentaires</a>
                <a href="<?= Router::url('/admin/notifications') ?>">Notifications</a>
                <a href="<?= Router::url('/admin/certificates') ?>">Certificats</a>
                <hr style="margin:0.5rem 1rem;border-color:var(--border);">
                <a href="<?= Router::url('/admin/export/users') ?>">Export CSV Utilisateurs</a>
                <a href="<?= Router::url('/admin/export/courses') ?>">Export CSV Cours</a>
                <a href="<?= Router::url('/admin/export/enrollments') ?>">Export CSV Inscriptions</a>
                <hr style="margin:0.5rem 1rem;border-color:var(--border);">
                <a href="<?= Router::url('/') ?>">Retour au site</a>
                <a href="<?= Router::url('/logout') ?>">Déconnexion</a>
            </nav>
        </aside>

        <div class="admin-main">
            <header class="admin-topbar">
                <button class="hamburger" id="adminHamburger" aria-label="Menu">
                    <span></span><span></span><span></span>
                </button>
                <h2><?= h($title ?? 'Administration') ?></h2>
            </header>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success" id="alert"><?= h($_SESSION['success']) ?><?php unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error" id="alert"><?= h($_SESSION['error']) ?><?php unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <div class="admin-content">
                <?= $content ?>
            </div>
        </div>
    </div>

    <script src="<?= Router::url('/js/app.js') ?>"></script>
</body>
</html>
