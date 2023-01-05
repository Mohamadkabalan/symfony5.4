<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

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
  public function details($id,ProductRepository $repo): Response
  {
    $product=$repo->find($id);
    if($product ===null){
      throw $this->createNotFoundException("The product Does not Exist");
    }
    return $this->render('products/details.html.twig',['product'=>$product]);
  }
}
