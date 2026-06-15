<?php

namespace App\Controller;
use App\Entity\Product;
use App\Entity\Order;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Persistence\ManagerRegistry;

final class OrderController extends AbstractController
{
    private OrderRepository $orderRepository;
    private $entityManager;

    public function __construct(
       OrderRepository $orderRepository,
        ManagerRegistry $doctrine
    ) {
        $this->orderRepository = $orderRepository;
        $this->entityManager = $doctrine->getManager();
    }
    
        
    
     #[Route('/order', name: 'order')]
    public function index(): Response
    {
        return $this->render('order/index.html.twig', [
            'controller_name' => 'OrderController',
        ]);
    }
    #[Route('/user/orders', name: 'user_order_list')]
    public function userOrders(): Response
    {
          if(!$this->getUser()){
          return $this->redirectToRoute('app_login');
        }
        return $this->render('order/user.html.twig', [
            'user' => $this->getUser(),
        ]);
        }
     #[Route('/store/order/{product}', name: 'order_store')]
    public function store(Product $product): Response
    {
        if(!$this->getUser()){
          return $this->redirectToRoute('app_login');
        }
        $order = new Order();
        $order->setPname($product->getName());
        $order->setPrice($product->getPrice());
        $order->setStatus('processing...');
        $order->setUser($this->getUser());

            $this->entityManager->persist($order);
            $this->entityManager->flush();

            $this->addFlash('success', 'Your order was saved successfully.');

            return $this->redirectToRoute('user_order_list');
        }

      
    }

