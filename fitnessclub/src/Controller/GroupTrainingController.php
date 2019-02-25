<?php

namespace App\Controller;

use App\Entity\GroupTraining;
use App\Entity\NotificationType;
use App\Entity\User;
use App\Entity\UserTraining;
use App\Mailer\RabbitMqSpool;
use App\Producer\MailSenderProducer;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use phpDocumentor\Reflection\Types\String_;
use ReflectionClass;
use stdClass;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class GroupTrainingController extends AbstractController
{
    private $producer;

    public function __construct(EntityManagerInterface $entityManager, MailSenderProducer $producer)
    {
        $this->producer = $producer;
    }

    /**
     * @Route("/admin/grouptraining/add")
     */
    public function add(Request $request)
    {

        $groupTraining = new GroupTraining();
        $form          = $this->getGroupTrainingForm($groupTraining);
        $form->add('add', SubmitType::class, ['label' => 'Add', 'attr' => ['class' => 'btn btn-info btn-md', 'style' => 'float:right']]);
        $form->handleRequest($request);
        if ($request->isMethod('POST')) {
            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $groupTraining->setIsExpired(false);
                $entityManager->persist($groupTraining);
                $entityManager->flush();
                return $this->redirectToRoute('addGroupTraining');
            }
        }
        return $this->render('group_training/addTraining.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/grouptraining/view/{id}")
     */
    public function view($id = null, Request $request)
    {

        try {
            new ReflectionClass('ReflectionClass' . ((int)$id . "" !== $id));
        } catch (Exception $e) {
            throw new NotFoundHttpException("Wrong input data");
        }
        $training = $this->getDoctrine()->getRepository(GroupTraining::class)->find((int)$id);
        if (!$training) {
            throw new NotFoundHttpException("Page Not Found");
        }
        if ($this->getUser() && in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {

            $entityManager                 = $this->getDoctrine()->getManager();
            $notification_types_repository = $this->getDoctrine()->getRepository(NotificationType::class);
            $notification_types            = $notification_types_repository->findAll();
            $notification_form             = $this->getNotificationForm($notification_types);
            $notification_form->handleRequest($request);
            if ($request->isMethod('POST')) {
                $users = $this->getUsersByGroupTrainingId((int)$id);
                $this->producer->setContentType('application/json');
                foreach ($users as $user) {
                    $birtday     = $user->getBirthday();
                    $messageBody = '';
                    if ($user->notification === 'phone') {
                        $messageBody = $notification_form->getData()['phone_message'];
                    } elseif ($user->notification === 'email') {
                        $messageBody = $notification_form->getData()['email_message'];
                    }
                    $message            = new stdClass();
                    $message->type      = $user->notification;
                    $message->firstname = $user->getFirstname();
                    $message->lastname  = $user->getLastname();
                    $message->birthday  = $birtday->format('Y-m-d');
                    $message->email     = $user->getEmail();
                    $message->phone     = $user->getPhone();
                    $message->message   = $messageBody;


                    $jsonMessage = serialize($message);
                    $this->producer->publish($jsonMessage);
                }

                return $this->redirectToRoute('showGroupTraining', ['id' => $id]);

            }
            return $this->render('group_training/adminView.html.twig', [
                'training' => $training,
                'notification_types' => $notification_types,
                'form' => $notification_form->createView()
            ]);
        } elseif ($this->getUser() && in_array('ROLE_USER', $this->getUser()->getRoles())) {

            return $this->render('group_training/view.html.twig', [
                'training' => $training
            ]);
        } else {
            return $this->render('group_training/view.html.twig', [
                'training' => $training
            ]);
        }
        throw new NotFoundHttpException("Something went wrong!");
    }

    private function getUsersByGroupTrainingId($id)
    {
        $repository = $this->getDoctrine()->getRepository(UserTraining::class);
        $trainings  = $repository->findActiveUserTrainings($id);
        $users      = [];
        foreach ($trainings as $training) {
            $user = $training->getUser();
            if ($user->getIsActive() && !$user->getIsDeleted() && $user->getIsAuthorised()) {
                $user->notification = $training->getNotificationType()->getName();
                array_push($users, $user);
            }
        }
        return $users;
    }

    public function edit($id = null, Request $request)
    {
        try {
            new ReflectionClass('ReflectionClass' . ((int)$id . "" !== $id));
        } catch (Exception $e) {
            throw new NotFoundHttpException("Wrong input data");
        }
        $training = $this->getDoctrine()->getRepository(GroupTraining::class)->find((int)$id);
        if (!$training) {
            throw new NotFoundHttpException("Page not found");
        }
        $form = $this->getGroupTrainingForm($training);
        $form->add('edit', SubmitType::class, ['label' => 'Edit', 'attr' => ['class' => 'btn btn-info btn-md', 'style' => 'float:right']])
            ->add('is_expired', CheckboxType::class);
        $form->handleRequest($request);
        if ($request->isMethod('POST')) {
            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($training);
                $entityManager->flush();
                return $this->redirectToRoute('admin');
            }
        }
        return $this->render('group_training/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    private function getNotificationForm($notifications)
    {
        $form = $this->createFormBuilder()->getForm();
        foreach ($notifications as $notification) {
            $form->add($notification->getName() . '_message', TextareaType::class, ['attr' => ['style' => 'resize: none']]);
        }
        $form->add('send', SubmitType::class, ['label' => 'Send Notification', 'attr' => ['class' => 'btn btn-info btn-md', 'style' => 'float:right']]);
        return $form;
    }

    private function getGroupTrainingForm($training)
    {
        $form = $this->createFormBuilder($training)
            ->add('name', TextType::class)
            ->add('master_name', TextType::class)
            ->add('description', TextareaType::class, ['attr' => ['style' => 'resize: none']])
            ->getForm();
        return $form;
    }
}
