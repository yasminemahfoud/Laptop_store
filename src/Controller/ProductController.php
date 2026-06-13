<?php

namespace App\Controller;
use App\Entity\Product;
use App\Form\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class ProductController extends AbstractController
{
      private $productRepository;
      private $entityManager;

      public function __construct(ProductRepository $productRepository,ManagerRegistry $doctrine)

      {
        $this->productRepository = $productRepository;
        $this->entityManager = $doctrine->getManager();
      }
     #[Route('/product', name: 'product_list')]
      public function index(): Response
      {
        return $this->render('product/index.html.twig', [
            'controller_name' => 'productController',
        ]);
      }
  
  #[Route('/store/product', name: 'product_store')]
    public function store(Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class,$product);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isvalid()){
             $product = $form->getData();
             if($request->files->get('product')['image']){
                $image = $request->files->get('product')['image'];
                $image_name = time().'_'.$image->getClientOriginalName();
                $image->move($this->getParameter('image_directory',$image_name));
                $product->setImage($image_name);
             }
             $this->entityManager->persist($product);
             $this->entityManager->flush();
             $this->addFash(
                'success',
                'your prodect was saved'
             );
             return $this->redirctToRoute('product_list');
        }
        return $this->render('product/create.html.twig', [
    'form' => $form->createView(),
]);
      
    }
}
