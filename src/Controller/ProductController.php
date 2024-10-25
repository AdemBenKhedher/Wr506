<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\SlugifyService ;
class ProductController extends AbstractController
{


    #[Route('/product', name: 'app_product')]
    public function index(SlugifyService $slugifiy): Response
    {
        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
            'slug' => $slugifiy->slugify('Hello World'),
            
        ]);
    }

    #[Route('/products', name: 'app_products')]
    public function listProducts(): Response
    {
        return $this->render('product/list.html.twig', [
            'title' => 'Liste des produits',
        ]);
    }

    #[Route('/product/{id}', name: 'app_product_id')]
    public function viewProduct(Request $request, int $id): Response
    {
        return $this->render('product/view.html.twig', [
            'id' => $id,
        ]);
    }
}