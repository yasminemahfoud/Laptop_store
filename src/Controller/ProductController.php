<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Filesystem\Filesystem;

final class ProductController extends AbstractController
{
    private ProductRepository $productRepository;
    private $entityManager;

    public function __construct(
        ProductRepository $productRepository,
        ManagerRegistry $doctrine
    ) {
        $this->productRepository = $productRepository;
        $this->entityManager = $doctrine->getManager();
    }

    #[Route('/product', name: 'product_list')]
    public function index(): Response
    {
        $products = $this->productRepository->findAll();

        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/store/product', name: 'product_store')]
    public function store(Request $request): Response
    {
        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $image = $form->get('image')->getData();

            if ($image) {
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(
                    $this->getParameter('image_directory'),
                    $imageName
                );
                $product->setImage($imageName);
            }

            $this->entityManager->persist($product);
            $this->entityManager->flush();

            $this->addFlash('success', 'Your product was saved successfully.');

            return $this->redirectToRoute('product_list');
        }

        return $this->render('product/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

   #[Route('/product/details/{id}', name: 'product_show')]
public function show(Product $product): Response
{
    return $this->render('product/show.html.twig', [
        'product' => $product,
    ]);
}

    #[Route('/product/edit/{id}', name: 'product_edit')]
    public function editproduct(Product $product, Request $request): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $image = $form->get('image')->getData();

            if ($image) {
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(
                    $this->getParameter('image_directory'),
                    $imageName
                );
                $product->setImage($imageName);
            }

            $this->entityManager->persist($product);
            $this->entityManager->flush();

            $this->addFlash('success', 'Your product updated.');

            return $this->redirectToRoute('product_list');
        }

        return $this->render('product/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/product/delete/{id}', name: 'product_delete')]
    public function delete(Product $product): Response
    {
        $filesystem = new Filesystem();
        $imagePath = $this->getParameter('kernel.project_dir') . '/public/uploads/' . $product->getImage();

        if ($filesystem->exists($imagePath)) {
            $filesystem->remove($imagePath);
        }

        $this->entityManager->remove($product);
        $this->entityManager->flush();

        $this->addFlash('success', 'Your product removed.');

        return $this->redirectToRoute('product_list');
    }
}