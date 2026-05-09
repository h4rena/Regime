<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// ═══════════════════════════════════════════════════════
//  ROUTE ACCUEIL (par défaut)
// ═══════════════════════════════════════════════════════
$routes->get('/', 'Home::index');
$routes->get('/accueil', 'Home::index');
$routes->get('/profil', 'Home::profil', ['filter' => 'auth']);
$routes->get('/profil/pdf', 'Home::profilPdf', ['filter' => 'auth']);

// ═══════════════════════════════════════════════════════
//  ROUTES PUBLIQUES — aucun filtre requis
// ═══════════════════════════════════════════════════════

// Authentification
$routes->get('/login',           'AuthController::loginForm');
$routes->post('/login',          'AuthController::login');
$routes->get('/deconnexion',     'AuthController::logout');
$routes->post('/gold/activer',    'AuthController::activateGold');

// Back office
$adminFilters = ['auth', 'role:Admin'];
$routes->get('/admin', 'BackofficeController::index', ['filter' => $adminFilters]);
$routes->get('/backoffice', 'BackofficeController::index', ['filter' => $adminFilters]);

// Régimes CRUD
$routes->get('/admin/regimes', 'RegimeController::index', ['filter' => $adminFilters]);
$routes->get('/admin/regimes/create', 'RegimeController::create', ['filter' => $adminFilters]);
$routes->post('/admin/regimes/store', 'RegimeController::store', ['filter' => $adminFilters]);
$routes->get('/admin/regimes/edit/(:num)', 'RegimeController::edit/$1', ['filter' => $adminFilters]);
$routes->post('/admin/regimes/update/(:num)', 'RegimeController::update/$1', ['filter' => $adminFilters]);
$routes->get('/admin/regimes/delete/(:num)', 'RegimeController::delete/$1', ['filter' => $adminFilters]);

// Activités CRUD
$routes->get('/admin/activites', 'ActiviteController::index', ['filter' => $adminFilters]);
$routes->get('/admin/activites/create', 'ActiviteController::create', ['filter' => $adminFilters]);
$routes->post('/admin/activites/store', 'ActiviteController::store', ['filter' => $adminFilters]);
$routes->get('/admin/activites/edit/(:num)', 'ActiviteController::edit/$1', ['filter' => $adminFilters]);
$routes->post('/admin/activites/update/(:num)', 'ActiviteController::update/$1', ['filter' => $adminFilters]);
$routes->get('/admin/activites/delete/(:num)', 'ActiviteController::delete/$1', ['filter' => $adminFilters]);

// Paramètres CRUD
$routes->get('/admin/parametres', 'ParametreController::index', ['filter' => $adminFilters]);
$routes->get('/admin/parametres/create', 'ParametreController::create', ['filter' => $adminFilters]);
$routes->post('/admin/parametres/store', 'ParametreController::store', ['filter' => $adminFilters]);
$routes->get('/admin/parametres/edit/(:num)', 'ParametreController::edit/$1', ['filter' => $adminFilters]);
$routes->post('/admin/parametres/update/(:num)', 'ParametreController::update/$1', ['filter' => $adminFilters]);
$routes->get('/admin/parametres/delete/(:num)', 'ParametreController::delete/$1', ['filter' => $adminFilters]);

// Inscription — Étape 1 (informations personnelles)
$routes->get('/inscription',     'AuthController::registerForm');
$routes->post('/inscription',    'AuthController::register');

// Inscription — Étape 2 (informations de santé)
// Note : protégée uniquement par la vérification de la session 'inscription_step1'
// dans le contrôleur (pas par AuthFilter car l'utilisateur n'est pas encore créé).
$routes->get('/inscription/sante',  'AuthController::santeForm');
$routes->post('/inscription/sante', 'AuthController::sante');

// Inscription — Étape 3 (choix objectif)
$routes->get('/inscription/objectif',  'AuthController::objectifForm');
$routes->post('/inscription/objectif', 'AuthController::objectif');


//wallet
$routes->get('/wallet', 'WalletController::afficheWalletUser', ['filter' => 'auth']);
$routes->post('/wallet/ajouter', 'WalletController::ajouterMontant', ['filter' => 'auth']);
// Public regimes pages
$routes->get('/regimes', 'RegimeFrontend::index');
$routes->get('/regimes/(:num)', 'RegimeFrontend::show/$1');
$routes->post('/regimes/souscrire/(:num)', 'RegimeFrontend::subscribe/$1', ['filter' => 'auth']);
