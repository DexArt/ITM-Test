<?php

namespace App\Controller;

use App\Entity\GroupTraining;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile", name="profile")
     */
    public function index()
    {

        if(in_array('ROLE_ADMIN',$this->getUser()->getRoles())){
            return $this->redirectToRoute('admin');
        }

        $imageName = $this->getUser()->getImage();


        $groupTrainings = [];
        $user_trainings = $this->getUser()->getUserTrainings();
        foreach ($user_trainings as $user_training){
            if(!$user_training->getIsDeleted() && !$user_training->getGroupTraining()->getIsExpired()){
                $data = new stdClass();
                $data->user_training_id = $user_training->getId();
                $data->training = $user_training->getGroupTraining();
                array_push($groupTrainings,$data);
            }
        }

        return $this->render('profile/index.html.twig', [
            'group_trainings' => $groupTrainings,
            'imageName' =>$imageName
        ]);
    }
}
