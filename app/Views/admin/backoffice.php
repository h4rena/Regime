<?php
$currentUser = $currentUser ?? session()->get('user') ?? [];
$displayName = trim(($currentUser['prenom'] ?? '') . ' ' . ($currentUser['nom'] ?? ''));
$displayName = $displayName !== '' ? $displayName : 'Administrateur';
$signupSeries = $signupSeries ?? [];
$stats = $stats ?? [];
$recentUsers = $recentUsers ?? [];
$regimes = $regimes ?? [];
$activities = $activities ?? [];
$walletCodes = $walletCodes ?? [];
$parameters = $parameters ?? [];
$maxSignup = 1;
if (! empty($signupSeries)) {
  $maxSignup = max(array_map(static fn ($item) => (int) ($item['value'] ?? 0), $signupSeries)) ?: 1;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriPlan - Back Office</title>
  <link rel="stylesheet" href="<?= base_url('css/style.css') ?>" />
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet" />
  <style>
    .td-muted { color: var(--text-muted); font-size: 13px; }
    .td-actions { white-space: nowrap; }
    .btn-action-edit,
    .btn-action-del {
      border: 1px solid var(--border-mid);
      border-radius: 999px;
      padding: 6px 12px;
      font-size: 12px;
      font-weight: 600;
      cursor: pointer;
      background: white;
      transition: all 0.15s ease;
    }
    .btn-action-edit {
      color: var(--blue-800);
    }
    .btn-action-edit:hover {
      background: var(--blue-50);
    }
    .btn-action-del {
      color: #A65B00;
      border-color: rgba(240, 192, 64, 0.45);
    }
    .btn-action-del:hover {
      background: #FFF7E3;
    }
    a.btn-action-edit,
    a.btn-action-del {
      display: inline-block;
      text-decoration: none;
    }
  </style>
</head>
<body>
  <nav class="navbar">
    <div class="nav-inner">
      <div class="nav-logo"><span class="logo-dot"></span>NutriPlan Admin</div>
      <ul class="nav-links">
        <li><a href="/profil" class="nav-link active">Mon profil</a></li>
        <li><a href="#dashboard" class="nav-link active">Tableau de bord</a></li>
        <li><a href="#regimes" class="nav-link">Régimes</a></li>
        <li><a href="#activites" class="nav-link">Activités</a></li>
        <li><a href="#wallet-codes" class="nav-link">Codes portefeuille</a></li>
        <li><a href="#parametres" class="nav-link">Paramètres</a></li>
      </ul>
      <div class="nav-actions">
        <span class="nav-user-name"><?= esc($displayName) ?></span>
        <a href="<?= base_url('/deconnexion') ?>" class="btn-nav-ghost">Déconnexion</a>
      </div>
    </div>
  </nav>

  <main class="page-main">
    <div class="container">
      <div class="card" id="dashboard">
        <div class="card-header-blue">
          <h2 class="card-title-white">Tableau de bord admin</h2>
        </div>
        <div class="card-body">
          <p class="section-eyebrow">Vue d'ensemble</p>
          <h1 class="section-title" style="margin-top:6px;">Pilotage de la plateforme</h1>
          <p class="section-subtitle">Suivi des utilisateurs, des régimes, des activités et des codes portefeuille.</p>
        </div>
      </div>

      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:16px;margin-top:16px;">
        <div class="card"><div class="card-body"><p class="hstat-val" style="color:var(--blue-900)"><?= esc((string) ($stats['users'] ?? 0)) ?></p><p class="hstat-lbl" style="color:var(--text-muted)">Utilisateurs</p></div></div>
        <div class="card"><div class="card-body"><p class="hstat-val" style="color:var(--blue-900)"><?= esc((string) ($stats['goldUsers'] ?? 0)) ?></p><p class="hstat-lbl" style="color:var(--text-muted)">Abonnés Gold</p></div></div>
        <div class="card"><div class="card-body"><p class="hstat-val" style="color:var(--blue-900)"><?= esc((string) ($stats['regimes'] ?? 0)) ?></p><p class="hstat-lbl" style="color:var(--text-muted)">Régimes</p></div></div>
        <div class="card"><div class="card-body"><p class="hstat-val" style="color:var(--blue-900)"><?= esc((string) ($stats['activities'] ?? 0)) ?></p><p class="hstat-lbl" style="color:var(--text-muted)">Activités</p></div></div>
        <div class="card"><div class="card-body"><p class="hstat-val" style="color:var(--blue-900)"><?= esc((string) ($stats['walletAvailable'] ?? 0)) ?></p><p class="hstat-lbl" style="color:var(--text-muted)">Codes disponibles</p></div></div>
        <div class="card"><div class="card-body"><p class="hstat-val" style="color:var(--blue-900)"><?= esc(number_format((float) ($stats['walletBalance'] ?? 0), 0, ',', ' ')) ?></p><p class="hstat-lbl" style="color:var(--text-muted)">Solde global (Ar)</p></div></div>
      </div>

      <div class="card mt-24">
        <div class="card-header-blue">
          <h2 class="card-title-white">Inscriptions par mois</h2>
        </div>
        <div class="card-body">
          <div style="display:flex;align-items:flex-end;gap:14px;min-height:210px;padding-top:10px;flex-wrap:wrap;">
            <?php foreach ($signupSeries as $item): ?>
              <?php $value = (int) ($item['value'] ?? 0); $height = $maxSignup > 0 ? max(8, (int) round(($value / $maxSignup) * 150)) : 8; ?>
              <div style="flex:1;min-width:60px;text-align:center;">
                <div style="height:160px;display:flex;align-items:flex-end;justify-content:center;">
                  <div style="width:100%;max-width:58px;height:<?= esc((string) $height) ?>px;background:linear-gradient(180deg,var(--blue-400),var(--blue-800));border-radius:14px 14px 6px 6px;box-shadow:var(--shadow-sm);"></div>
                </div>
                <div style="margin-top:10px;font-weight:600;color:var(--blue-900)"><?= esc((string) $value) ?></div>
                <div style="font-size:12px;color:var(--text-muted)"><?= esc((string) $item['label']) ?></div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <div class="card mt-24" id="regimes">
        <div class="card-header-blue" style="justify-content:space-between;gap:12px;flex-wrap:wrap;">
          <h2 class="card-title-white">CRUD des régimes</h2>
          <a class="btn-primary-sm" href="<?= base_url('/admin/regimes/create') ?>">+ Nouveau régime</a>
        </div>
        <div class="card-body p0">
          <table class="data-table">
            <thead>
              <tr><th>Nom</th><th>Variation</th><th>Durée</th><th>Prix</th><th>Actions</th></tr>
            </thead>
            <tbody>
              <?php foreach ($regimes as $regime): ?>
                <tr>
                  <td><strong><?= esc((string) ($regime['nom'] ?? '')) ?></strong></td>
                  <td><?= esc((string) ($regime['variation_poids'] ?? '')) ?> kg</td>
                  <td><?= esc((string) ($regime['duree_jours'] ?? '')) ?> jours</td>
                  <td><?= esc(number_format((float) ($regime['prix'] ?? 0), 0, ',', ' ')) ?> Ar</td>
                  <td class="td-actions">
                    <a class="btn-action-edit" href="<?= base_url('/admin/regimes/edit/' . (int) ($regime['id'] ?? 0)) ?>">Modifier</a>
                    <a class="btn-action-del" href="<?= base_url('/admin/regimes/delete/' . (int) ($regime['id'] ?? 0)) ?>" onclick="return confirm('Supprimer ce régime ?')">Supprimer</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="card mt-24" id="activites">
        <div class="card-header-blue" style="justify-content:space-between;gap:12px;flex-wrap:wrap;">
          <h2 class="card-title-white">CRUD des activités sportives</h2>
          <a class="btn-primary-sm" href="<?= base_url('/admin/activites/create') ?>">+ Nouvelle activité</a>
        </div>
        <div class="card-body p0">
          <table class="data-table">
            <thead>
              <tr><th>Activité</th><th>Calories / heure</th><th>Durée recommandée</th><th>Actions</th></tr>
            </thead>
            <tbody>
              <?php foreach ($activities as $activity): ?>
                <tr>
                  <td><strong><?= esc((string) ($activity['nom'] ?? '')) ?></strong></td>
                  <td><?= esc((string) ($activity['calories_par_heure'] ?? '')) ?></td>
                  <td><?= esc((string) ($activity['duree_recommandee_min'] ?? '')) ?> min</td>
                  <td class="td-actions">
                    <a class="btn-action-edit" href="<?= base_url('/admin/activites/edit/' . (int) ($activity['id'] ?? 0)) ?>">Modifier</a>
                    <a class="btn-action-del" href="<?= base_url('/admin/activites/delete/' . (int) ($activity['id'] ?? 0)) ?>" onclick="return confirm('Supprimer cette activité ?')">Supprimer</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="card mt-24" id="wallet-codes">
        <div class="card-header-blue" style="justify-content:space-between;gap:12px;flex-wrap:wrap;">
          <h2 class="card-title-white">Validation des codes portefeuille</h2>
          <a class="btn-primary-sm" href="<?= base_url('/admin/codes/create') ?>">+ Générer des codes</a>
        </div>
        <div class="card-body p0">
          <table class="data-table">
            <thead>
              <tr><th>Code</th><th>Montant</th><th>Statut</th><th>Utilisé par</th><th>Date</th><th>Actions</th></tr>
            </thead>
            <tbody>
              <?php foreach ($walletCodes as $code): ?>
                <tr>
                  <td><code><?= esc((string) ($code['code'] ?? '')) ?></code></td>
                  <td><?= esc(number_format((float) ($code['montant'] ?? 0), 0, ',', ' ')) ?> Ar</td>
                  <td>
                    <?php if (! empty($code['is_used'])): ?>
                      <span class="badge badge-used">Utilisé</span>
                    <?php else: ?>
                      <span class="badge badge-success">Disponible</span>
                    <?php endif; ?>
                  </td>
                  <td><?= esc(trim((string) ($code['user_nom'] ?? '') . ' ' . (string) ($code['user_prenom'] ?? '')) ?: '—') ?></td>
                  <td><?= esc((string) ($code['used_at'] ?? '—')) ?></td>
                  <td class="td-actions">
                    <a class="btn-action-edit" href="<?= base_url('/admin/codes/edit/' . (int) ($code['id'] ?? 0)) ?>">Modifier</a>
                    <a class="btn-action-del" href="<?= base_url('/admin/codes/delete/' . (int) ($code['id'] ?? 0)) ?>" onclick="return confirm('Supprimer ce code ?')">Supprimer</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="card mt-24" id="parametres">
        <div class="card-header-blue" style="justify-content:space-between;gap:12px;flex-wrap:wrap;">
          <h2 class="card-title-white">CRUD des paramètres nécessaires</h2>
          <a class="btn-primary-sm" href="<?= base_url('/admin/parametres/create') ?>">+ Nouveau paramètre</a>
        </div>
        <div class="card-body p0">
          <table class="data-table">
            <thead>
              <tr><th>Clé</th><th>Libellé</th><th>Valeur</th><th>Description</th><th>Actions</th></tr>
            </thead>
            <tbody>
              <?php foreach ($parameters as $parameter): ?>
                <tr>
                  <td><code><?= esc((string) ($parameter['cle'] ?? '')) ?></code></td>
                  <td><strong><?= esc((string) ($parameter['libelle'] ?? '')) ?></strong></td>
                  <td><?= esc((string) ($parameter['valeur'] ?? '')) ?></td>
                  <td><?= esc((string) ($parameter['description'] ?? '')) ?></td>
                  <td class="td-actions">
                    <a class="btn-action-edit" href="<?= base_url('/admin/parametres/edit/' . (int) ($parameter['id'] ?? 0)) ?>">Modifier</a>
                    <a class="btn-action-del" href="<?= base_url('/admin/parametres/delete/' . (int) ($parameter['id'] ?? 0)) ?>" onclick="return confirm('Supprimer ce paramètre ?')">Supprimer</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="card mt-24">
        <div class="card-header-blue" style="justify-content:space-between;gap:12px;flex-wrap:wrap;">
          <h2 class="card-title-white">Utilisateurs récents</h2>
        </div>
        <div class="card-body p0">
          <table class="data-table">
            <thead>
              <tr><th>Utilisateur</th><th>Rôle</th><th>Gold</th><th>Portefeuille</th><th>Inscription</th></tr>
            </thead>
            <tbody>
              <?php foreach ($recentUsers as $user): ?>
                <tr>
                  <td><strong><?= esc(trim((string) ($user['prenom'] ?? '') . ' ' . (string) ($user['nom'] ?? ''))) ?></strong><br /><span class="td-muted"><?= esc((string) ($user['email'] ?? '')) ?></span></td>
                  <td><?= esc((string) ($user['role_nom'] ?? 'Utilisateur')) ?></td>
                  <td>
                    <?php if (! empty($user['is_gold'])): ?>
                      <span class="badge badge-gold">Gold</span>
                    <?php else: ?>
                      <span class="badge">Normal</span>
                    <?php endif; ?>
                  </td>
                  <td><?= esc(number_format((float) ($user['wallet_balance'] ?? 0), 0, ',', ' ')) ?> Ar</td>
                  <td class="td-muted"><?= esc((string) ($user['created_at'] ?? '')) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>
</body>
</html>