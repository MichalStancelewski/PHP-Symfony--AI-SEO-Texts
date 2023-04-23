<?php

namespace App\Controller;

use App\Form\FormRequest;
use App\Form\Type\FormRequestType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/new', name: 'app_user_panel_new')]
    public function index(Request $request): Response
    {
        $formRequest = new FormRequest();

        $form = $this->createForm(FormRequestType::class, $formRequest);


        if ($form->isSubmitted() && $form->isValid()) {
            $formRequest = $form->getData();
            //TODO create new task & redirect with success msg
            return $this->redirectToRoute('form/new.html.twig', [
                //TODO redirect with failure msg
                'form' => $form,
            ]);
        }

        return $this->render('form/new.html.twig', [
            //TODO redirect with failure msg
            'form' => $form,
        ]);
    }
}
