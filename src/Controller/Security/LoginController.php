<?php

namespace App\Controller\Security;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class LoginController extends AbstractController
{

    #[Route('/admin/login', name: 'admin_login')]
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

    #[Route('/admin/logout', name: 'admin_logout')]
    function admin_logout()
    {

        return null;
    }


    #[Route('/', name: 'admin_dashboard')]
    function admin_dashboard()
    {

        return $this->render('admin/dashboard.html.twig');
    }

     #[Route('/staff', name: 'staff_dashboard')]
    function staff_dashboard()
    {

        return $this->render('business/staff/dashboard.html.twig');
    }


    #[Route('/business/login', name: 'business_login')]
    function business_login(AuthenticationUtils $authenticationUtils)
    {

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/business_login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    #[Route('/business/logout', name: 'business_logout')]
    function business_logout()
    {

        return null;
    }

     #[Route('/business/dashboard', name: 'business_dashboard')]
    function business_dashboard()
    {

        return $this->render('business/dashboard.html.twig');
    }

    #[Route('/staff/login', name: 'staff_login')]
    function staff_login(AuthenticationUtils $authenticationUtils)
    {

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/staff_login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    #[Route('/staff/logout', name: 'staff_logout')]
    function staff_logout()
    {

        return null;
    }
}
