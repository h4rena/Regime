<?php $errors = $errors ?? []; $mode = $mode ?? 'create'; $regime = $regime ?? []; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <title><?= $mode === 'create' ? 'Nouveau régime' : 'Modifier régime' ?></title>
  <link rel="stylesheet" href="<?= base_url('css/style.css') ?>" />
</head>
<body>
  <main class="page-main container">
    <h2><?= $mode === 'create' ? 'Créer un régime' : 'Modifier le régime' ?></h2>
    <?php if (! empty($errors)): ?><div class="alert alert-danger"><?php foreach ($errors as $e) echo esc($e) . '<br />'; ?></div><?php endif; ?>

    <form method="POST" action="<?= $mode === 'create' ? base_url('/admin/regimes/store') : base_url('/admin/regimes/update/' . $regime['id']) ?>">
      <?= csrf_field() ?>
      <div class="form-group">
        <label class="form-label">Nom</label>
        <input name="nom" class="form-input" value="<?= esc($regime['nom'] ?? '') ?>" required />
      </div>
      <div class="form-group">
        <label class="form-label">Variation poids (kg)</label>
        <input name="variation_poids" class="form-input" value="<?= esc($regime['variation_poids'] ?? '') ?>" required />
      </div>
      <div class="form-group">
        <label class="form-label">Durée (jours)</label>
        <input name="duree_jours" class="form-input" value="<?= esc($regime['duree_jours'] ?? '') ?>" required />
      </div>
      <div class="form-group">
        <label class="form-label">Prix (Ar)</label>
        <input name="prix" class="form-input" value="<?= esc($regime['prix'] ?? '') ?>" required />
      </div>
      <button class="btn-primary-full" type="submit"><?= $mode === 'create' ? 'Créer' : 'Enregistrer' ?></button>
    </form>
  </main>
</body>
</html>
