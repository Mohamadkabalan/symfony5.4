<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\ProductRepository;

class ProductsController extends AbstractController
{

  /**
   * @Route("/products", name="app_products")
   */
  public function index(ProductRepository $repo): Response
  {
    if (!$this->getUser()) {
      return $this->redirectToRoute('app_login');
    }
    $products=$repo->findBy([]);
    return $this->render('products/index.html.twig',['products'=>$products]);
  }

  /**
   * @Route("/products/{id}")
   */
  public function details($id,Request $request,ProductRepository $repo,SessionInterface $session): Response
  {
    $product=$repo->find($id);
    if($product ===null){
      throw $this->createNotFoundException("The product Does not Exist");
    }
    //add to basket handling
    $basket=$session->get('basket',[]);

    if($request->isMethod('POST')){
      $basket[$product->getId()]=$product;
      $session->set('basket',$basket);
    }
    $isInBasket=array_key_exists($product->getId(),$basket);

    return $this->render('products/details.html.twig',['product'=>$product,"inBasket" => $isInBasket]);
  }
}
