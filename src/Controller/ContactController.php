<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\Type\ContactType;
use Mailjet\Resources;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     */
    public function index(Request $request): Response
    {   
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        foreach ($categories as $category) {
            if($category->getParentId()!== null) {
                $childCategories[$category->getParentId()] = $this->getDoctrine()->getRepository(Category::class)->findByParentId($category->getParentId());
            }
        }
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $contactFormData = $form->getData();;
            $api_key= $this->getParameter('api_key');
            $api_key_private= $this->getParameter('api_key_private'); 
    
            $mj = new \Mailjet\Client($api_key,$api_key_private,true,['version' => 'v3.1']);
            $body = [
            'Messages' => [
                [
                'From' => [
                'Email' => $contactFormData['email'],
                'Name' => $contactFormData['nom'].' '.$contactFormData['prenom']
                ],
                'To' => [
                [
                    'Email' => "marine.romeu@hotmail.fr",
                    'Name' => "Marine"
                ]
                ],
                'Subject' => $contactFormData['sujet'],
                'TextPart' => $contactFormData['message'],
                'HTMLPart' => $contactFormData['message'],
                'CustomID' => "AppGettingStartedTest"
                ]
                ]
            ];
            $response = $mj->post(Resources::$Email, ['body' => $body]);
            $response->success();
            return $this->redirectToRoute('contact');
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
            'categories' => $categories,
            'childCategories' => $childCategories,
        ]);
    }
}
