<?php
$user = $user ?? session()->get('user') ?? [];
$sante = $sante ?? [];
$sessionUser = session()->get('user') ?? [];
$isAdmin = (($sessionUser['role'] ?? $user['role'] ?? null) === 'Admin') || ((int) ($sessionUser['id_role'] ?? $user['id_role'] ?? 0) === 1);

$displayName = trim(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? ''));
$displayName = $displayName !== '' ? $displayName : 'Utilisateur';

$errors = session()->getFlashdata('errors') ?? [];
$error = session()->getFlashdata('error');
$success = session()->getFlashdata('success');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriPlan - Modifier mon profil</title>
  <link rel="stylesheet" href="<?= base_url('css/style.css') ?>" />
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet" />
</head>
<body>

  <nav class="navbar">
    <div class="nav-inner">
      <div class="nav-logo"><span class="logo-dot"></span>NutriPlan</div>
      <ul class="nav-links">
        <li><a href="<?= base_url('/') ?>" class="nav-link">Accueil</a></li>
        <li><a href="<?= base_url('/regimes') ?>" class="nav-link">Régimes</a></li>
        <li><a href="<?= base_url('/profil') ?>" class="nav-link active">Mon profil</a></li>
        <li><a href="<?= base_url('/wallet') ?>" class="nav-link">Portefeuille</a></li>
        <?php if ($isAdmin): ?>
          <li><a href="<?= base_url('/admin') ?>" class="nav-link">Admin</a></li>
          <li><a href="<?= base_url('/dashboard') ?>" class="nav-link">Dashboard</a></li>
        <?php endif; ?>
      </ul>
      <div class="nav-actions">
        <span class="nav-user-name"><?= esc($displayName) ?></span>
        <a href="<?= base_url('/deconnexion') ?>" class="btn-nav-ghost">Déconnexion</a>
      </div>
    </div>
  </nav>

  <main class="main-container">
    <section class="hero-profil-edit">
      <div class="container">
        <h1>Modifier mon profil</h1>
        <p>Mettez à jour vos informations personnelles et vos données de santé</p>
      </div>
    </section>

    <section class="edit-form-section">
      <div class="container">
        <?php if ($error): ?>
          <div class="alert alert-error">
            <?= esc($error) ?>
          </div>
        <?php endif; ?>

        <div class="form-card">
          <form method="post" action="<?= base_url('/profil/update') ?>" class="profil-edit-form">
            <?= csrf_field() ?>

            <div class="form-row">
              <div class="form-group">
                <label for="prenom">Prénom</label>
                <input 
                  type="text" 
                  id="prenom" 
                  name="prenom" 
                  value="<?= esc(old('prenom') ?? $user['prenom'] ?? '') ?>"
                  class="form-input <?= !empty($errors['prenom']) ? 'is-invalid' : '' ?>"
                  required
                />
                <?php if (!empty($errors['prenom'])): ?>
                  <span class="form-error"><?= esc($errors['prenom']) ?></span>
                <?php endif; ?>
              </div>

              <div class="form-group">
                <label for="nom">Nom</label>
                <input 
                  type="text" 
                  id="nom" 
                  name="nom" 
                  value="<?= esc(old('nom') ?? $user['nom'] ?? '') ?>"
                  class="form-input <?= !empty($errors['nom']) ? 'is-invalid' : '' ?>"
                  required
                />
                <?php if (!empty($errors['nom'])): ?>
                  <span class="form-error"><?= esc($errors['nom']) ?></span>
                <?php endif; ?>
              </div>
            </div>

            <div class="form-group">
              <label for="email">Email</label>
              <input 
                type="email" 
                id="email" 
                name="email" 
                value="<?= esc(old('email') ?? $user['email'] ?? '') ?>"
                class="form-input <?= !empty($errors['email']) ? 'is-invalid' : '' ?>"
                required
              />
              <?php if (!empty($errors['email'])): ?>
                <span class="form-error"><?= esc($errors['email']) ?></span>
              <?php endif; ?>
            </div>

            <h3 class="form-section-title">Données de santé</h3>

            <div class="form-row">
              <div class="form-group">
                <label for="poids">Poids (kg)</label>
                <input 
                  type="number" 
                  id="poids" 
                  name="poids" 
                  value="<?= esc(old('poids') ?? $sante['poids'] ?? '') ?>"
                  step="0.1"
                  min="10"
                  max="300"
                  class="form-input <?= !empty($errors['poids']) ? 'is-invalid' : '' ?>"
                  required
                />
                <?php if (!empty($errors['poids'])): ?>
                  <span class="form-error"><?= esc($errors['poids']) ?></span>
                <?php endif; ?>
              </div>

              <div class="form-group">
                <label for="taille">Taille (cm)</label>
                <input 
                  type="number" 
                  id="taille" 
                  name="taille" 
                  value="<?= esc(old('taille') ?? $sante['taille'] ?? '') ?>"
                  step="1"
                  min="100"
                  max="250"
                  class="form-input <?= !empty($errors['taille']) ? 'is-invalid' : '' ?>"
                  required
                />
                <?php if (!empty($errors['taille'])): ?>
                  <span class="form-error"><?= esc($errors['taille']) ?></span>
                <?php endif; ?>
              </div>
            </div>

            <div class="form-actions">
              <a href="<?= base_url('/profil') ?>" class="btn-outline">Annuler</a>
              <button type="submit" class="btn-primary">Sauvegarder les modifications</button>
            </div>
          </form>
        </div>
      </div>
    </section>
  </main>

  <style>
    .hero-profil-edit {
      background: linear-gradient(135deg, var(--blue-600) 0%, var(--blue-700) 100%);
      color: white;
      padding: 60px 20px;
      text-align: center;
      margin-bottom: 40px;
    }

    .hero-profil-edit h1 {
      font-size: 36px;
      font-weight: 700;
      margin: 0 0 10px 0;
      font-family: "Playfair Display", serif;
    }

    .hero-profil-edit p {
      font-size: 16px;
      opacity: 0.9;
      margin: 0;
    }

    .edit-form-section {
      padding: 40px 20px;
      background: #f8fafb;
      min-height: 60vh;
    }

    .form-card {
      background: white;
      border-radius: 12px;
      padding: 40px;
      max-width: 600px;
      margin: 0 auto;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .profil-edit-form {
      display: flex;
      flex-direction: column;
      gap: 24px;
    }

    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
    }

    .form-group {
      display: flex;
      flex-direction: column;
      gap: 6px;
    }

    .form-group label {
      font-size: 14px;
      font-weight: 500;
      color: var(--text-primary);
    }

    .form-input {
      padding: 10px 12px;
      border: 1px solid var(--border-color);
      border-radius: 6px;
      font-size: 14px;
      font-family: "DM Sans", sans-serif;
      transition: all 0.2s ease;
    }

    .form-input:focus {
      outline: none;
      border-color: var(--blue-600);
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .form-input.is-invalid {
      border-color: #ef4444;
    }

    .form-error {
      font-size: 12px;
      color: #ef4444;
      font-weight: 500;
    }

    .form-section-title {
      font-size: 16px;
      font-weight: 600;
      color: var(--text-primary);
      margin-top: 16px;
      margin-bottom: 0;
    }

    .form-actions {
      display: flex;
      gap: 12px;
      justify-content: center;
      margin-top: 32px;
    }

    .btn-primary, .btn-outline {
      padding: 10px 24px;
      border-radius: 6px;
      font-size: 14px;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.2s ease;
      text-decoration: none;
      display: inline-block;
      border: none;
    }

    .btn-primary {
      background: var(--blue-600);
      color: white;
    }

    .btn-primary:hover {
      background: var(--blue-700);
    }

    .btn-outline {
      background: transparent;
      color: var(--text-primary);
      border: 1px solid var(--border-color);
    }

    .btn-outline:hover {
      background: var(--bg-light);
    }

    .alert {
      padding: 12px 16px;
      border-radius: 6px;
      margin-bottom: 20px;
      font-size: 14px;
    }

    .alert-error {
      background: #fee;
      color: #c33;
      border: 1px solid #fcc;
    }

    @media (max-width: 600px) {
      .form-row {
        grid-template-columns: 1fr;
      }

      .form-card {
        padding: 24px;
      }

      .hero-profil-edit h1 {
        font-size: 24px;
      }

      .form-actions {
        flex-direction: column;
      }

      .btn-primary, .btn-outline {
        width: 100%;
      }
    }
  </style>

</body>
</html>
