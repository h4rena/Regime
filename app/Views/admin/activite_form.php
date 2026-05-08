<?php $errors = $errors ?? []; $mode = $mode ?? 'create'; $activite = $activite ?? []; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <title><?= $mode === 'create' ? 'Nouvelle activité' : 'Modifier activité' ?></title>
  <link rel="stylesheet" href="<?= base_url('css/style.css') ?>" />
</head>
<body>
  <main class="page-main container">
    <h2><?= $mode === 'create' ? 'Créer une activité' : 'Modifier l\'activité' ?></h2>
    <?php if (! empty($errors)): ?><div class="alert alert-danger"><?php foreach ($errors as $e) echo esc($e) . '<br />'; ?></div><?php endif; ?>

    <form method="POST" action="<?= $mode === 'create' ? base_url('/admin/activites/store') : base_url('/admin/activites/update/' . $activite['id']) ?>">
      <?= csrf_field() ?>
      <div class="form-group">
        <label class="form-label">Nom</label>
        <input name="nom" class="form-input" value="<?= esc($activite['nom'] ?? '') ?>" required />
      </div>
      <div class="form-group">
        <label class="form-label">Calories par heure</label>
        <input name="calories_par_heure" class="form-input" value="<?= esc($activite['calories_par_heure'] ?? '') ?>" required />
      </div>
      <div class="form-group">
        <label class="form-label">Durée recommandée (min)</label>
        <input name="duree_recommandee_min" class="form-input" value="<?= esc($activite['duree_recommandee_min'] ?? '') ?>" required />
      </div>
      <button class="btn-primary-full" type="submit"><?= $mode === 'create' ? 'Créer' : 'Enregistrer' ?></button>
    </form>
  </main>
</body>
</html>
