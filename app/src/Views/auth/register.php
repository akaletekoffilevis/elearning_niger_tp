<div class="container">
    <div class="auth-card">
        <h1>Inscription</h1>
        <form action="<?= Router::url('/register') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="full_name">Nom complet</label>
                <input type="text" name="full_name" id="full_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="username">Nom d'utilisateur</label>
                <input type="text" name="username" id="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" name="password" id="password" class="form-control" required minlength="6">
            </div>
            <button type="submit" class="btn btn-primary btn-block">S'inscrire</button>
        </form>
        <p class="auth-link">Déjà inscrit ? <a href="<?= Router::url('/login') ?>">Se connecter</a></p>
    </div>
</div>
