<?php

namespace App\Controller;

use App\Entity\GroupTraining;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="default")
     */
    public function home()
    {
        $repository = $this->getDoctrine()->getRepository(GroupTraining::class);
        $groupTrainings = $repository->findBy(['is_expired'=>false]);

        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
            'group_trainings' => $groupTrainings
        ]);
    }
}
