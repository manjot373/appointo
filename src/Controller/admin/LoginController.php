<?php

namespace App\Controller\admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/admin')]
class LoginController extends AbstractController
{

    #[Route('/login', name: 'admin_login')]
    function admin_login(AuthenticationUtils $authenticationUtils)
    {

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('admin/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    #[Route('/logout', name: 'admin_logout')]
    function admin_logout()
    {

        return null;
    }


    #[Route('/', name: 'admin_dashboard')]
    function admin_dashboard()
    {

        return $this->render('admin/dashboard.html.twig');
    }
}
