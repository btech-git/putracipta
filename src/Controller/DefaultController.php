<?php

namespace App\Controller;

use App\Form\Admin\UserType;
use App\Repository\Admin\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'app_index', methods: ['GET'])]
    public function index(): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home_page');
        } else {
            return $this->redirectToRoute('app_login_page');
        }
    }

    #[Route('/home_page', name: 'app_home_page', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function homePage(): Response
    {
        return $this->render('default/home_page.html.twig');
    }

    #[Route('/login_page', name: 'app_login_page', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_ANONYMOUSLY')]
    public function loginPage(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('default/login_page.html.twig', [
            'lastUsername' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/login', name: 'app_login', methods: ['POST'])]
    public function login(): void
    {
    }

    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(): void
    {
    }

    #[Route('/{id}/show_profile', name: 'app_show_profile', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function showProfile(User $user): Response
    {
        return $this->render('default/show_profile.html.twig', array(
            'user' => $user,
        ));
    }

    #[Route('/{id}/edit_profile', name: 'app_edit_profile', methods: ['GET', 'POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function editProfile(Request $request, User $user, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->add($user);

            return $this->redirectToRoute('app_show_profile', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('default/edit_profile.html.twig', array(
            'user' => $user,
            'form' => $form,
        ));
    }

    #[Route('/{id}/change_password', name: 'app_change_password', methods: ['GET', 'POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function changePassword(Request $request, User $user, UserRepository $userRepository, UserPasswordEncoderInterface $userPasswordEncoder): Response
    {
        $userPassword = [];
        $form = $this->createFormBuilder($userPassword)
            ->add('oldPassword', PasswordType::class, ['label' => 'Current Password'])
            ->add('newPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options'  => ['label' => 'New Password'],
                'second_options' => ['label' => 'Confirm Password'],
            ])->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $userPasswordEncoder->encodePassword($user, $userPassword['newPassword']);
            $userRepository->upgradePassword($user, $password);

            return $this->redirectToRoute('app_show_profile', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('default/change_password.html.twig', array(
            'user' => $user,
            'form' => $form,
        ));
    }
}
