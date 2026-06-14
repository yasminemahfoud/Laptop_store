<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    private ProductRepository $productRepository;
    private CategoryRepository $categoryRepository;
    private $entityManager;

    public function __construct(
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository,
        ManagerRegistry $doctrine
    ) {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->entityManager = $doctrine->getManager();
    }

    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'products' => $this->productRepository->findAll(),
            'categories' => $this->categoryRepository->findAll(),
        ]);
    }

    #[Route('/product/category/{id}', name: 'product_category')]
    public function categoryProduct(Category $category): Response
    {
        return $this->render('home/index.html.twig', [
            'products' => $category->getProducts(),
            'categories' => $this->categoryRepository->findAll(),
        ]);
    }
}