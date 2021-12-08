<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/category")
 */
class CategoryController extends AbstractController
{
    /**
     * Method to get all the categories
     *
     * @param CategoryRepository $categoryRepository
     * @return Response
     */
    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/", name="category_index", methods={"GET"})
     */
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();
        foreach ($categories as $category) {
            if($category->getParentId()!== null) {
                $childCategories[$category->getParentId()] = $this->getDoctrine()->getRepository(Category::class)->findByParentId($category->getParentId());
            }
        }
        return $this->render('category/index.html.twig', [
            'categories' => $categories,
            'childCategories' => $childCategories,
        ]);
    }

    /**
     * Method to create a new category
     *
     * @param Request $request
     * @return Response
     */
    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/new", name="category_new", methods={"GET","POST"})
     */
    public function new(Request $request, CategoryRepository $categoryRepository): Response
    {   
        $categories = $categoryRepository->findAll();
        foreach ($categories as $category) {
            if($category->getParentId()!== null) {
                $childCategories[$category->getParentId()] = $this->getDoctrine()->getRepository(Category::class)->findByParentId($category->getParentId());
            }
        }
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('category/new.html.twig', [
            'category' => $category,
            'categories' => $categories,
            'form' => $form,
            'childCategories' => $childCategories,
        ]);
    }

    /**
     * Method to get one category
     *
     * @param Category $category
     * @return Response
     */
    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/{id}", name="category_show", methods={"GET"})
     */
    public function show(Category $category ): Response
    {   
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        foreach ($categories as $category) {
            if($category->getParentId()!== null) {
                $childCategories[$category->getParentId()] = $this->getDoctrine()->getRepository(Category::class)->findByParentId($category->getParentId());
            }
        }
        return $this->render('category/show.html.twig', [
            'category' => $category,
            'categories' => $categories,
            'childCategories' => $childCategories,
        ]);
    }

    /**
     * Method to update a category
     *
     * @param Request $request
     * @param Category $category
     * @return Response
     */
    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/{id}/edit", name="category_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Category $category): Response
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        foreach ($categories as $category) {
            if($category->getParentId()!== null) {
                $childCategories[$category->getParentId()] = $this->getDoctrine()->getRepository(Category::class)->findByParentId($category->getParentId());
            }
        }
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('category/edit.html.twig', [
            'categories' => $categories,
            'category' => $category,
            'form' => $form,
            'childCategories' => $childCategories,
        ]);
    }

    /**
     * Method to delete a category
     *
     * @param Request $request
     * @param Category $category
     * @return Response
     */
    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/{id}", name="category_delete", methods={"POST"})
     */
    public function delete(Request $request, Category $category): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($category);
            $entityManager->flush();
        }

        return $this->redirectToRoute('category_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Method to get all the products associated with a category
     *
     * @param Category $category
     * @return Response
     */
    /**
     * @Route("/{id}/products", name="catalog")
     */
    public function getProductByCategory(Category $category): Response
    {
        $products = $category->getProducts();
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        foreach ($categories as $category) {
            if($category->getParentId()!== null) {
                $childCategories[$category->getParentId()] = $this->getDoctrine()->getRepository(Category::class)->findByParentId($category->getParentId());
            }
        }
        return $this->render('product/catalog.html.twig', [
            'products'=>$products,
            'categories' =>$categories,
            'category' =>$category,
            'childCategories' => $childCategories,
        ]);
    }
}
