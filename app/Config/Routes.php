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
