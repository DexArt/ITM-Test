<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use DateTime;
use Exception;
use ReflectionClass;
use Swift_Mailer;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->encoder = $userPasswordEncoder;
    }

    /**
     * @Route("/admin/users", name="users")
     */
    public function index()
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $users      = $repository->getAllUsersExcept($this->getUser());
        $count      = sizeof($users);
        $this->addFlash(
            'notice',
            "Found $count users"
        );
        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/admin/users/add", name="users")
     */
    public function add(Request $request, ValidatorInterface $validator, Swift_Mailer $mailer)
    {

        $user = new User();
        $form = $this->getUserForm($user);
        $form->add('image', FileType::class, ['label'=>'Select image']);
        $form->add('add', SubmitType::class, ['label' => 'Add', 'translation_domain' => false, 'attr' => ['class' => 'btn btn-info btn-md', 'style' => 'float:right']]);
        $form->handleRequest($request);
        if ($request->isMethod('POST') && $form->isValid()) {
            try {
                $entityManager = $this->getDoctrine()->getManager();

                $avatar = $user->getImage();
                if($avatar){
                    $fileName = md5(uniqid()).'.'.$avatar->guessExtension();

                    $avatar->move($this->getParameter('image_directory'), $fileName);

                    $user->setImage($fileName);
                }else{
                    $user->setImage(null);
                }

                $user->setIsAuthorised(false);
                $user->setIsActive(false);
                $user->setRoles(['ROLE_USER']);
                $user->setPassword($this->encoder->encodePassword($user, md5($user->getEmail() . time())));
                $user->setAuthToken(md5($user->getEmail() . time()));
                $user->setIsDeleted(false);
                $entityManager->persist($user);
                $entityManager->flush();
            } catch (Exception $e) {
                $form->get('email')->addError(new FormError('Email already exist'));
                return $this->render('user/addUser.html.twig', [
                    'form' => $form->createView()
                ]);
            }
            if ($this->resetPasswordEmail($user, $mailer)) {
                return $this->redirectToRoute('users');
            }
        }
        return $this->render('user/addUser.html.twig', [
            'form' => $form->createView()
        ]);

    }

    public function edit($id, Request $request)
    {
        try {
            new ReflectionClass('ReflectionClass' . ((int)$id . "" !== $id));
        } catch (Exception $e) {
            throw new NotFoundHttpException("Wrong input data");
        }
        $entityManager = $this->getDoctrine()->getManager();
        $repository    = $this->getDoctrine()->getRepository(User::class);
        if (in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            $user = $repository->find((int)$id);
            $form = $this->getUserForm($user);
            $form->add('email', TextType::class, ['attr' => ['readonly' => true]]);
            $form->add('is_active', CheckboxType::class);
            $form->add('Edit', SubmitType::class, ['label' => 'Edit', 'attr' => ['class' => 'btn btn-info btn-md', 'style' => 'float:right']]);
            $form->handleRequest($request);
            if ($request->isMethod('POST')) {
                if ($form->isValid()) {
                    try {
                        $entityManager = $this->getDoctrine()->getManager();
                        $entityManager->persist($user);
                        $entityManager->flush();
                        $this->get('session')->getFlashBag()->clear();
                        $this->get('session')->getFlashBag()->add('notice', 'Profile updated');

                    } catch (Exception $e) {
                        return $this->render('user/addUser.html.twig', [
                            'form' => $form->createView()
                        ]);
                    }
                }
            }
            return $this->render('user/addUser.html.twig', [
                'form' => $form->createView()
            ]);

        } elseif (in_array('ROLE_USER', $this->getUser()->getRoles())) {

            $user         = $repository->find((int)$this->getUser()->getId());
            if($user->getId() !== (int)$id){
                throw new NotFoundHttpException("Page not found");
            }
            $userDataForm = $this->getUserForm($user);
            $userDataForm = $this->getUserFormReadOnly($userDataForm);
            $passwordForm = $this->getResetPasswordForm($user);
            $passwordForm->handleRequest($request);
            if ($request->isMethod('POST')) {
                if ($passwordForm->isValid()) {
                    $entityManager = $this->getDoctrine()->getManager();
                    $user->setPassword($this->encoder->encodePassword($user, $passwordForm->getData()->getPassword()));
                    $entityManager->persist($user);
                    $entityManager->flush();
                }
            }
            return $this->render('profile/edit.html.twig', [
                'passwordForm' => $passwordForm->createView(),
                'userDataForm' => $userDataForm->createView()
            ]);

        } else {
            throw new NotFoundHttpException("Access Denied!");
        }

    }

    public function remove($id)
    {
        try {
            new ReflectionClass('ReflectionClass' . ((int)$id . "" !== $id));
        } catch (Exception $e) {
            throw new NotFoundHttpException("Wrong input data");
        }
        $entityManager = $this->getDoctrine()->getManager();
        $repository    = $this->getDoctrine()->getRepository(User::class);
        $user          = $repository->find((int)$id);
        $user->setIsDeleted(true);
        $user->setIsActive(false);
        $entityManager->persist($user);
        $entityManager->flush();
        $this->get('session')->getFlashBag()->add("userDeleted", "User successfully deleted");
        return $this->redirectToRoute('users');

    }

    private function resetPasswordEmail(User $user, Swift_Mailer $mailer)
    {
        $message = (new \Swift_Message('Registration Confirm Link'))
            ->setFrom('fitness.club.itm2019@gmail.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    'emails/registration.html.twig',
                    ['token' => $user->getAuthToken(), 'firstname' => $user->getFirstname()]
                ),
                'text/html'
            );
        if ($mailer->send($message)) {
            return true;
        } else {
            return false;
        }
    }

    private function getUserForm(User $user)
    {
        $form = $this->createFormBuilder($user)
            ->add('firstname', TextType::class)
            ->add('lastname')
            ->add('email')
            ->add('birthday', DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('gender', ChoiceType::class, [
                'choices' => [
                    'Male' => 'male',
                    'Female' => 'female',
                    'Unknown' => 'unknown'
                ],
                'choice_translation_domain' => false
            ])
            ->add('phone')
            ->getForm();
        return $form;
    }

    private function getResetPasswordForm(User $user)
    {
        $form = $this->createFormBuilder($user)
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['class' => 'form-control', 'autocomplete' => 'off']],
                'required' => true,
                'first_options' => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password'],
            ])
            ->add('resetPassword', SubmitType::class, ['label' => 'Reset', 'attr' => ['class' => 'btn btn-info btn-md', 'style' => 'float:right']])
            ->getForm();
        return $form;
    }

    private function getUserFormReadOnly($form)
    {
        $options = ['attr' => ['readonly' => true]];
        $form->add('firstname', TextType::class, $options);
        $form->add('lastname', TextType::class, $options);
        $form->add('email', TextType::class, $options);
        $form->add('birthday', DateType::class, ['widget' => 'single_text', 'attr' => ['readonly' => true]]);
        $form->add('gender', TextType::class, $options);
        $form->add('phone', TextType::class, $options);
        return $form;
    }
}
