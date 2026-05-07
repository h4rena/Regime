<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriPlan — Accueil</title>
  <link rel="stylesheet" href="<?= base_url('css/style.css') ?>" />
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet" />
</head>
<body>

  <!-- NAVBAR -->
  <nav class="navbar">
    <div class="nav-inner">
      <div class="nav-logo">
        <span class="logo-dot"></span>
        NutriPlan
      </div>
      <ul class="nav-links">
        <li><a href="<?= base_url('/') ?>" class="nav-link active">Accueil</a></li>
        <li><a href="<?= base_url('/regimes') ?>" class="nav-link">Régimes</a></li>
        <li><a href="<?= base_url('/profil') ?>" class="nav-link">Mon profil</a></li>
        <li><a href="<?= base_url('/wallet') ?>" class="nav-link">Portefeuille</a></li>
      </ul>
      <div class="nav-actions">
        <a href="<?= base_url('/login') ?>" class="btn-nav-ghost">Connexion</a>
        <a href="<?= base_url('/inscription') ?>" class="btn-nav">S'inscrire</a>
      </div>
    </div>
  </nav>

  <!-- HERO -->
  <section class="hero">
    <div class="hero-bg-circle c1"></div>
    <div class="hero-bg-circle c2"></div>
    <div class="container">
      <div class="hero-content">
        <span class="hero-badge">✦ Nutrition personnalisée</span>
        <h1 class="hero-title">Votre régime,<br /><em>adapté à vous.</em></h1>
        <p class="hero-desc">Entrez vos informations, calculez votre IMC et découvrez les régimes alimentaires et activités sportives parfaitement adaptés à vos objectifs.</p>
        <div class="hero-cta">
          <a href="<?= base_url('/inscription') ?>" class="btn-primary-lg">Commencer gratuitement</a>
          <a href="<?= base_url('/regimes') ?>" class="btn-outline-lg">Voir les régimes</a>
        </div>
        <div class="hero-stats">
          <div class="hstat"><span class="hstat-val">5+</span><span class="hstat-lbl">Régimes</span></div>
          <div class="hstat-divider"></div>
          <div class="hstat"><span class="hstat-val">5+</span><span class="hstat-lbl">Activités</span></div>
          <div class="hstat-divider"></div>
          <div class="hstat"><span class="hstat-val">Gold</span><span class="hstat-lbl">−15% remise</span></div>
        </div>
      </div>
      <div class="hero-visual">
        <div class="imc-demo-card">
          <p class="idc-label">Votre IMC</p>
          <div class="idc-circle">
            <span class="idc-val">22.4</span>
            <span class="idc-unit">kg/m²</span>
          </div>
          <div class="idc-bar">
            <div class="idc-fill"></div>
            <div class="idc-marker"></div>
          </div>
          <div class="idc-range">
            <span>Maigreur</span><span>Normal</span><span>Obésité</span>
          </div>
          <div class="idc-status">
            <span class="badge badge-success">Poids normal</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- FEATURES -->
  <section class="features-section">
    <div class="container">
      <p class="section-eyebrow">Comment ça marche</p>
      <h2 class="section-title">Simple, rapide, efficace</h2>
      <div class="features-grid">
        <div class="feat-card">
          <div class="feat-icon-wrap">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          </div>
          <h3 class="feat-title">Créez votre profil</h3>
          <p class="feat-desc">Renseignez genre, taille, poids et obtenez votre IMC instantanément.</p>
        </div>
        <div class="feat-card">
          <div class="feat-icon-wrap">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
          </div>
          <h3 class="feat-title">Choisissez un objectif</h3>
          <p class="feat-desc">Augmenter, réduire ou stabiliser votre poids selon votre IMC idéal.</p>
        </div>
        <div class="feat-card">
          <div class="feat-icon-wrap">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="2"/><path d="M9 12h6M9 16h4"/></svg>
          </div>
          <h3 class="feat-title">Recevez votre programme</h3>
          <p class="feat-desc">Régime alimentaire + activités sportives sur mesure, exportables en PDF.</p>
        </div>
        <div class="feat-card feat-card-gold">
          <div class="gold-ribbon">GOLD</div>
          <div class="feat-icon-wrap gold-icon">
            <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
          </div>
          <h3 class="feat-title">Accès Premium</h3>
          <p class="feat-desc">Débloquez -15% de remise sur tous les régimes avec Gold.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- FOOTER -->
  <footer class="footer">
    <div class="container">
      <div class="footer-inner">
        <div class="footer-logo">
          <span class="logo-dot sm"></span>
          NutriPlan
        </div>
        <p class="footer-copy">© 2024 NutriPlan. Tous droits réservés.</p>
      </div>
    </div>
  </footer>

</body>
</html>