<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContentController extends AbstractController
{
    /**
     * @Route("/home", name="app_homepage")
     */
    public function index(): Response {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        foreach ($categories as $category) {
            if($category->getParentId()!== null) {
                $childCategories[$category->getParentId()] = $this->getDoctrine()->getRepository(Category::class)->findByParentId($category->getParentId());
            }
        }
        $products = $this->getDoctrine()->getRepository(Product::class)->getPromotions();
        return $this->render('content/homepage.html.twig', [
            'controller_name' => 'ContentController',
            'categories' => $categories,
            'childCategories' => $childCategories,
            'products' => $products,
        ]);
    }

}
