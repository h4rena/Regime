<?php $flash = session()->getFlashdata(); ?>
<!DOCTYPE html>
<html lang="fr">
<?php $title = 'Régimes - Backoffice'; ?>
<head>
  <meta charset="utf-8" />
  <title><?= esc($title) ?></title>
  <link rel="stylesheet" href="<?= base_url('css/style.css') ?>" />
  <style>.td-actions{white-space:nowrap}</style>
</head>
<body>
  <main class="page-main container">
    <h2>CRUD Régimes</h2>
    <?php if (! empty($flash['success'])): ?><div class="alert alert-success"><?= esc($flash['success']) ?></div><?php endif; ?>
    <a href="<?= base_url('/admin/regimes/create') ?>" class="btn-primary-sm">+ Nouveau régime</a>
    <div class="card mt-12">
      <div class="card-body p0">
        <table class="data-table">
          <thead><tr><th>Nom</th><th>Variation</th><th>Durée</th><th>Prix</th><th>Actions</th></tr></thead>
          <tbody>
            <?php foreach ($regimes as $r): ?>
              <tr>
                <td><?= esc($r['nom']) ?></td>
                <td><?= esc($r['variation_poids']) ?> kg</td>
                <td><?= esc($r['duree_jours']) ?> j</td>
                <td><?= esc(number_format((float)$r['prix'],0,',',' ')) ?> Ar</td>
                <td class="td-actions">
                  <a class="btn-action-edit" href="<?= base_url('/admin/regimes/edit/' . $r['id']) ?>">Modifier</a>
                  <a class="btn-action-del" href="<?= base_url('/admin/regimes/delete/' . $r['id']) ?>" onclick="return confirm('Supprimer ?')">Supprimer</a>
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
