<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriPlan - Objectif</title>
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
        <h2 class="auth-left-title">Choisissez<br /><em>votre objectif.</em></h2>
        <p class="auth-left-desc">Votre IMC ideal est affiche ci-dessous. Choisissez ensuite votre objectif pour terminer l'inscription.</p>
      </div>
    </div>

    <div class="auth-right">
      <div class="auth-form-card">
        <div class="auth-steps">
          <div class="step-item done"><div class="step-circle">✓</div><span>Informations</span></div>
          <div class="step-line done"></div>
          <div class="step-item done"><div class="step-circle">✓</div><span>Sante</span></div>
          <div class="step-line done"></div>
          <div class="step-item active"><div class="step-circle">3</div><span>Objectif</span></div>
        </div>

        <h1 class="auth-form-title">Objectif</h1>
        <p class="auth-form-sub">Votre programme va s'appuyer sur cet objectif.</p>

        <div class="imc-preview" style="margin-bottom: 20px;">
          <div>
            <div class="imc-preview-label"><?= esc($idealImc['label'] ?? 'IMC ideal') ?></div>
            <div class="imc-category"><?= esc($idealImc['hint'] ?? '') ?></div>
          </div>
          <div style="text-align:right">
            <div class="imc-value"><?= esc($idealImc['value'] ?? '22.0') ?></div>
            <div class="imc-category">kg/m²</div>
          </div>
        </div>

        <form class="auth-form" method="POST" action="<?= base_url('/inscription/objectif') ?>">
          <?= csrf_field() ?>

          <div class="goal-select">
            <?php foreach (($objectifs ?? []) as $objectif): ?>
              <?php $selected = ((string) ($old['id_objectif'] ?? '') === (string) $objectif['id']) ? 'checked' : ''; ?>
              <label class="goal-option">
                <input type="radio" name="id_objectif" value="<?= esc($objectif['id']) ?>" <?= $selected ?> required />
                <div class="goal-card">
                  <div class="goal-icon">🎯</div>
                  <span class="goal-text"><?= esc($objectif['nom']) ?></span>
                </div>
              </label>
            <?php endforeach; ?>
          </div>
          <small class="field-error"><?= esc($errors['id_objectif'] ?? '') ?></small>

          <button type="submit" class="btn-primary-full">Enregistrer le formulaire</button>
        </form>

        <p class="auth-switch">Retour a <a href="<?= base_url('/inscription/sante') ?>">l'etape precedente</a></p>
      </div>
    </div>
  </div>
</body>
</html>
