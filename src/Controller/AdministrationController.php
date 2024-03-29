<?php

namespace App\Controller;

use App\Entity\Simulator;
use App\Service\Tipico\Content\SimulatorFavoriteList\Data\AddSimulatorToListData;
use App\Service\Tipico\Content\SimulatorFavoriteList\Data\AddSimulatorToListType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/administration')]
class AdministrationController extends AbstractController
{
    #[\Symfony\Component\Routing\Annotation\Route('/add/{simulator}', name: 'app_administration_add')]
    public function createClientAction(
        Request $request,
        Simulator $simulator,
    ): Response {
        $data = new AddSimulatorToListData();
        $data->setSimulator($simulator);

        $form = $this->createForm(AddSimulatorToListType::class, $data, ['simulator' => $simulator]);;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $form->getData();

            dd("Admin Controller");
            return $this->redirectToRoute(
                'app_tipico_simulation_detail',
                ['simulator' => $simulator->getId()]
            );
        }

        return $this->render(
            'administration/add.html.twig',
            [
                'simulator' => $simulator,
                'data' => $data,
                'form' => $form,
                'formAction' => $this->generateUrl('app_administration_add', ['simulator' => $simulator->getId()]),
            ]
        );

//        return $this->renderAjaxForm(
//            'administration/add.html.twig',
//            [
//                'form' => $form->createView(),
//                'simulator' => $simulator,
//                'data' => $form->getData(),
//                'formAction' => $this->generateUrl('app_administration_add', ['simulator' => $simulator->getId()]),
//            ],
//            $form,
//            $request,
//            'administration/live_component.administration.add.form.html.twig',
//        );
    }

    protected function renderAjaxForm(
        string $formTemplate,
        array $data,
        FormInterface $form,
        Request $request,
        string $ajaxTemplate = 'layout/_form.html.twig'
    ): Response {
        return $this->render(
            $request->isXmlHttpRequest() ? $ajaxTemplate : $formTemplate,
            $data,
            new Response(
                null,
                $form->isSubmitted() && !$form->isValid() ? 422 : 200,
            )
        );
    }
}
