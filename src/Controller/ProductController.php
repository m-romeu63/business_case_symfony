<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Form\ProductType;
use App\Form\AddToCartType;
use App\Manager\CartManager;
use App\Services\FileUploader;
use App\Repository\ProductRepository;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;


/**
 * @Route("/product")
 */
class ProductController extends AbstractController
{

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/", name="product_index", methods={"GET"})
     */
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        foreach ($categories as $category) {
            if($category->getParentId()!== null) {
                $childCategories[$category->getParentId()] = $this->getDoctrine()->getRepository(Category::class)->findByParentId($category->getParentId());
            }
        }
        return $this->render('product/index.html.twig', [
            'products' => $products,
            'categories'=>$categories,
            'childCategories' => $childCategories,
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/new", name="product_new", methods={"GET","POST"})
     */
    public function new(Request $request, FileUploader $fileUploader): Response
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        foreach ($categories as $category) {
            if($category->getParentId()!== null) {
                $childCategories[$category->getParentId()] = $this->getDoctrine()->getRepository(Category::class)->findByParentId($category->getParentId());
            }
        }
        
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photo1 = $form->get('photo1')->getData();
            $photo2 = $form->get('photo2')->getData();
            $photo3 = $form->get('photo3')->getData();
            $entityManager = $this->getDoctrine()->getManager();
            if($photo1) {
                $photo1FileName = $fileUploader->uploadPicture($photo1);
                $product->setPhoto1($photo1FileName);
            } if($photo2) {
                $photo2FileName = $fileUploader->uploadPicture($photo2);
                $product->setPhoto2($photo2FileName);
            } if($photo3) {
                $photo3FileName = $fileUploader->uploadPicture($photo3);
                $product->setPhoto3($photo3FileName);
            }
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('product_index', [], Response::HTTP_SEE_OTHER);
            }
        else {
            return $this->renderForm('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
            'categories' => $categories,
            'childCategories' => $childCategories,
        ]);
        }
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/{id}", name="product_show", methods={"GET"})
     */
    public function show(Product $product): Response
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        foreach ($categories as $category) {
            if($category->getParentId()!== null) {
                $childCategories[$category->getParentId()] = $this->getDoctrine()->getRepository(Category::class)->findByParentId($category->getParentId());
            }
        }
        return $this->render('product/show.html.twig', [
            'product' => $product,
            'categories' => $categories,
            'childCategories' => $childCategories,
        ]);
    }

    /**
     * @Route("/{id}/detail", name="product_detail")
     */
    public function getDetail(Product $product, Request $request, CartManager $cartManager): Response
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        foreach ($categories as $category) {
            if($category->getParentId()!== null) {
                $childCategories[$category->getParentId()] = $this->getDoctrine()->getRepository(Category::class)->findByParentId($category->getParentId());
            }
        }

        $form = $this->createForm(AddToCartType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $item = $form->getData();
            $item->setProduct($product);

            $cart = $cartManager->getCurrentCart();
            $cart
                ->addItem($item)
                ->setUpdatedAt(new \DateTime());

            $cartManager->save($cart);

            return $this->redirectToRoute('product_detail', ['id' => $product->getId()]);
        }

        return $this->render('product/detail.html.twig', [
            'product' => $product,
            'categories' => $categories,
            'childCategories' => $childCategories,
            'form' => $form->createView()
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/{id}/edit", name="product_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Product $product, FileUploader $fileUploader): Response
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        foreach ($categories as $category) {
            if($category->getParentId()!== null) {
                $childCategories[$category->getParentId()] = $this->getDoctrine()->getRepository(Category::class)->findByParentId($category->getParentId());
            }
        }
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photo1 = $form->get('photo1')->getData();
            $photo2 = $form->get('photo2')->getData();
            $photo3 = $form->get('photo3')->getData();

            if($photo1) {
                $photo1FileName = $fileUploader->uploadPicture($photo1);
                $product->setPhoto1($photo1FileName);
            } if($photo2) {
                $photo2FileName = $fileUploader->uploadPicture($photo2);
                $product->setPhoto2($photo2FileName);
            } if($photo3) {
                $photo3FileName = $fileUploader->uploadPicture($photo3);
                $product->setPhoto3($photo3FileName);
            }
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('product_index', [], Response::HTTP_SEE_OTHER);
            }
        else {
            return $this->renderForm('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
            'categories' => $categories,
            'childCategories' => $childCategories,
        ]);
        }
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/{id}", name="product_delete", methods={"POST"})
     */
    public function delete(Request $request, Product $product): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $photo1 = $product->getPhoto1();
            $photo2 = $product->getPhoto2();
            $photo3 = $product->getPhoto3();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($product);
            $entityManager->flush();
            unlink('/uploads/images/'.$photo1);
            unlink('/uploads/images/'.$photo2);
            unlink('/uploads/images/'.$photo3);
        }

        return $this->redirectToRoute('product_index', [], Response::HTTP_SEE_OTHER);
    }

    
}