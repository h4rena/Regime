<?php $errors = $errors ?? []; $mode = $mode ?? 'create'; $param = $parametre ?? []; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <title><?= $mode === 'create' ? 'Nouveau paramètre' : 'Modifier paramètre' ?></title>
  <link rel="stylesheet" href="<?= base_url('css/style.css') ?>" />
</head>
<body>
  <nav class="navbar">
    <div class="nav-inner">
      <div class="nav-logo"><span class="logo-dot"></span>NutriPlan Admin</div>
      <ul class="nav-links">
        <li><a href="<?= base_url('/profil') ?>" class="nav-link">Mon profil</a></li>
        <li><a href="<?= base_url('/dashboard') ?>" class="nav-link">Tableau de bord</a></li>
        <li><a href="<?= base_url('/admin/regimes') ?>" class="nav-link">Régimes</a></li>
        <li><a href="<?= base_url('/admin/activites') ?>" class="nav-link">Activités</a></li>
        <li><a href="<?= base_url('/admin/codes') ?>" class="nav-link">Codes portefeuille</a></li>
        <li><a href="<?= base_url('/admin/parametres') ?>" class="nav-link active">Paramètres</a></li>
      </ul>
      <div class="nav-actions">
        <span class="nav-user-name">Admin</span>
        <a href="<?= base_url('/deconnexion') ?>" class="btn-nav-ghost">Déconnexion</a>
      </div>
    </div>
  </nav>

  <main class="page-main container">
    <h2><?= $mode === 'create' ? 'Créer un paramètre' : 'Modifier le paramètre' ?></h2>
    <?php if (! empty($errors)): ?><div class="alert alert-danger"><?php foreach ($errors as $e) echo esc($e) . '<br />'; ?></div><?php endif; ?>

    <form method="POST" action="<?= $mode === 'create' ? base_url('/admin/parametres/store') : base_url('/admin/parametres/update/' . $param['id']) ?>">
      <?= csrf_field() ?>
      <div class="form-group">
        <label class="form-label">Clé</label>
        <input name="cle" class="form-input" value="<?= esc($param['cle'] ?? '') ?>" required />
      </div>
      <div class="form-group">
        <label class="form-label">Libellé</label>
        <input name="libelle" class="form-input" value="<?= esc($param['libelle'] ?? '') ?>" required />
      </div>
      <div class="form-group">
        <label class="form-label">Valeur</label>
        <input name="valeur" class="form-input" value="<?= esc($param['valeur'] ?? '') ?>" required />
      </div>
      <div class="form-group">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-input"><?= esc($param['description'] ?? '') ?></textarea>
      </div>
      <div class="form-group">
        <label class="form-label">Type</label>
        <select name="type" class="form-input">
          <option value="text" <?= (isset($param['type']) && $param['type'] === 'text') ? 'selected' : '' ?>>Texte</option>
          <option value="number" <?= (isset($param['type']) && $param['type'] === 'number') ? 'selected' : '' ?>>Nombre</option>
        </select>
      </div>
      <button class="btn-primary-full" type="submit"><?= $mode === 'create' ? 'Créer' : 'Enregistrer' ?></button>
    </form>
  </main>
</body>
</html>
