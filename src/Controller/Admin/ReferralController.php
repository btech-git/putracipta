<?php

namespace App\Controller\Admin;

use App\Entity\Admin\User;
use App\Repository\Admin\UserRepository;
use App\Repository\Master\EmployeeRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/referral')]
class ReferralController extends AbstractController
{
    private UserRepository $userRepository;
    private EmployeeRepository $employeeRepository;

    public function __construct(UserRepository $userRepository, EmployeeRepository $employeeRepository)
    {
        $this->userRepository = $userRepository;
        $this->employeeRepository = $employeeRepository;
    }

    #[Route('/{user_id}/{referral_id}/assign', name: 'app_admin_referral_assign', methods: ['POST'])]
    #[IsGranted('ROLE_USER_MANAGEMENT')]
    public function assign(string $userId, string $referralId): Response
    {
        $user = $this->userRepository->find($userId);
        $employee = $this->employeeRepository->find($referralId);
        $employee->setUser($user);
        $this->employeeRepository->add($employee, true);

        return $this->redirectToRoute('app_master_employee_show', ['id' => $employee->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/show_profile', name: 'app_admin_referral_show_profile', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function showProfile(User $user): Response
    {
        $employee = $this->employeeRepository->findOneByUser($user);
        return $this->redirectToRoute('app_master_employee_show', ['id' => $employee->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/change_profile', name: 'app_admin_referral_change_profile', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function changeProfile(User $user): Response
    {
        $employee = $this->employeeRepository->findOneByUser($user);
        return $this->redirectToRoute('app_master_employee_edit', ['id' => $employee->getId()], Response::HTTP_SEE_OTHER);
    }

//    #[Route('/{id}/change_password', name: 'app_admin_referral_change_password', methods: ['GET'])]
//    #[IsGranted('ROLE_USER')]
//    public function changePassword(User $user): Response
//    {
//        return $this->redirectToRoute('app_admin_user_change_password', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
//    }
}
