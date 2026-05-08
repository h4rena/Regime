<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriPlan - Inscription</title>
  <link rel="stylesheet" href="<?= base_url('css/style.css') ?>" />
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet" />
</head>
<body class="auth-page">
  <?php
  $old = isset($old) && is_array($old) ? $old : [];
  $errors = isset($errors) && is_array($errors) ? $errors : [];
  $selectedGenreId = (string) ($old['genre_id'] ?? '');
  ?>
  <div class="auth-split">
    <div class="auth-left">
      <a href="<?= base_url('/') ?>" class="auth-logo">
        <span class="logo-dot"></span> NutriPlan
      </a>
      <div class="auth-left-content">
        <h2 class="auth-left-title">Inscription<br /><em>etape 1.</em></h2>
        <p class="auth-left-desc">Renseignez vos informations personnelles puis passez a la page sante.</p>
      </div>
    </div>

    <div class="auth-right">
      <div class="auth-form-card">
        <div class="auth-steps">
          <div class="step-item active">
            <div class="step-circle">1</div>
            <span>Informations</span>
          </div>
          <div class="step-line"></div>
          <div class="step-item">
            <div class="step-circle">2</div>
            <span>Sante</span>
          </div>
        </div>

        <h1 class="auth-form-title">Inscription</h1>
        <p class="auth-form-sub">Nom, prenom, email, genre, date de naissance et mot de passe.</p>

        <form class="auth-form" method="POST" action="<?= base_url('/inscription') ?>" data-ajax-form="register-step1">
          <?= csrf_field() ?>

          <div class="form-group">
            <label class="form-label" for="nom">Nom</label>
            <input id="nom" type="text" name="nom" class="form-input" data-field="nom" value="<?= esc((string) ($old['nom'] ?? '')) ?>" required />
            <small class="field-error" data-error-for="nom"><?= esc((string) ($errors['nom'] ?? '')) ?></small>
          </div>

          <div class="form-group">
            <label class="form-label" for="prenom">Prenom</label>
            <input id="prenom" type="text" name="prenom" class="form-input" data-field="prenom" value="<?= esc((string) ($old['prenom'] ?? '')) ?>" required />
            <small class="field-error" data-error-for="prenom"><?= esc((string) ($errors['prenom'] ?? '')) ?></small>
          </div>

          <div class="form-group">
            <label class="form-label" for="email">Email</label>
            <input id="email" type="email" name="email" class="form-input" data-field="email" value="<?= esc((string) ($old['email'] ?? '')) ?>" required />
            <small class="field-error" data-error-for="email"><?= esc((string) ($errors['email'] ?? '')) ?></small>
          </div>

          <div class="form-group">
            <label class="form-label" for="date_naissance">Date de naissance</label>
            <input id="date_naissance" type="date" name="date_naissance" class="form-input" data-field="date_naissance" value="<?= esc((string) ($old['date_naissance'] ?? '')) ?>" required />
            <small class="field-error" data-error-for="date_naissance"><?= esc((string) ($errors['date_naissance'] ?? '')) ?></small>
          </div>

          <div class="form-group">
            <label class="form-label">Genre</label>
            <div class="checkbox-grid" data-single-check-group>
              <?php foreach (($genres ?? []) as $genre): ?>
                <?php $genreId = (string) ($genre['id'] ?? ''); ?>
                <?php $checked = $selectedGenreId === $genreId ? 'checked' : ''; ?>
                <label class="checkbox-card" for="genre-<?= esc($genreId) ?>">
                  <input id="genre-<?= esc($genreId) ?>" type="radio" name="genre_id" value="<?= esc($genreId) ?>" <?= $checked ?> data-field="genre_id" />
                  <span><?= esc((string) ($genre['nom'] ?? '')) ?></span>
                </label>
              <?php endforeach; ?>
            </div>
            <small class="field-error" data-error-for="genre_id"><?= esc((string) ($errors['genre_id'] ?? '')) ?></small>
          </div>

          <div class="form-group">
            <label class="form-label" for="password">Mot de passe</label>
            <div class="password-wrap">
              <input id="password" type="password" name="password" class="form-input" data-field="password" required />
              <button type="button" class="toggle-password" data-toggle-password>Voir</button>
            </div>
            <small class="field-error" data-error-for="password"><?= esc((string) ($errors['password'] ?? '')) ?></small>
          </div>

          <div class="form-group">
            <label class="form-label" for="password_confirmation">Confirmer le mot de passe</label>
            <div class="password-wrap">
              <input id="password_confirmation" type="password" name="password_confirmation" class="form-input" data-field="password_confirmation" required />
              <button type="button" class="toggle-password" data-toggle-password>Voir</button>
            </div>
            <small class="field-error" data-error-for="password_confirmation"><?= esc((string) ($errors['password_confirmation'] ?? '')) ?></small>
          </div>

          <button type="submit" class="btn-primary-full" data-submit-btn>Suivant: Sante</button>
        </form>

        <p class="auth-switch">Deja un compte ? <a href="<?= base_url('/login') ?>">Se connecter</a></p>
      </div>
    </div>
  </div>

  <script src="<?= base_url('js/auth.js') ?>"></script>
</body>
</html>
