<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

/**
 * RoleFilter
 *
 * Vérifie que l'utilisateur connecté possède l'un des rôles
 * passés en argument depuis la définition de route.
 *
 * Exemple d'usage dans Routes.php :
 *   filter' => 'role:admin'
 *   'filter' => 'role:admin,bibliothecaire'
 */
class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $user    = $session->get('user');

        if (! $user || ! in_array($user['role'], $arguments ?? [])) {
            return redirect()
                ->to('/')
                ->with('erreur', 'Accès refusé : droits insuffisants.');
        }
    }

    public function after(
        RequestInterface  $request,
        ResponseInterface $response,
        $arguments = null
    ) {
        // Rien à faire après
    }
}