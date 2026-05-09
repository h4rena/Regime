<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriPlan — Connexion</title>
  <link rel="stylesheet" href="<?= base_url('css/style.css') ?>" />
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet" />
</head>
<body class="auth-page">
  <?php
  $errors = isset($errors) && is_array($errors) ? $errors : [];
  $successMessage = session()->getFlashdata('success');
  ?>

  <div class="auth-split">
    <div class="auth-left">
      <a href="<?= base_url('/') ?>" class="auth-logo">
        <span class="logo-dot"></span> NutriPlan
      </a>
      <div class="auth-left-content">
        <h2 class="auth-left-title">Bon retour<br /><em>parmi nous.</em></h2>
        <p class="auth-left-desc">Connectez-vous pour accéder à votre programme personnalisé et suivre vos progrès.</p>
      </div>
    </div>

    <div class="auth-right">
      <div class="auth-form-card">
        <h1 class="auth-form-title">Connexion</h1>
        <p class="auth-form-sub">Accédez à votre espace NutriPlan</p>

        <?php if ($successMessage): ?>
          <div class="alert alert-success"><?= esc((string) $successMessage) ?></div>
        <?php endif; ?>

        <form class="auth-form" method="POST" action="<?= base_url('/login') ?>" data-ajax-form="login">
          <?= csrf_field() ?>
          <div class="form-group">
            <label class="form-label">Adresse e-mail</label>
            <input type="email" name="email" class="form-input" placeholder="ravo@email.com" required data-field="email" />
            <small class="field-error" data-error-for="email"><?= esc((string) ($errors['email'] ?? '')) ?></small>
          </div>
          <div class="form-group">
            <label class="form-label">Mot de passe</label>
            <div class="password-wrap">
              <input type="password" name="password" class="form-input" placeholder="********" required data-field="password" />
              <button type="button" class="toggle-password" data-toggle-password>Voir</button>
            </div>
            <small class="field-error" data-error-for="password"><?= esc((string) ($errors['password'] ?? '')) ?></small>
          </div>
          <button type="submit" class="btn-primary-full" data-submit-btn>Se connecter</button>
        </form>

        <p class="auth-switch">Pas encore de compte ? <a href="<?= base_url('/inscription') ?>">S'inscrire</a></p>

        <div class="admin-link-sep">
          <span>Administration</span>
        </div>
        <a href="<?= base_url('/admin') ?>" class="btn-outline-full">Accès back office →</a>
      </div>
    </div>
  </div>

  <script src="<?= base_url('js/auth.js') ?>"></script>

</body>
</html>