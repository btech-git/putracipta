<?php

namespace App\Controller;

use App\Entity\Admin\User;
use App\Repository\Admin\UserRepository;
use App\Repository\Master\EmployeeRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

#[Route('/referral')]
class ReferralController extends AbstractController
{
    private UserRepository $userRepository;
    private EmployeeRepository $employeeRepository;

    public function __construct(UserRepository $userRepository, EmployeeRepository $employeeRepository)
    {
        $this->userRepository = $userRepository;
        $this->employeeRepository = $employeeRepository;
    }

    #[Route('/{user_id}/{referral_id}/assign', name: 'app_referral_assign', methods: ['POST'])]
    #[IsGranted('ROLE_USER_MANAGEMENT')]
    public function assign(string $userId, string $referralId): Response
    {
        $user = $this->userRepository->find($userId);
        $employee = $this->employeeRepository->find($referralId);
        $employee->setUser($user);
        $this->employeeRepository->add($employee, true);

        return $this->redirectToRoute('app_master_employee_show', ['id' => $employee->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/show_profile', name: 'app_referral_show_profile', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function showProfile(User $user): Response
    {
        $employee = $this->employeeRepository->findOneByUser($user);
        return $this->redirectToRoute('app_master_employee_show', ['id' => $employee->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/change_profile', name: 'app_referral_change_profile', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function changeProfile(User $user): Response
    {
        $employee = $this->employeeRepository->findOneByUser($user);
        return $this->redirectToRoute('app_master_employee_edit', ['id' => $employee->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/change_password', name: 'app_referral_change_password', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function changePassword(Request $request, UserInterface $user, UserRepository $userRepository, UserPasswordEncoderInterface $userPasswordEncoder): Response
    {
        $form = $this->createFormBuilder([])
            ->add('oldPassword', PasswordType::class, ['label' => 'Current Password', 'constraints' => [new NotBlank(), new NotNull(), new UserPassword()]])
            ->add('newPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options'  => ['label' => 'New Password'],
                'second_options' => ['label' => 'Confirm Password'],
                'constraints' => [new NotBlank(), new NotNull()],
            ])->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userPassword = $form->getData();
            $hashedPassword = $userPasswordEncoder->encodePassword($user, $userPassword['newPassword']);
            $userRepository->upgradePassword($user, $hashedPassword);

            return $this->redirectToRoute('app_referral_show_profile', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('referral/change_password.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
}
