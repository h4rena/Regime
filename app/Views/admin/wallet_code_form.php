<?php
$mode = $mode ?? 'create';
$code = $code ?? [];
$users = $users ?? [];
$errors = $errors ?? [];
$title = $mode === 'create' ? 'Nouveau code portefeuille' : 'Modifier le code portefeuille';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= esc($title) ?> — Admin</title>
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
        <li><a href="<?= base_url('/admin/codes') ?>" class="nav-link active">Codes portefeuille</a></li>
        <li><a href="<?= base_url('/admin/parametres') ?>" class="nav-link">Paramètres</a></li>
      </ul>
      <div class="nav-actions">
        <span class="nav-user-name"><?= esc($displayName ?? 'Admin') ?></span>
        <a href="<?= base_url('/deconnexion') ?>" class="btn-nav-ghost">Déconnexion</a>
      </div>
    </div>
  </nav>

  <main class="page-main">
    <div class="container container-sm">
      <div class="card">
        <div class="card-header-blue">
          <h2 class="card-title-white"><?= esc($title) ?></h2>
        </div>
        <div class="card-body">
          <?php if (! empty($errors)): ?>
            <div class="alert alert-danger">
              <?php foreach ($errors as $message): ?>
                <div><?= esc($message) ?></div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

          <form method="post" action="<?= $mode === 'create' ? base_url('/admin/codes/store') : base_url('/admin/codes/update/' . $code['id']) ?>">
            <div class="form-grid">
              <label>
                <span>Code</span>
                <input type="text" name="code" value="<?= esc((string) ($code['code'] ?? '')) ?>" required />
              </label>

              <label>
                <span>Montant</span>
                <input type="number" step="0.01" name="montant" value="<?= esc((string) ($code['montant'] ?? '')) ?>" required />
              </label>

              <label>
                <span>Utilisé</span>
                <input type="checkbox" name="is_used" value="1" <?= ! empty($code['is_used']) ? 'checked' : '' ?> />
              </label>

              <label>
                <span>Utilisé par</span>
                <select name="used_by">
                  <option value="">-- Aucun --</option>
                  <?php foreach ($users as $user): ?>
                    <option value="<?= esc((string) $user['id']) ?>" <?= (string) ($code['used_by'] ?? '') === (string) $user['id'] ? 'selected' : '' ?>>
                      <?= esc(trim((string) ($user['prenom'] ?? '') . ' ' . (string) ($user['nom'] ?? '') . ' - ' . (string) ($user['email'] ?? ''))) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </label>
            </div>

            <div style="display:flex;gap:12px;align-items:center;margin-top:18px;">
              <button type="submit" class="btn-primary-sm"><?= $mode === 'create' ? 'Créer' : 'Enregistrer' ?></button>
              <a href="<?= base_url('/admin/codes') ?>" class="btn-nav-ghost">Retour</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </main>
</body>
</html>
