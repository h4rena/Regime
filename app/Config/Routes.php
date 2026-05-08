<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// ═══════════════════════════════════════════════════════
//  ROUTE ACCUEIL (par défaut)
// ═══════════════════════════════════════════════════════
$routes->get('/', 'Home::index');
$routes->get('/accueil', 'Home::index');
$routes->get('/profil', 'Home::profil', ['filter' => 'auth']);

// ═══════════════════════════════════════════════════════
//  ROUTES PUBLIQUES — aucun filtre requis
// ═══════════════════════════════════════════════════════

// Authentification
$routes->get('/login',           'AuthController::loginForm');
$routes->post('/login',          'AuthController::login');
$routes->get('/deconnexion',     'AuthController::logout');
$routes->post('/gold/activer',    'AuthController::activateGold');

// Back office
$routes->get('/admin', 'BackofficeController::index', ['filter' => 'auth,role:Admin']);
$routes->get('/backoffice', 'BackofficeController::index', ['filter' => 'auth,role:Admin']);
// Backoffice
$routes->get('/admin', 'BackofficeController::index', ['filter' => 'auth,role:Admin']);

// Régimes CRUD
$routes->get('/admin/regimes', 'RegimeController::index', ['filter' => 'auth,role:Admin']);
$routes->get('/admin/regimes/create', 'RegimeController::create', ['filter' => 'auth,role:Admin']);
$routes->post('/admin/regimes/store', 'RegimeController::store', ['filter' => 'auth,role:Admin']);
$routes->get('/admin/regimes/edit/(:num)', 'RegimeController::edit/$1', ['filter' => 'auth,role:Admin']);
$routes->post('/admin/regimes/update/(:num)', 'RegimeController::update/$1', ['filter' => 'auth,role:Admin']);
$routes->get('/admin/regimes/delete/(:num)', 'RegimeController::delete/$1', ['filter' => 'auth,role:Admin']);

// Activités CRUD
$routes->get('/admin/activites', 'ActiviteController::index', ['filter' => 'auth,role:Admin']);
$routes->get('/admin/activites/create', 'ActiviteController::create', ['filter' => 'auth,role:Admin']);
$routes->post('/admin/activites/store', 'ActiviteController::store', ['filter' => 'auth,role:Admin']);
$routes->get('/admin/activites/edit/(:num)', 'ActiviteController::edit/$1', ['filter' => 'auth,role:Admin']);
$routes->post('/admin/activites/update/(:num)', 'ActiviteController::update/$1', ['filter' => 'auth,role:Admin']);
$routes->get('/admin/activites/delete/(:num)', 'ActiviteController::delete/$1', ['filter' => 'auth,role:Admin']);

// Paramètres CRUD
$routes->get('/admin/parametres', 'ParametreController::index', ['filter' => 'auth,role:Admin']);
$routes->get('/admin/parametres/create', 'ParametreController::create', ['filter' => 'auth,role:Admin']);
$routes->post('/admin/parametres/store', 'ParametreController::store', ['filter' => 'auth,role:Admin']);
$routes->get('/admin/parametres/edit/(:num)', 'ParametreController::edit/$1', ['filter' => 'auth,role:Admin']);
$routes->post('/admin/parametres/update/(:num)', 'ParametreController::update/$1', ['filter' => 'auth,role:Admin']);
$routes->get('/admin/parametres/delete/(:num)', 'ParametreController::delete/$1', ['filter' => 'auth,role:Admin']);

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

// Public regimes pages
$routes->get('/regimes', 'RegimeFrontend::index');
$routes->get('/regimes/(:num)', 'RegimeFrontend::show/$1');
