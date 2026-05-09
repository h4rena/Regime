<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriPlan — Portefeuille</title>
  <link rel="stylesheet" href="<?= base_url('css/style.css') ?>" />
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet" />
</head>

<body>

  <nav class="navbar">
    <div class="nav-inner">
      <div class="nav-logo"><span class="logo-dot"></span>NutriPlan</div>
      <ul class="nav-links">
        <li><a href="index.html" class="nav-link">Accueil</a></li>
        <li><a href="regimes.html" class="nav-link">Régimes</a></li>
        <li><a href="profil.html" class="nav-link">Mon profil</a></li>
        <li><a href="wallet.html" class="nav-link active">Portefeuille</a></li>
      </ul>
      <div class="nav-actions">
        <span class="nav-user-name">Ravo A.</span>
        <a href="login.html" class="btn-nav-ghost">Déconnexion</a>
      </div>
    </div>
  </nav>

  <main class="page-main">
    <div class="container container-sm">

      <h1 class="page-h1">Mon portefeuille</h1>
      <p class="page-h1-sub">Gérez votre solde et vos codes de recharge</p>

      <!-- BALANCE CARD -->
      <div class="wallet-balance-card">
        <div class="wbc-bg1"></div>
        <div class="wbc-bg2"></div>
        <div class="wbc-content">
          <div>
            <p class="wbc-label">Solde disponible</p>
            <p class="wbc-amount" id="solde"><?= $wallets['montant'] ?? '0' ?> Ar</p>
            <p class="wbc-sub">Mis à jour aujourd'hui</p>
          </div>
          <div class="wbc-icon">
            <svg width="32" height="32" fill="none" stroke="rgba(255,255,255,0.6)" stroke-width="1.5" viewBox="0 0 24 24">
              <rect x="1" y="4" width="22" height="16" rx="2" />
              <path d="M1 10h22" />
            </svg>
          </div>
        </div>
      </div>

      <!-- GOLD OPTION -->
      <div class="gold-upgrade-card">
        <div class="guc-left">
          <span class="guc-star">★</span>
          <div>
            <p class="guc-title">Passer à l'Option Gold</p>
            <p class="guc-desc">Obtenez 15% de remise sur tous les régimes. Paiement unique.</p>
          </div>
        </div>
        <button class="btn-gold">50 000 Ar — Activer</button>
      </div>

      <!-- CODE INPUT -->
      <div class="card mt-24">
        <div class="card-header-blue">
          <h2 class="card-title-white">Recharger avec un code</h2>
        </div>
        <div class="card-body">
          <p class="form-help">Entrez le code de recharge reçu pour créditer votre portefeuille.</p>
          <div class="code-input-group">
            <input type="text" class="form-input code-input-field" id="codeInput" placeholder="NUT-2026-XXXX" maxlength="16" />
            <button class="btn-primary-sm" onclick="validerCode()">Valider</button>
          </div>
          <div id="codeMessage" class="code-message" style="display:none"></div>
        </div>
      </div>



      <!-- TRANSACTIONS -->
      <div class="card mt-16">
        <div class="card-header-blue">
          <h2 class="card-title-white">Historique des transactions</h2>
        </div>
        <div class="card-body p0">
          <table class="data-table">
            <thead>
              <tr>
                <th>Date</th>
                <th>Description</th>
                <th>Montant</th>
                <th>Solde</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="td-muted">05/05/2026</td>
                <td>Code NUT-2026-B3</td>
                <td class="td-green">+10 000 Ar</td>
                <td>35 000 Ar</td>
              </tr>
              <tr>
                <td class="td-muted">02/05/2026</td>
                <td>Régime Équilibré Marin</td>
                <td class="td-red">−28 000 Ar</td>
                <td>25 000 Ar</td>
              </tr>
              <tr>
                <td class="td-muted">28/04/2026</td>
                <td>Code NUT-2026-A1</td>
                <td class="td-green">+5 000 Ar</td>
                <td>53 000 Ar</td>
              </tr>
              <tr>
                <td class="td-muted">20/04/2026</td>
                <td>Code NUT-2026-D9</td>
                <td class="td-green">+50 000 Ar</td>
                <td>48 000 Ar</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </main>

  <script>
    function validerCode() {

      const codeInput = document.getElementById('codeInput');

      const code = codeInput.value.trim();

      const messageDiv = document.getElementById('codeMessage');

      // Vérification champ vide
      if (code === '') {

        messageDiv.style.display = 'block';
        messageDiv.style.color = 'red';
        messageDiv.textContent = 'Veuillez entrer un code.';

        return;
      }

      fetch('/wallet/ajouter', {

          method: 'POST',

          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
          },

          body: 'code=' + encodeURIComponent(code)

        })

        .then(response => response.json())

        .then(data => {

          messageDiv.style.display = 'block';

          messageDiv.textContent = data.message;

          // Succès
          if (data.status === 'success') {

            messageDiv.style.color = 'green';

            // Mise à jour automatique du solde
            document.getElementById('solde').textContent =
              data.solde + ' Ar';

            // vider input
            codeInput.value = '';

          }

          // Erreur
          else {

            messageDiv.style.color = 'red';
          }

        })

        .catch(error => {

          console.error('Erreur :', error);

          messageDiv.style.display = 'block';

          messageDiv.style.color = 'red';

          messageDiv.textContent =
            'Une erreur est survenue.';
        });
    }
  </script>
</body>

</html>