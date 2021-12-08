<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Customer;
use App\Form\CustomerType;
use App\Repository\CustomerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/customer")
 */
class CustomerController extends AbstractController
{
    /**
     * @Route("/", name="customer_index", methods={"GET"})
     */
    public function index(CustomerRepository $customerRepository): Response
    {
        return $this->render('customer/index.html.twig', [
            'customers' => $customerRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="customer_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        foreach ($categories as $category) {
            if($category->getParentId()!== null) {
                $childCategories[$category->getParentId()] = $this->getDoctrine()->getRepository(Category::class)->findByParentId($category->getParentId());
            }
        }
        $customer = new Customer();
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($customer);
            $entityManager->flush();

            return $this->redirectToRoute('customer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('customer/new.html.twig', [
            'customer' => $customer,
            'form' => $form,
            'categories'=>$categories,
            'childCategories' => $childCategories,
        ]);
    }

    /**
     * @Route("/{id}", name="customer_show", methods={"GET"})
     */
    public function show(Customer $customer): Response
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        foreach ($categories as $category) {
            if($category->getParentId()!== null) {
                $childCategories[$category->getParentId()] = $this->getDoctrine()->getRepository(Category::class)->findByParentId($category->getParentId());
            }
        }
        return $this->render('customer/show.html.twig', [
            'customer' => $customer,
            'categories'=>$categories,
            'childCategories' => $childCategories,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="customer_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Customer $customer): Response
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        foreach ($categories as $category) {
            if($category->getParentId()!== null) {
                $childCategories[$category->getParentId()] = $this->getDoctrine()->getRepository(Category::class)->findByParentId($category->getParentId());
            }
        }
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('customer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('customer/edit.html.twig', [
            'customer' => $customer,
            'form' => $form,
            'categories'=>$categories,
            'childCategories' => $childCategories,
        ]);
    }

    /**
     * @Route("/{id}", name="customer_delete", methods={"POST"})
     */
    public function delete(Request $request, Customer $customer): Response
    {
        if ($this->isCsrfTokenValid('delete'.$customer->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($customer);
            $entityManager->flush();
        }

        return $this->redirectToRoute('customer_index', [], Response::HTTP_SEE_OTHER);
    }
}
