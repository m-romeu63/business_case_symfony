<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\Type\SavType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Mailjet\Resources;

class SavController extends AbstractController
{
    /**
     * @Route("/sav", name="sav")
     */
    public function index(Request $request): Response
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        foreach ($categories as $category) {
            if($category->getParentId()!== null) {
                $childCategories[$category->getParentId()] = $this->getDoctrine()->getRepository(Category::class)->findByParentId($category->getParentId());
            }
        }
        $form = $this->createForm(SavType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $savFormData = $form->getData();
            $api_key='959f5aa5b41cc64ebe9a3ce31f88338f';
            $api_key_private='3c1dae6fdc9391994227f15121536d48'; 
    
            $mj = new \Mailjet\Client($api_key,$api_key_private,true,['version' => 'v3.1']);
            $body = [
            'Messages' => [
                [
                'From' => [
                'Email' => $savFormData['email'],
                'Name' => $savFormData['nom'].' '.$savFormData['prenom']
                ],
                'To' => [
                [
                    'Email' => "romeumarine@gmail.com",
                    'Name' => "Marine"
                ]
                ],
                'Subject' => "Demande de SAV : ".$savFormData['order'].' - '.$savFormData['motif'],
                'TextPart' => $savFormData['message'],
                'HTMLPart' => $savFormData['message'],
                'CustomID' => "AppGettingStartedTest"
                ]
                ]
            ];
            $response = $mj->post(Resources::$Email, ['body' => $body]);
            $response->success();
            return $this->redirectToRoute('sav');
        }

        return $this->render('sav/index.html.twig', [
            'form' => $form->createView(),
            'categories' => $categories,
            'childCategories' => $childCategories,
        ]);
    }
}
