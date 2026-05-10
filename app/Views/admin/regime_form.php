<?php $errors = $errors ?? []; $mode = $mode ?? 'create'; $regime = $regime ?? []; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <title><?= $mode === 'create' ? 'Nouveau régime' : 'Modifier régime' ?></title>
  <link rel="stylesheet" href="<?= base_url('css/style.css') ?>" />
</head>
<body>
  <nav class="navbar">
    <div class="nav-inner">
      <div class="nav-logo"><span class="logo-dot"></span>NutriPlan Admin</div>
      <ul class="nav-links">
        <li><a href="<?= base_url('/profil') ?>" class="nav-link">Mon profil</a></li>
        <li><a href="<?= base_url('/dashboard') ?>" class="nav-link">Tableau de bord</a></li>
        <li><a href="<?= base_url('/admin/regimes') ?>" class="nav-link active">Régimes</a></li>
        <li><a href="<?= base_url('/admin/activites') ?>" class="nav-link">Activités</a></li>
        <li><a href="<?= base_url('/admin/codes') ?>" class="nav-link">Codes portefeuille</a></li>
        <li><a href="<?= base_url('/admin/parametres') ?>" class="nav-link">Paramètres</a></li>
      </ul>
      <div class="nav-actions">
        <span class="nav-user-name">Admin</span>
        <a href="<?= base_url('/deconnexion') ?>" class="btn-nav-ghost">Déconnexion</a>
      </div>
    </div>
  </nav>

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
        <input name="variation_poids" class="form-input" type="number" step="0.01" value="<?= esc($regime['variation_poids'] ?? '') ?>" required />
      </div>
      <div class="form-group">
        <label class="form-label">Durée (jours)</label>
        <input name="duree_jours" class="form-input" type="number" value="<?= esc($regime['duree_jours'] ?? '') ?>" required />
      </div>
      <div class="form-group">
        <label class="form-label">Prix (Ar)</label>
        <input name="prix" class="form-input" type="number" step="0.01" value="<?= esc($regime['prix'] ?? '') ?>" required />
      </div>

      <hr style="margin: 20px 0; border: none; border-top: 1px solid #ddd;" />
      <h3 style="margin-top: 20px; margin-bottom: 15px;">Composition du régime</h3>

      <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
        <div class="form-group">
          <label class="form-label">% Viande</label>
          <input name="pourcentage_viande" class="form-input" type="number" step="0.01" min="0" max="100" value="<?= esc($regime['pourcentage_viande'] ?? '25') ?>" required />
          <small style="color: #666;">Défaut: 25%</small>
        </div>
        <div class="form-group">
          <label class="form-label">% Poisson</label>
          <input name="pourcentage_poisson" class="form-input" type="number" step="0.01" min="0" max="100" value="<?= esc($regime['pourcentage_poisson'] ?? '40') ?>" required />
          <small style="color: #666;">Défaut: 40%</small>
        </div>
        <div class="form-group">
          <label class="form-label">% Volaille</label>
          <input name="pourcentage_volaille" class="form-input" type="number" step="0.01" min="0" max="100" value="<?= esc($regime['pourcentage_volaille'] ?? '35') ?>" required />
          <small style="color: #666;">Défaut: 35%</small>
        </div>
      </div>
      <small style="color: #999; display: block; margin-top: 10px;">💡 Total recommandé: 100%</small>

      <button class="btn-primary-full" type="submit" style="margin-top: 20px;"><?= $mode === 'create' ? 'Créer' : 'Enregistrer' ?></button>
    </form>
  </main>
</body>
</html>
