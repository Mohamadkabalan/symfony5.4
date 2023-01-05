<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\ProductRepository;

class BasketController extends AbstractController
{

  /**
   * @Route("/basket", name="app_basket")
   */
  public function basket(Request $request,SessionInterface $session): Response
  {
    if (!$this->getUser()) {
      return $this->redirectToRoute('app_login');
    }
    $basket=$session->get('basket',[]);
    if($request->isMethod('POST')){
      unset($basket[$request->request->get('id')]);
      $session->set('basket',$basket);
    }
    $total=array_sum(array_map(function($product){
      return $product->getPrice();
    },$basket));
    return $this->render('basket/basket.html.twig',['basket'=>$basket,'total'=>$total]);
  }

}
