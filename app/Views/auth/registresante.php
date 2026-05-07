<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriPlan - Sante</title>
  <link rel="stylesheet" href="<?= base_url('css/style.css') ?>" />
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet" />
</head>
<body class="auth-page">
  <div class="auth-split">
    <div class="auth-left">
      <a href="<?= base_url('/') ?>" class="auth-logo">
        <span class="logo-dot"></span> NutriPlan
      </a>
      <div class="auth-left-content">
        <h2 class="auth-left-title">Inscription<br /><em>etape 2.</em></h2>
        <p class="auth-left-desc">Ajoutez vos informations de sante pour calculer votre IMC automatiquement.</p>
      </div>
    </div>

    <div class="auth-right">
      <div class="auth-form-card">
        <div class="auth-steps">
          <div class="step-item done">
            <div class="step-circle">✓</div>
            <span>Informations</span>
          </div>
          <div class="step-line done"></div>
          <div class="step-item active">
            <div class="step-circle">2</div>
            <span>Sante</span>
          </div>
        </div>

        <h1 class="auth-form-title">Informations de sante</h1>
        <p class="auth-form-sub">Taille, poids et objectif pour finaliser votre compte.</p>

        <form class="auth-form" method="POST" action="<?= base_url('/inscription/sante') ?>">
          <?= csrf_field() ?>

          <div class="form-group">
            <label class="form-label" for="taille">Taille (cm)</label>
            <input id="taille" type="number" min="100" max="250" step="0.1" name="taille" class="form-input" value="<?= esc($old['taille'] ?? '') ?>" required />
            <small class="field-error"><?= esc($errors['taille'] ?? '') ?></small>
          </div>

          <div class="form-group">
            <label class="form-label" for="poids">Poids (kg)</label>
            <input id="poids" type="number" min="30" max="300" step="0.1" name="poids" class="form-input" value="<?= esc($old['poids'] ?? '') ?>" required />
            <small class="field-error"><?= esc($errors['poids'] ?? '') ?></small>
          </div>

          <button type="submit" class="btn-primary-full">Suivant: Objectif</button>
        </form>

        <p class="auth-switch">Retour a <a href="<?= base_url('/inscription') ?>">l'etape precedente</a></p>
      </div>
    </div>
  </div>
</body>
</html>
