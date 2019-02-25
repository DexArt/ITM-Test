<?php

namespace App\Controller;

use App\Entity\GroupTraining;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index()
    {
        $repository = $this->getDoctrine()->getRepository(GroupTraining::class);
        $groupTrainings = $repository->findBy(['is_expired'=>false]);

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'group_trainings' =>$groupTrainings
        ]);
    }
}
