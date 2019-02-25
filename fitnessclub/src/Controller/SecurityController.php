<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class SecurityController extends AbstractController
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->encoder = $userPasswordEncoder;
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request, AuthenticationUtils $utils)
    {
        if($this->getUser()){
            return $this->redirectToRoute('default');
        }

        $error = $utils->getLastAuthenticationError();

        $lastUsername = $utils->getLastUsername();
        return $this->render('security/login.html.twig', [
            'error' => $error,
            'last_username' => $lastUsername
        ]);
    }
    /**
     * @Route("/logout", name="logout")
     */
    public function logout(){

    }

    /**
     * @Route("/resetPassword/{token}", name="resetPassword")
     */
    public function resetPassword($token, Request $request){

        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->findOneBy(['auth_token'=>$token]);

        if($user){
            $form = $this->getResetPasswordForm($user);

            $form->handleRequest($request);

            if ($request->isMethod('POST')) {

                if ($form->isValid()) {
                    $entityManager = $this->getDoctrine()->getManager();
                    $user->setIsAuthorised(true);
                    $user->setIsDeleted(false);
                    $user->setAuthToken(null);
                    $user->setIsActive(true);
                    $user->setPassword($this->encoder->encodePassword($user,$form->getData()->getPassword()));
                    $entityManager->persist($user);
                    $entityManager->flush();

                    $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
                    $this->get('security.token_storage')->setToken($token);
                    $this->get('session')->set('_security_main', serialize($token));

                    return $this->redirectToRoute('profile');

                }
            }


            return $this->render('security/resetPassword.html.twig',[
                'form' => $form->createView()
            ]);
        }
        throw new NotFoundHttpException("Page not found");

    }

    private function getResetPasswordForm(User $user){
        $form = $this->createFormBuilder($user)
            ->add('password', RepeatedType::class,[
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['class' => 'form-control', 'autocomplete'=>'off']],
                'required' => true,
                'first_options'  => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password'],

            ])
            ->add('resetPassword', SubmitType::class, ['label' => 'Reset', 'attr'=>['class' => 'btn btn-info btn-md', 'style' => 'float:right']])
            ->getForm();

        return $form;

    }
}
