<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class HomeController extends AbstractController
{
  /**
   * @Route("/home", name="app_home")
   */
  public function index(): Response
  {
     if (!$this->getUser()) {
         return $this->redirectToRoute('app_login');
     }
    return $this->render('home/index.html.twig');
  }

}
