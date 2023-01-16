<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

use Symfony\Component\Mailer\MailerInterfacel;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

use App\Entity\Order;
use App\Repository\ProductRepository;

class CheckoutController extends AbstractController
{

  /**
   * @Route("/checkout", name="app_checkout")
   */
  public function checkout(Request $request, ProductRepository $repo,SessionInterface $session,MailerInterface $mailer): Response
  {
    if (!$this->getUser()) {
      return $this->redirectToRoute('app_login');
    }
    $basket=$session->get('basket',[]);
    $total=array_sum(array_map(function($product){
      return $product->getPrice();
    },$basket));
    if(!$total){
      return $this->redirectToRoute('app_products');
    }
    $order= new Order;

    $form=$this->createFormBuilder($order)
      ->add('name',TextType::class)
      ->add('email',TextType::class)
      ->add('address',TextareaType::class)
      ->add('save',SubmitType::class,['label' => 'Confirm Order'])
      ->getForm();

    $form->handleRequest($request);
    if($form->isSubmitted() && $form->isValid()){
      $order=$form->getData();
      foreach ($basket as $product){
        $order->getProducts()->add($repo->find($product->getId()));
      }
      $entityManager= $this->getDoctrine()->getManager();
      $entityManager->persist($order);
      $entityManager->flush();

      $this->sendEmailConfirmation($order,$mailer);

      $session->set('basket',[]);
      return $this->render('checkout/confirmation.html.twig');
    }


    return $this->render('checkout/checkout.html.twig',['total'=>$total,'form'=>$form->createView()]);
  }

  private function sendEmailConfirmation(Order $order, MailerInterface $mailer){
    $email = (new TemplatedEmail())
      ->from('mkabalane@gmail.com')
      ->to(new Address($order->getEmail(), $order->getName()))
      ->subject('Order confirmation')
      ->htmlTemplate('emails/order.html.twig')
      ->context(['order' => $order]);

    $mailer->send($email);
  }

}
