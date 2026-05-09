<?php
// Sécurité : valeurs par défaut si variables manquantes
$title              = $title              ?? 'Tableau de Bord';
$stats              = $stats              ?? [];
$users_per_month    = $users_per_month    ?? [];
$regimes_popular    = $regimes_popular    ?? [];
$objectifs_dist     = $objectifs_dist     ?? [];
$gold_vs_normal     = $gold_vs_normal     ?? null;
$revenue_per_regime = $revenue_per_regime ?? [];
$activites_popular  = $activites_popular  ?? [];
$imc_distribution   = $imc_distribution  ?? [];
$recent_users       = $recent_users       ?? [];
$recent_regimes     = $recent_regimes     ?? [];
$error              = $error              ?? null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> — Admin</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:      #0c0e14;
            --surface: #13161f;
            --card:    #181c27;
            --border:  #252a38;
            --accent:  #4fffb0;
            --accent2: #7b61ff;
            --accent3: #ff6b6b;
            --gold:    #ffd166;
            --text:    #e8eaf0;
            --muted:   #6b7280;
            --radius:  14px;
            --font:    'Syne', sans-serif;
            --mono:    'DM Mono', monospace;
        }

        body { background: var(--bg); color: var(--text); font-family: var(--font); min-height: 100vh; }

        /* SIDEBAR */
        .sidebar {
            position: fixed; left: 0; top: 0; bottom: 0; width: 220px;
            background: var(--surface); border-right: 1px solid var(--border);
            display: flex; flex-direction: column; padding: 28px 20px; z-index: 100;
        }
        .sidebar-logo {
            font-size: 1.1rem; font-weight: 800; letter-spacing: -0.5px;
            color: var(--accent); margin-bottom: 36px;
            display: flex; align-items: center; gap: 8px;
        }
        .sidebar-logo span { color: var(--text); }
        .sidebar nav a {
            display: flex; align-items: center; gap: 10px;
            color: var(--muted); text-decoration: none;
            padding: 10px 12px; border-radius: 8px;
            font-size: .9rem; font-weight: 600; margin-bottom: 4px; transition: all .2s;
        }
        .sidebar nav a.active,
        .sidebar nav a:hover { color: var(--text); background: var(--card); }
        .sidebar nav a.active { color: var(--accent); }
        .sidebar-bottom { margin-top: auto; font-size: .78rem; color: var(--muted); text-align: center; }

        /* MAIN */
        .main { margin-left: 220px; padding: 36px 36px 60px; min-height: 100vh; }

        /* HEADER */
        .page-header {
            display: flex; align-items: center; justify-content: space-between; margin-bottom: 36px;
        }
        .page-header h1 { font-size: 1.8rem; font-weight: 800; letter-spacing: -1px; }
        .page-header h1 span { color: var(--accent); }
        .badge-admin {
            background: linear-gradient(135deg, var(--accent2), var(--accent));
            color: #fff; font-size: .75rem; font-weight: 700;
            padding: 4px 12px; border-radius: 20px; letter-spacing: .5px;
        }

        /* ERREUR */
        .alert-error {
            background: rgba(255,107,107,.1); border: 1px solid var(--accent3);
            border-radius: 10px; padding: 14px 18px; margin-bottom: 24px;
            color: var(--accent3); font-size: .85rem;
        }

        /* KPI GRID */
        .kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; margin-bottom: 32px; }
        .kpi-card {
            background: var(--card); border: 1px solid var(--border);
            border-radius: var(--radius); padding: 22px 24px;
            position: relative; overflow: hidden; transition: transform .2s, border-color .2s;
        }
        .kpi-card:hover { transform: translateY(-3px); border-color: var(--accent); }
        .kpi-card::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
            background: var(--accent-line, var(--accent));
        }
        .kpi-card:nth-child(2) { --accent-line: var(--gold); }
        .kpi-card:nth-child(3) { --accent-line: var(--accent2); }
        .kpi-card:nth-child(4) { --accent-line: var(--accent3); }
        .kpi-label { font-size: .72rem; text-transform: uppercase; letter-spacing: 1.5px; color: var(--muted); margin-bottom: 10px; }
        .kpi-value { font-size: 2rem; font-weight: 800; letter-spacing: -1px; font-family: var(--mono); }
        .kpi-sub   { font-size: .78rem; color: var(--muted); margin-top: 6px; }
        .kpi-icon  { position: absolute; right: 18px; top: 18px; font-size: 1.8rem; opacity: .12; }

        /* SECTION TITLE */
        .section-title {
            font-size: .7rem; text-transform: uppercase; letter-spacing: 2px;
            color: var(--accent); font-weight: 700; margin-bottom: 16px;
        }

        /* CHARTS */
        .charts-row { display: grid; gap: 20px; margin-bottom: 28px; }
        .charts-row.col2 { grid-template-columns: 1fr 1fr; }
        .charts-row.col3 { grid-template-columns: 2fr 1fr 1fr; }
        .chart-card { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); padding: 24px; }
        .chart-card h3 { font-size: .85rem; font-weight: 700; color: var(--text); margin-bottom: 18px; }
        .chart-card canvas { width: 100% !important; }

        /* MINI STATS */
        .mini-stats { display: flex; gap: 12px; margin-bottom: 28px; flex-wrap: wrap; }
        .mini-stat {
            flex: 1; min-width: 140px; background: var(--card); border: 1px solid var(--border);
            border-radius: 10px; padding: 16px 18px; display: flex; align-items: center; gap: 14px;
        }
        .mini-stat-icon { width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0; }
        .mini-stat-info { display: flex; flex-direction: column; }
        .mini-stat-val  { font-size: 1.1rem; font-weight: 800; font-family: var(--mono); }
        .mini-stat-lbl  { font-size: .7rem; color: var(--muted); }

        /* TABLE */
        .table-card { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); padding: 24px; margin-bottom: 28px; overflow-x: auto; }
        .table-card h3 { font-size: .85rem; font-weight: 700; margin-bottom: 18px; }
        table { width: 100%; border-collapse: collapse; font-size: .82rem; }
        thead th {
            background: var(--surface); color: var(--muted); text-align: left; padding: 10px 14px;
            font-size: .68rem; text-transform: uppercase; letter-spacing: 1.2px; border-bottom: 1px solid var(--border);
        }
        tbody tr { border-bottom: 1px solid var(--border); transition: background .15s; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: var(--surface); }
        tbody td { padding: 11px 14px; color: var(--text); }
        .badge { display: inline-block; padding: 2px 10px; border-radius: 20px; font-size: .72rem; font-weight: 700; }
        .badge-gold   { background: rgba(255,209,102,.15); color: var(--gold); }
        .badge-normal { background: rgba(107,114,128,.15);  color: var(--muted); }
        .badge-active { background: rgba(79,255,176,.15);   color: var(--accent); }

        /* RESPONSIVE */
        @media (max-width: 1100px) {
            .kpi-grid { grid-template-columns: repeat(2, 1fr); }
            .charts-row.col3 { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 768px) {
            .sidebar { display: none; }
            .main { margin-left: 0; padding: 20px; }
            .kpi-grid { grid-template-columns: 1fr 1fr; }
            .charts-row.col2, .charts-row.col3 { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="sidebar-logo">◈ <span>Regime<b>App</b></span></div>
    <nav>
        <a href="<?= base_url('/admin/dashboard') ?>" class="active">⬡ Dashboard</a>
        <a href="<?= base_url('/admin/regimes') ?>">◈ Régimes</a>
        <a href="<?= base_url('/admin/activites') ?>">◇ Activités</a>
        <a href="<?= base_url('/admin/codes') ?>">◎ Codes Wallet</a>
        <a href="<?= base_url('/admin/parametres') ?>">⊡ Paramètres</a>
        <a href="<?= base_url('/profil') ?>">○ Mon profil</a>
    </nav>
    <div class="sidebar-bottom">
        <?php
            $sessionUser = session()->get('user') ?? [];
            $sidebarName = trim(($sessionUser['prenom'] ?? '') . ' ' . ($sessionUser['nom'] ?? ''));
            echo esc($sidebarName ?: 'Administrateur');
        ?>
        <br><a href="<?= base_url('/deconnexion') ?>" style="color:var(--accent3);text-decoration:none;font-size:.75rem;">Déconnexion →</a>
    </div>
</aside>

<!-- MAIN -->
<main class="main">

    <div class="page-header">
        <h1>Tableau de <span>Bord</span></h1>
        <span class="badge-admin">ADMIN</span>
    </div>

    <?php if ($error): ?>
    <div class="alert-error">⚠️ <?= esc($error) ?></div>
    <?php endif; ?>

    <!-- KPI CARDS -->
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-icon">👤</div>
            <div class="kpi-label">Utilisateurs</div>
            <div class="kpi-value"><?= number_format($stats['total_users'] ?? 0) ?></div>
            <div class="kpi-sub"><?= $stats['gold_users'] ?? 0 ?> Gold actifs</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon">💰</div>
            <div class="kpi-label">Revenus Totaux</div>
            <div class="kpi-value"><?= number_format($stats['total_revenue'] ?? 0, 0, ',', ' ') ?></div>
            <div class="kpi-sub">Ariary (Ar)</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon">📋</div>
            <div class="kpi-label">Souscriptions</div>
            <div class="kpi-value"><?= number_format($stats['total_subscriptions'] ?? 0) ?></div>
            <div class="kpi-sub">Régimes souscrits</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon">🎟</div>
            <div class="kpi-label">Codes Wallet</div>
            <div class="kpi-value"><?= ($stats['codes_used'] ?? 0) ?> / <?= ($stats['codes_total'] ?? 0) ?></div>
            <div class="kpi-sub">Utilisés / Total</div>
        </div>
    </div>

    <!-- MINI STATS -->
    <div class="mini-stats">
        <div class="mini-stat">
            <div class="mini-stat-icon" style="background:rgba(79,255,176,.1);color:var(--accent)">🥗</div>
            <div class="mini-stat-info">
                <span class="mini-stat-val"><?= $stats['total_regimes'] ?? 0 ?></span>
                <span class="mini-stat-lbl">Régimes définis</span>
            </div>
        </div>
        <div class="mini-stat">
            <div class="mini-stat-icon" style="background:rgba(123,97,255,.1);color:var(--accent2)">🏃</div>
            <div class="mini-stat-info">
                <span class="mini-stat-val"><?= $stats['total_activites'] ?? 0 ?></span>
                <span class="mini-stat-lbl">Activités sportives</span>
            </div>
        </div>
        <div class="mini-stat">
            <div class="mini-stat-icon" style="background:rgba(255,209,102,.1);color:var(--gold)">⭐</div>
            <div class="mini-stat-info">
                <span class="mini-stat-val"><?= number_format($stats['total_wallet_balance'] ?? 0, 0, ',', ' ') ?> Ar</span>
                <span class="mini-stat-lbl">Solde total wallets</span>
            </div>
        </div>
        <div class="mini-stat">
            <div class="mini-stat-icon" style="background:rgba(255,107,107,.1);color:var(--accent3)">🎯</div>
            <div class="mini-stat-info">
                <span class="mini-stat-val"><?= array_sum(array_column($objectifs_dist, 'total')) ?></span>
                <span class="mini-stat-lbl">Objectifs définis</span>
            </div>
        </div>
    </div>

    <!-- ROW 1 : Inscriptions + Objectifs -->
    <div class="section-title">Analyse des utilisateurs</div>
    <div class="charts-row col2">
        <div class="chart-card">
            <h3>📈 Nouvelles inscriptions (12 derniers mois)</h3>
            <canvas id="chartInscriptions" height="220"></canvas>
        </div>
        <div class="chart-card">
            <h3>🎯 Distribution des objectifs</h3>
            <canvas id="chartObjectifs" height="220"></canvas>
        </div>
    </div>

    <!-- ROW 2 : Régimes + Gold + IMC -->
    <div class="section-title">Régimes & Abonnements</div>
    <div class="charts-row col3">
        <div class="chart-card">
            <h3>🥗 Régimes les plus souscrits</h3>
            <canvas id="chartRegimes" height="220"></canvas>
        </div>
        <div class="chart-card">
            <h3>⭐ Gold vs Normal</h3>
            <canvas id="chartGold" height="220"></canvas>
        </div>
        <div class="chart-card">
            <h3>⚖️ Distribution IMC</h3>
            <canvas id="chartImc" height="220"></canvas>
        </div>
    </div>

    <!-- ROW 3 : Revenus + Activités -->
    <div class="section-title">Revenus & Activités</div>
    <div class="charts-row col2">
        <div class="chart-card">
            <h3>💰 Revenus par régime (Ar)</h3>
            <canvas id="chartRevenus" height="220"></canvas>
        </div>
        <div class="chart-card">
            <h3>🏃 Activités les plus associées</h3>
            <canvas id="chartActivites" height="220"></canvas>
        </div>
    </div>

    <!-- TABLEAU CROISÉ -->
    <div class="section-title">Tableau croisé — Régimes</div>
    <div class="table-card">
        <h3>Régimes × Souscriptions × Revenus</h3>
        <table>
            <thead>
                <tr>
                    <th>Régime</th>
                    <th>Souscriptions</th>
                    <th>Part (%)</th>
                    <th>Revenus (Ar)</th>
                    <th>Revenu moyen / souscription</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $totalSubs = array_sum(array_column($regimes_popular, 'total')) ?: 1;
                foreach ($regimes_popular as $r):
                    $part = round(($r['total'] / $totalSubs) * 100, 1);
                    $avg  = $r['total'] > 0 ? round($r['revenus'] / $r['total']) : 0;
                ?>
                <tr>
                    <td><strong><?= esc($r['nom']) ?></strong></td>
                    <td style="font-family:var(--mono)"><?= $r['total'] ?></td>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div style="width:80px;height:6px;background:var(--border);border-radius:4px;overflow:hidden;">
                                <div style="width:<?= $part ?>%;height:100%;background:var(--accent);border-radius:4px;"></div>
                            </div>
                            <?= $part ?>%
                        </div>
                    </td>
                    <td style="font-family:var(--mono)"><?= number_format($r['revenus'], 0, ',', ' ') ?></td>
                    <td style="font-family:var(--mono);color:var(--gold)"><?= number_format($avg, 0, ',', ' ') ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($regimes_popular)): ?>
                <tr><td colspan="5" style="text-align:center;color:var(--muted);padding:24px">Aucune donnée disponible</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- TABLEAUX : Derniers inscrits + Dernières souscriptions -->
    <div class="section-title">Dernières activités</div>
    <div class="charts-row col2">

        <div class="table-card" style="margin-bottom:0">
            <h3>👤 Derniers utilisateurs inscrits</h3>
            <table>
                <thead>
                    <tr><th>Nom</th><th>Email</th><th>Genre</th><th>Statut</th><th>Inscrit le</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_users as $u): ?>
                    <tr>
                        <td><strong><?= esc(trim(($u['prenom'] ?? '') . ' ' . ($u['nom'] ?? ''))) ?></strong></td>
                        <td style="color:var(--muted);font-size:.78rem"><?= esc($u['email'] ?? '') ?></td>
                        <td><?= esc($u['genre'] ?? '—') ?></td>
                        <td>
                            <span class="badge <?= !empty($u['is_gold']) ? 'badge-gold' : 'badge-normal' ?>">
                                <?= !empty($u['is_gold']) ? '⭐ Gold' : 'Normal' ?>
                            </span>
                        </td>
                        <td style="font-family:var(--mono);font-size:.78rem">
                            <?= isset($u['created_at']) ? date('d/m/Y', strtotime($u['created_at'])) : '—' ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($recent_users)): ?>
                    <tr><td colspan="5" style="text-align:center;color:var(--muted);padding:24px">Aucun utilisateur</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="table-card" style="margin-bottom:0">
            <h3>📋 Dernières souscriptions régimes</h3>
            <table>
                <thead>
                    <tr><th>Utilisateur</th><th>Régime</th><th>Prix payé (Ar)</th><th>Début</th><th>Statut</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_regimes as $ur): ?>
                    <tr>
                        <td><strong><?= esc($ur['utilisateur'] ?? '—') ?></strong></td>
                        <td style="font-size:.8rem"><?= esc($ur['regime'] ?? '—') ?></td>
                        <td style="font-family:var(--mono)"><?= number_format($ur['prix_paye'] ?? 0, 0, ',', ' ') ?></td>
                        <td style="font-family:var(--mono);font-size:.78rem">
                            <?= !empty($ur['date_debut']) ? date('d/m/Y', strtotime($ur['date_debut'])) : '—' ?>
                        </td>
                        <td><span class="badge badge-active"><?= esc($ur['statut'] ?? 'actif') ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($recent_regimes)): ?>
                    <tr><td colspan="5" style="text-align:center;color:var(--muted);padding:24px">Aucune souscription</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

</main>

<!-- CHARTS JS -->
<script>
const C = {
    accent:'#4fffb0', accent2:'#7b61ff', accent3:'#ff6b6b',
    gold:'#ffd166', muted:'#6b7280', border:'#252a38', bg:'#181c27', text:'#e8eaf0',
};
Chart.defaults.color = C.muted;
Chart.defaults.borderColor = C.border;
Chart.defaults.font.family = "'DM Mono', monospace";
Chart.defaults.plugins.legend.labels.boxWidth = 12;

const dataInscriptions = <?= json_encode(array_values($users_per_month)) ?>;
const dataObjectifs    = <?= json_encode(array_values($objectifs_dist)) ?>;
const dataRegimes      = <?= json_encode(array_values($regimes_popular)) ?>;
const dataGold         = <?= json_encode($gold_vs_normal) ?>;
const dataImc          = <?= json_encode(array_values($imc_distribution)) ?>;
const dataRevenus      = <?= json_encode(array_values($revenue_per_regime)) ?>;
const dataActivites    = <?= json_encode(array_values($activites_popular)) ?>;

// 1. Inscriptions (Line)
new Chart(document.getElementById('chartInscriptions'), {
    type: 'line',
    data: {
        labels: dataInscriptions.map(d => d.label || d.mois),
        datasets: [{
            label: 'Inscriptions',
            data: dataInscriptions.map(d => d.total),
            borderColor: C.accent, backgroundColor: 'rgba(79,255,176,.08)',
            borderWidth: 2.5, pointBackgroundColor: C.accent, pointRadius: 4,
            tension: .4, fill: true,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { color: C.border } },
            y: { grid: { color: C.border }, beginAtZero: true, ticks: { precision: 0 } }
        }
    }
});

// 2. Objectifs (Donut)
new Chart(document.getElementById('chartObjectifs'), {
    type: 'doughnut',
    data: {
        labels: dataObjectifs.map(d => d.nom),
        datasets: [{
            data: dataObjectifs.map(d => d.total),
            backgroundColor: [C.accent, C.accent2, C.gold],
            borderColor: C.bg, borderWidth: 3, hoverOffset: 8,
        }]
    },
    options: { responsive: true, cutout: '68%', plugins: { legend: { position: 'bottom' } } }
});

// 3. Régimes (Bar horizontal)
new Chart(document.getElementById('chartRegimes'), {
    type: 'bar',
    data: {
        labels: dataRegimes.map(d => d.nom),
        datasets: [{
            label: 'Souscriptions', data: dataRegimes.map(d => d.total),
            backgroundColor: 'rgba(79,255,176,.25)', borderColor: C.accent,
            borderWidth: 2, borderRadius: 6,
        }]
    },
    options: {
        indexAxis: 'y', responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { color: C.border }, beginAtZero: true, ticks: { precision: 0 } },
            y: { grid: { color: 'transparent' } }
        }
    }
});

// 4. Gold vs Normal (Pie)
new Chart(document.getElementById('chartGold'), {
    type: 'pie',
    data: {
        labels: ['⭐ Gold', 'Normal'],
        datasets: [{
            data: [dataGold ? dataGold.gold : 0, dataGold ? dataGold.normal : 0],
            backgroundColor: [C.gold, C.muted], borderColor: C.bg, borderWidth: 3,
        }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});

// 5. IMC (Bar)
new Chart(document.getElementById('chartImc'), {
    type: 'bar',
    data: {
        labels: dataImc.map(d => d.label),
        datasets: [{
            label: 'Utilisateurs', data: dataImc.map(d => d.total),
            backgroundColor: [C.accent2, C.accent, C.gold, C.accent3], borderRadius: 6,
        }]
    },
    options: {
        responsive: true, plugins: { legend: { display: false } },
        scales: {
            x: { grid: { color: 'transparent' } },
            y: { grid: { color: C.border }, beginAtZero: true, ticks: { precision: 0 } }
        }
    }
});

// 6. Revenus (Bar)
new Chart(document.getElementById('chartRevenus'), {
    type: 'bar',
    data: {
        labels: dataRevenus.map(d => d.nom),
        datasets: [{
            label: 'Revenus (Ar)', data: dataRevenus.map(d => d.revenus),
            backgroundColor: 'rgba(123,97,255,.3)', borderColor: C.accent2,
            borderWidth: 2, borderRadius: 6,
        }]
    },
    options: {
        responsive: true, plugins: { legend: { display: false } },
        scales: {
            x: { grid: { color: 'transparent' } },
            y: { grid: { color: C.border }, beginAtZero: true }
        }
    }
});

// 7. Activités (Radar)
new Chart(document.getElementById('chartActivites'), {
    type: 'radar',
    data: {
        labels: dataActivites.map(d => d.nom),
        datasets: [{
            label: 'Associations', data: dataActivites.map(d => d.total),
            borderColor: C.accent3, backgroundColor: 'rgba(255,107,107,.15)',
            pointBackgroundColor: C.accent3, borderWidth: 2,
        }]
    },
    options: {
        responsive: true, plugins: { legend: { display: false } },
        scales: {
            r: {
                grid: { color: C.border },
                pointLabels: { color: C.muted, font: { size: 11 } },
                ticks: { display: false }, beginAtZero: true,
            }
        }
    }
});
</script>
</body>
</html>