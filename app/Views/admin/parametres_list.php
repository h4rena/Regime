<?php $flash = session()->getFlashdata(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <title>Paramètres - Backoffice</title>
  <link rel="stylesheet" href="<?= base_url('css/style.css') ?>" />
  <style>.td-actions{white-space:nowrap}</style>
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
    <h2>CRUD Paramètres</h2>
    <?php if (! empty($flash['success'])): ?><div class="alert alert-success"><?= esc($flash['success']) ?></div><?php endif; ?>
    <a href="<?= base_url('/admin/parametres/create') ?>" class="btn-primary-sm">+ Nouveau paramètre</a>
    <div class="card mt-12">
      <div class="card-body p0">
        <table class="data-table">
          <thead><tr><th>Clé</th><th>Libellé</th><th>Valeur</th><th>Description</th><th>Actions</th></tr></thead>
          <tbody>
            <?php foreach ($parameters as $p): ?>
              <tr>
                <td><code><?= esc($p['cle']) ?></code></td>
                <td><?= esc($p['libelle']) ?></td>
                <td><?= esc($p['valeur']) ?></td>
                <td><?= esc($p['description'] ?? '') ?></td>
                <td class="td-actions">
                  <a class="btn-action-edit" href="<?= base_url('/admin/parametres/edit/' . $p['id']) ?>">Modifier</a>
                  <a class="btn-action-del" href="<?= base_url('/admin/parametres/delete/' . $p['id']) ?>" onclick="return confirm('Supprimer ?')">Supprimer</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</body>
</html>
