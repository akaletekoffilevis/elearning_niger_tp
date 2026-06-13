<div class="container">
    <div class="auth-card">
        <h1>Connexion</h1>
        <form action="<?= Router::url('/login') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Se connecter</button>
        </form>
        <p class="auth-link">Pas encore inscrit ? <a href="<?= Router::url('/register') ?>">Créer un compte</a></p>
    </div>
</div>
