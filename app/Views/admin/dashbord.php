<?php
$title              = $title              ?? 'Tableau de Bord';
$stats              = $stats              ?? [];
$users_per_month    = $users_per_month    ?? [];
$regimes_popular    = $regimes_popular    ?? [];
$objectifs_dist     = $objectifs_dist     ?? [];
$gold_vs_normal     = $gold_vs_normal     ?? [];
$revenue_per_regime = $revenue_per_regime ?? [];
$activites_popular  = $activites_popular  ?? [];
$imc_distribution   = $imc_distribution   ?? [];
$recent_users       = $recent_users       ?? [];
$recent_regimes     = $recent_regimes     ?? [];
$wallet_codes_stats = $wallet_codes_stats ?? [];
$error              = $error              ?? null;

$currentUser = session()->get('user') ?? [];
$displayName = trim(($currentUser['prenom'] ?? '') . ' ' . ($currentUser['nom'] ?? ''));
$displayName = $displayName !== '' ? $displayName : 'Administrateur';
$isAdmin = (($currentUser['role'] ?? null) === 'Admin');

$walletDisponible = (int) ($wallet_codes_stats['disponibles'] ?? 0);
$walletUtilises = (int) ($wallet_codes_stats['utilises'] ?? 0);
$totalObjectives = array_sum(array_map(static fn ($item) => (int) ($item['total'] ?? 0), $objectifs_dist));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= esc($title) ?> — Admin</title>
  <link rel="stylesheet" href="<?= base_url('css/style.css') ?>" />
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <style>
    .dashboard-intro { margin-bottom: 24px; }
    .dashboard-overview {
      display: grid;
      grid-template-columns: 1.2fr .8fr;
      gap: 16px;
      margin-top: 16px;
    }
    .dashboard-badges {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-top: 16px;
    }
    .dashboard-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 8px 12px;
      border-radius: 999px;
      background: var(--gray-100);
      color: var(--text-secondary);
      border: 1px solid var(--border);
      font-size: 13px;
      font-weight: 500;
    }
    .section-subtitle {
      font-size: 14px;
      color: var(--text-muted);
      line-height: 1.6;
      margin-bottom: 6px;
    }
    .metric-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 16px;
      margin: 22px 0 28px;
    }
    .metric-card {
      position: relative;
      background: #fff;
      border: 1px solid var(--border);
      border-radius: var(--radius-md);
      box-shadow: var(--shadow-sm);
      padding: 20px;
      overflow: hidden;
    }
    .metric-card::before {
      content: '';
      position: absolute;
      inset: 0 0 auto 0;
      height: 4px;
      background: var(--metric-accent, var(--blue-400));
    }
    .metric-card:nth-child(2) { --metric-accent: var(--blue-600); }
    .metric-card:nth-child(3) { --metric-accent: #F0C040; }
    .metric-card:nth-child(4) { --metric-accent: #A65B00; }
    .metric-top {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 12px;
      margin-bottom: 16px;
    }
    .metric-label {
      font-size: 12px;
      font-weight: 600;
      letter-spacing: .06em;
      text-transform: uppercase;
      color: var(--text-muted);
      margin-bottom: 8px;
    }
    .metric-value {
      font-family: var(--font-display);
      font-size: 34px;
      line-height: 1;
      color: var(--text-primary);
    }
    .metric-note {
      margin-top: 8px;
      color: var(--text-muted);
      font-size: 13px;
    }
    .metric-icon {
      width: 42px;
      height: 42px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: var(--blue-50);
      color: var(--blue-800);
      font-size: 20px;
      flex-shrink: 0;
    }
    .dashboard-section { margin-top: 28px; }
    .dashboard-grid-2 {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
    }
    .dashboard-grid-3 {
      display: grid;
      grid-template-columns: 2fr 1fr 1fr;
      gap: 20px;
    }
    .chart-box { min-height: 300px; }
    .table-wrap { overflow-x: auto; }
    .dashboard-table {
      width: 100%;
      border-collapse: collapse;
      min-width: 760px;
    }
    .dashboard-table th,
    .dashboard-table td {
      padding: 12px 14px;
      border-bottom: 1px solid var(--border);
      text-align: left;
      vertical-align: middle;
      font-size: 14px;
    }
    .dashboard-table th {
      background: var(--gray-50);
      color: var(--text-secondary);
      font-size: 12px;
      text-transform: uppercase;
      letter-spacing: .04em;
      font-weight: 600;
    }
    .dashboard-table tbody tr:hover { background: var(--gray-50); }
    .stat-pill {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 5px 10px;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 600;
      background: var(--gray-100);
      color: var(--text-secondary);
    }
    .stat-pill.is-gold {
      background: #FFF0C0;
      color: #7A5200;
      border: 1px solid #F0C040;
    }
    @media (max-width: 1100px) {
      .metric-grid,
      .dashboard-grid-3 { grid-template-columns: repeat(2, 1fr); }
      .dashboard-overview { grid-template-columns: 1fr; }
    }
    @media (max-width: 768px) {
      .metric-grid,
      .dashboard-grid-2,
      .dashboard-grid-3 { grid-template-columns: 1fr; }
      .metric-value { font-size: 30px; }
    }
  </style>
</head>
<body>
  <nav class="navbar">
    <div class="nav-inner">
      <div class="nav-logo"><span class="logo-dot"></span>NutriPlan Admin</div>
      <ul class="nav-links">
        <li><a href="<?= base_url('/profil') ?>" class="nav-link">Mon profil</a></li>
        <li><a href="<?= base_url('/dashboard') ?>" class="nav-link active">Tableau de bord</a></li>
        <li><a href="<?= base_url('/admin/regimes') ?>" class="nav-link">Régimes</a></li>
        <li><a href="<?= base_url('/admin/activites') ?>" class="nav-link">Activités</a></li>
        <li><a href="<?= base_url('/admin/codes') ?>" class="nav-link">Codes portefeuille</a></li>
        <li><a href="<?= base_url('/admin/parametres') ?>" class="nav-link">Paramètres</a></li>
      </ul>
      <div class="nav-actions">
        <span class="nav-user-name"><?= esc($displayName) ?></span>
        <a href="<?= base_url('/deconnexion') ?>" class="btn-nav-ghost">Déconnexion</a>
      </div>
    </div>
  </nav>

  <main class="page-main">
    <div class="container">
      <div class="card dashboard-intro">
        <div class="card-header-blue">
          <h2 class="card-title-white">Tableau de bord admin</h2>
          <span class="badge badge-gold">ADMIN</span>
        </div>
        <div class="card-body">
          <p class="section-eyebrow">Vue d'ensemble</p>
          <h1 class="section-title" style="margin-bottom: 12px;">Pilotage de la plateforme</h1>
          <p class="section-subtitle">Suivi des utilisateurs, des régimes, des activités et des codes portefeuille.</p>
          <div class="dashboard-badges">
            <span class="dashboard-badge">Utilisateurs: <?= number_format((int) ($stats['total_users'] ?? 0)) ?></span>
            <span class="dashboard-badge">Régimes: <?= number_format((int) ($stats['total_regimes'] ?? 0)) ?></span>
            <span class="dashboard-badge">Activités: <?= number_format((int) ($stats['total_activites'] ?? 0)) ?></span>
            <span class="dashboard-badge">Objectifs: <?= number_format($totalObjectives) ?></span>
          </div>
        </div>
      </div>

      <div class="metric-grid">
        <div class="metric-card">
          <div class="metric-top">
            <div>
              <div class="metric-label">Utilisateurs</div>
              <div class="metric-value"><?= number_format((int) ($stats['total_users'] ?? 0)) ?></div>
            </div>
            <div class="metric-icon">👤</div>
          </div>
          <div class="metric-note"><?= number_format((int) ($stats['gold_users'] ?? 0)) ?> Gold actifs</div>
        </div>
        <div class="metric-card">
          <div class="metric-top">
            <div>
              <div class="metric-label">Revenus Totaux</div>
              <div class="metric-value"><?= number_format((float) ($stats['total_revenue'] ?? 0), 0, ',', ' ') ?></div>
            </div>
            <div class="metric-icon">💰</div>
          </div>
          <div class="metric-note">Ariary (Ar)</div>
        </div>
        <div class="metric-card">
          <div class="metric-top">
            <div>
              <div class="metric-label">Souscriptions</div>
              <div class="metric-value"><?= number_format((int) ($stats['total_subscriptions'] ?? 0)) ?></div>
            </div>
            <div class="metric-icon">📋</div>
          </div>
          <div class="metric-note">Régimes souscrits</div>
        </div>
        <div class="metric-card">
          <div class="metric-top">
            <div>
              <div class="metric-label">Codes Wallet</div>
              <div class="metric-value"><?= number_format($walletUtilises) ?> / <?= number_format((int) ($stats['codes_total'] ?? 0)) ?></div>
            </div>
            <div class="metric-icon">🎟</div>
          </div>
          <div class="metric-note"><?= number_format($walletDisponible) ?> disponibles</div>
        </div>
      </div>

      <div class="dashboard-grid-2 dashboard-section">
        <div class="card">
          <div class="card-header-blue"><h2 class="card-title-white">Inscriptions par mois</h2></div>
          <div class="card-body"><canvas id="chartInscriptions" height="240"></canvas></div>
        </div>
        <div class="card">
          <div class="card-header-blue"><h2 class="card-title-white">Distribution des objectifs</h2></div>
          <div class="card-body"><canvas id="chartObjectifs" height="240"></canvas></div>
        </div>
      </div>

      <div class="dashboard-grid-3 dashboard-section">
        <div class="card">
          <div class="card-header-blue"><h2 class="card-title-white">Régimes les plus souscrits</h2></div>
          <div class="card-body"><canvas id="chartRegimes" height="240"></canvas></div>
        </div>
        <div class="card">
          <div class="card-header-blue"><h2 class="card-title-white">Gold vs Normal</h2></div>
          <div class="card-body"><canvas id="chartGold" height="240"></canvas></div>
        </div>
        <div class="card">
          <div class="card-header-blue"><h2 class="card-title-white">Distribution IMC</h2></div>
          <div class="card-body"><canvas id="chartImc" height="240"></canvas></div>
        </div>
      </div>

      <div class="dashboard-grid-2 dashboard-section">
        <div class="card">
          <div class="card-header-blue"><h2 class="card-title-white">Revenus par régime</h2></div>
          <div class="card-body"><canvas id="chartRevenus" height="240"></canvas></div>
        </div>
        <div class="card">
          <div class="card-header-blue"><h2 class="card-title-white">Activités associées</h2></div>
          <div class="card-body"><canvas id="chartActivites" height="240"></canvas></div>
        </div>
      </div>

      <div class="dashboard-section">
        <div class="section-eyebrow">Tableau croisé</div>
        <h2 class="section-title">Régimes, souscriptions et revenus</h2>
        <div class="card">
          <div class="card-header-blue"><h2 class="card-title-white">Récapitulatif</h2></div>
          <div class="card-body p0 table-wrap">
            <table class="dashboard-table">
              <thead>
                <tr>
                  <th>Régime</th>
                  <th>Souscriptions</th>
                  <th>Part</th>
                  <th>Revenus (Ar)</th>
                  <th>Revenu moyen</th>
                </tr>
              </thead>
              <tbody>
                <?php $totalSubs = array_sum(array_map(static fn ($item) => (int) ($item['total'] ?? 0), $regimes_popular)) ?: 1; ?>
                <?php foreach ($regimes_popular as $regime): ?>
                  <?php
                    $regimeTotal = (int) ($regime['total'] ?? 0);
                    $part = round(($regimeTotal / $totalSubs) * 100, 1);
                    $avg = $regimeTotal > 0 ? round(((float) ($regime['revenus'] ?? 0)) / $regimeTotal) : 0;
                  ?>
                  <tr>
                    <td><strong><?= esc($regime['nom'] ?? '—') ?></strong></td>
                    <td><?= number_format($regimeTotal) ?></td>
                    <td><?= esc((string) $part) ?>%</td>
                    <td><?= number_format((float) ($regime['revenus'] ?? 0), 0, ',', ' ') ?></td>
                    <td><?= number_format($avg, 0, ',', ' ') ?></td>
                  </tr>
                <?php endforeach; ?>
                <?php if (empty($regimes_popular)): ?>
                  <tr><td colspan="5" style="text-align:center;color:var(--text-muted);">Aucune donnée disponible</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="dashboard-grid-2 dashboard-section">
        <div class="card">
          <div class="card-header-blue"><h2 class="card-title-white">Derniers utilisateurs inscrits</h2></div>
          <div class="card-body p0 table-wrap">
            <table class="dashboard-table">
              <thead>
                <tr>
                  <th>Nom</th>
                  <th>Email</th>
                  <th>Genre</th>
                  <th>Statut</th>
                  <th>Inscrit le</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($recent_users as $user): ?>
                  <tr>
                    <td><strong><?= esc(trim(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? ''))) ?></strong></td>
                    <td><?= esc($user['email'] ?? '') ?></td>
                    <td><?= esc($user['genre'] ?? '—') ?></td>
                    <td><span class="stat-pill <?= ! empty($user['is_gold']) ? 'is-gold' : '' ?>"><?= ! empty($user['is_gold']) ? 'Gold' : 'Normal' ?></span></td>
                    <td><?= ! empty($user['created_at']) ? date('d/m/Y', strtotime($user['created_at'])) : '—' ?></td>
                  </tr>
                <?php endforeach; ?>
                <?php if (empty($recent_users)): ?>
                  <tr><td colspan="5" style="text-align:center;color:var(--text-muted);">Aucun utilisateur</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <div class="card">
          <div class="card-header-blue"><h2 class="card-title-white">Dernières souscriptions régimes</h2></div>
          <div class="card-body p0 table-wrap">
            <table class="dashboard-table">
              <thead>
                <tr>
                  <th>Utilisateur</th>
                  <th>Régime</th>
                  <th>Prix payé</th>
                  <th>Début</th>
                  <th>Statut</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($recent_regimes as $subscription): ?>
                  <tr>
                    <td><strong><?= esc($subscription['utilisateur'] ?? '—') ?></strong></td>
                    <td><?= esc($subscription['regime'] ?? '—') ?></td>
                    <td><?= number_format((float) ($subscription['prix_paye'] ?? 0), 0, ',', ' ') ?></td>
                    <td><?= ! empty($subscription['date_debut']) ? date('d/m/Y', strtotime($subscription['date_debut'])) : '—' ?></td>
                    <td><span class="stat-pill is-gold"><?= esc($subscription['statut'] ?? 'actif') ?></span></td>
                  </tr>
                <?php endforeach; ?>
                <?php if (empty($recent_regimes)): ?>
                  <tr><td colspan="5" style="text-align:center;color:var(--text-muted);">Aucune souscription</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <?php if ($error): ?>
        <div class="card dashboard-section">
          <div class="card-body">
            <div style="background:#FFF7E3;border:1px solid #F0C040;border-radius:8px;padding:16px;color:#A65B00;">
              <strong>⚠️ Erreur:</strong> <?= esc($error) ?>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </main>

  <script>
    const palette = {
      blue: '#185FA5',
      blueLight: '#378ADD',
      blueDark: '#042C53',
      gold: '#F0C040',
      gray: '#8BB5DC',
      border: 'rgba(55, 138, 221, 0.18)'
    };

    Chart.defaults.color = palette.gray;
    Chart.defaults.borderColor = palette.border;
    Chart.defaults.font.family = "'DM Sans', sans-serif";

    const dataInscriptions = <?= json_encode(array_values($users_per_month)) ?>;
    const dataObjectifs = <?= json_encode(array_values($objectifs_dist)) ?>;
    const dataRegimes = <?= json_encode(array_values($regimes_popular)) ?>;
    const dataGold = <?= json_encode($gold_vs_normal) ?>;
    const dataImc = <?= json_encode(array_values($imc_distribution)) ?>;
    const dataRevenus = <?= json_encode(array_values($revenue_per_regime)) ?>;
    const dataActivites = <?= json_encode(array_values($activites_popular)) ?>;

    new Chart(document.getElementById('chartInscriptions'), {
      type: 'line',
      data: {
        labels: dataInscriptions.map(item => item.label || item.mois),
        datasets: [{
          label: 'Inscriptions',
          data: dataInscriptions.map(item => item.total),
          borderColor: palette.blueLight,
          backgroundColor: 'rgba(55, 138, 221, 0.10)',
          borderWidth: 3,
          pointRadius: 4,
          pointBackgroundColor: palette.blueDark,
          tension: 0.35,
          fill: true,
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
          x: { grid: { color: 'rgba(55, 138, 221, 0.10)' } },
          y: { grid: { color: 'rgba(55, 138, 221, 0.10)' }, beginAtZero: true, ticks: { precision: 0 } }
        }
      }
    });

    new Chart(document.getElementById('chartObjectifs'), {
      type: 'doughnut',
      data: {
        labels: dataObjectifs.map(item => item.nom),
        datasets: [{
          data: dataObjectifs.map(item => item.total),
          backgroundColor: [palette.blueLight, palette.gold, palette.blueDark, '#85B7EB'],
          borderColor: '#ffffff',
          borderWidth: 3,
        }]
      },
      options: {
        responsive: true,
        cutout: '66%',
        plugins: { legend: { position: 'bottom' } }
      }
    });

    new Chart(document.getElementById('chartRegimes'), {
      type: 'bar',
      data: {
        labels: dataRegimes.map(item => item.nom),
        datasets: [{
          label: 'Souscriptions',
          data: dataRegimes.map(item => item.total),
          backgroundColor: 'rgba(55, 138, 221, 0.28)',
          borderColor: palette.blue,
          borderWidth: 2,
          borderRadius: 8,
        }]
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
          x: { grid: { color: 'rgba(55, 138, 221, 0.10)' }, beginAtZero: true, ticks: { precision: 0 } },
          y: { grid: { color: 'transparent' } }
        }
      }
    });

    new Chart(document.getElementById('chartGold'), {
      type: 'pie',
      data: {
        labels: ['Gold', 'Normal'],
        datasets: [{
          data: [dataGold ? dataGold.gold : 0, dataGold ? dataGold.normal : 0],
          backgroundColor: [palette.gold, '#8BB5DC'],
          borderColor: '#ffffff',
          borderWidth: 3,
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } }
      }
    });

    new Chart(document.getElementById('chartImc'), {
      type: 'bar',
      data: {
        labels: dataImc.map(item => item.label),
        datasets: [{
          label: 'Utilisateurs',
          data: dataImc.map(item => item.total),
          backgroundColor: [palette.blueLight, palette.blue, palette.gold, '#A65B00'],
          borderRadius: 8,
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
          x: { grid: { color: 'transparent' } },
          y: { grid: { color: 'rgba(55, 138, 221, 0.10)' }, beginAtZero: true, ticks: { precision: 0 } }
        }
      }
    });

    new Chart(document.getElementById('chartRevenus'), {
      type: 'bar',
      data: {
        labels: dataRevenus.map(item => item.nom),
        datasets: [{
          label: 'Revenus (Ar)',
          data: dataRevenus.map(item => item.revenus),
          backgroundColor: 'rgba(24, 95, 165, 0.26)',
          borderColor: palette.blueDark,
          borderWidth: 2,
          borderRadius: 8,
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
          x: { grid: { color: 'transparent' } },
          y: { grid: { color: 'rgba(55, 138, 221, 0.10)' }, beginAtZero: true }
        }
      }
    });

    new Chart(document.getElementById('chartActivites'), {
      type: 'radar',
      data: {
        labels: dataActivites.map(item => item.nom),
        datasets: [{
          label: 'Associations',
          data: dataActivites.map(item => item.total),
          borderColor: palette.gold,
          backgroundColor: 'rgba(240, 192, 64, 0.16)',
          pointBackgroundColor: palette.blueDark,
          borderWidth: 2,
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
          r: {
            grid: { color: 'rgba(55, 138, 221, 0.10)' },
            pointLabels: { color: palette.blueDark, font: { size: 11 } },
            ticks: { display: false },
            beginAtZero: true,
          }
        }
      }
    });
  </script>
</body>
</html>