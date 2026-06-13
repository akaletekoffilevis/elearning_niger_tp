<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eLearning Niger</title>
    <link rel="stylesheet" href="<?= Router::url('/css/app.css') ?>">
</head>
<body class="<?= isset($_SESSION['theme']) ? 'theme-' . $_SESSION['theme'] : '' ?>">
    <nav class="navbar">
        <div class="container">
            <a href="<?= Router::url('/') ?>" class="navbar-brand">eLearning Niger</a>
            <button class="hamburger" id="hamburger" aria-label="Menu">
                <span></span><span></span><span></span>
            </button>
            <div class="navbar-menu" id="navbarMenu">
                <div class="navbar-links">
                    <a href="<?= Router::url('/courses') ?>">Cours</a>
                    <?php if (Auth::check()): ?>
                        <?php $currentUser = Auth::user(); ?>
                        <a href="<?= Router::url('/dashboard') ?>">Tableau de bord</a>
                        <?php if (Auth::isAdmin()): ?>
                            <a href="<?= Router::url('/admin') ?>">Administration</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <div class="navbar-auth">
                    <?php if (Auth::check()): ?>
                        <?php $notifCount = Notification::unreadCount((int) $_SESSION['user_id']); ?>
                        <a href="<?= Router::url('/notifications') ?>" class="notif-bell" style="position:relative;font-size:1.25rem;">
                            🔔
                            <?php if ($notifCount > 0): ?>
                                <span class="notif-badge"><?= $notifCount > 9 ? '9+' : $notifCount ?></span>
                            <?php endif; ?>
                        </a>
                        <a href="<?= Router::url('/profile') ?>" style="display:flex;align-items:center;gap:0.5rem;color:var(--text);text-decoration:none;">
                            <?php if (!empty($currentUser['avatar'])): ?>
                                <img src="<?= h($currentUser['avatar']) ?>" alt="" style="width:28px;height:28px;border-radius:50%;object-fit:cover;">
                            <?php endif; ?>
                            <span class="navbar-user"><?= h($currentUser['full_name']) ?></span>
                        </a>
                        <a href="<?= Router::url('/logout') ?>" class="btn btn-sm btn-outline">Déconnexion</a>
                    <?php else: ?>
                        <a href="<?= Router::url('/login') ?>" class="btn btn-sm btn-outline">Connexion</a>
                        <a href="<?= Router::url('/register') ?>" class="btn btn-sm btn-primary">Inscription</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success" id="alert"><?= h($_SESSION['success']) ?><?php unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error" id="alert"><?= h($_SESSION['error']) ?><?php unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <main class="main-content">
        <?= $content ?>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; <?= date('Y') ?> eLearning Niger. Tous droits réservés.</p>
        </div>
    </footer>

    <script src="<?= Router::url('/js/app.js') ?>"></script>
</body>
</html>
