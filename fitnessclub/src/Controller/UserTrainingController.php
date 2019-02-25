<?php

namespace App\Controller;

use App\Entity\GroupTraining;
use App\Entity\NotificationType;
use App\Entity\User;
use App\Entity\UserTraining;
use Symfony\Component\HttpFoundation\Request;
use Exception;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class UserTrainingController extends AbstractController
{
    /**
     * @Route("/profile/subscribe/{id}")
     */

    public function subscribe($id = null, Request $request){
        try {
            new ReflectionClass('ReflectionClass' . ((int)$id . "" !== $id));
        } catch (Exception $e) {
            throw new NotFoundHttpException("Wrong input data");
        }

        $training = $this->getDoctrine()->getRepository(GroupTraining::class)->find((int)$id);

        var_dump(sizeof($this->getUser()->getUserTrainings()));

        foreach ($this->getUser()->getUserTrainings() as $userTraining){
            if($training === $userTraining->getGroupTraining()){
                return $this->redirectToRoute('viewUserGroupTraining',['id'=>$userTraining->getId()]);
            }
        }

        if($training && $training->getIsExpired() == false){
            $notification_types_repository    = $this->getDoctrine()->getRepository(NotificationType::class);
            $user_training = new UserTraining();
            $user_training->setUser($this->getUser());
            $user_training->setGroupTraining($training);

            $form = $this->getSubscribeForm($user_training);
            $form->add('subscribe', SubmitType::class, ['label' => 'Subscribe', 'attr'=>['class' => 'btn btn-info btn-md', 'style' => 'float:right']]);

            $form->handleRequest($request);
            if ($request->isMethod('POST')) {
                if ($form->isSubmitted() && $form->isValid()) {
                    $entityManager = $this->getDoctrine()->getManager();
                    $user_training->setIsDeleted(false);
                    $entityManager->persist($user_training);
                    $entityManager->flush();

                    return $this->redirectToRoute('profile');
                }
            }



            return $this->render('user_training/subscribe.html.twig', [
                'form' => $form->createView(),
                'user' => $this->getUser(),
                'training'=>$training
            ]);


        }
        throw new NotFoundHttpException("Something went wrong");
    }

    public function view($id){
        try {
            new ReflectionClass('ReflectionClass' . ((int)$id . "" !== $id));
        } catch (Exception $e) {
            throw new NotFoundHttpException("Wrong input data");
        }

        $user_training = $this->getDoctrine()->getRepository(UserTraining::class)->find((int)$id);

        if($user_training && $user_training->getUser()->getId() === $this->getUser()->getId()){

            $form = $this->getSubscribeForm($user_training);
            $options = ['attr' => ['readonly' => true]];
            $form->add('notification_type', TextType::class,$options);


            return $this->render('user_training/view.html.twig', [
                'form' => $form->createView(),
                'user' => $this->getUser(),
                'training'=>$user_training->getGroupTraining(),
                'user_training'=>$user_training
            ]);


        }
        throw new NotFoundHttpException("Page not found");


    }

    public function edit($id, Request $request){
        try {
            new ReflectionClass('ReflectionClass' . ((int)$id . "" !== $id));
        } catch (Exception $e) {
            throw new NotFoundHttpException("Wrong input data");
        }

        $user_training = $this->getDoctrine()->getRepository(UserTraining::class)->find((int)$id);

        var_dump($user_training->getId());

        if($this->getUser()->getUserTrainings()->contains($user_training)){
            $notification_types_repository    = $this->getDoctrine()->getRepository(NotificationType::class);
            $training = $user_training->getGroupTraining();
            $form = $this->getSubscribeForm($user_training);
            $form->add('edit', SubmitType::class, ['label' => 'Edit', 'attr'=>['class' => 'btn btn-info btn-md', 'style' => 'float:right']]);

            $form->handleRequest($request);
            if ($request->isMethod('POST')) {
                if ($form->isSubmitted() && $form->isValid()) {
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($user_training);
                    $entityManager->flush();

                    return $this->redirectToRoute('profile');
                }
            }

            return $this->render('user_training/subscribe.html.twig', [
                'form' => $form->createView(),
                'user' => $this->getUser(),
                'training'=>$training
            ]);


        }
        throw new NotFoundHttpException("Something went wrong");
    }

    public function remove($id = null){
        try {
            new ReflectionClass('ReflectionClass' . ((int)$id . "" !== $id));
        } catch (Exception $e) {
            throw new NotFoundHttpException("Wrong input data");
        }
        $repository = $this->getDoctrine()->getRepository(UserTraining::class);
        $userTraining = $repository->find((int)$id);

        $entityManager = $this->getDoctrine()->getManager();
        $userTraining->setIsDeleted(true);
        $entityManager->persist($userTraining);
        $entityManager->flush();
        return $this->redirectToRoute('profile');

    }

    private function getSubscribeForm($user_training){
        $form = $this->createFormBuilder($user_training)
            ->add('notification_type')
            ->getForm();
        return $form;
    }
}
