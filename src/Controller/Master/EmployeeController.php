<?php

namespace App\Controller\Master;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Master\Employee;
use App\Form\Master\EmployeeType;
use App\Grid\Master\EmployeeGridType;
use App\Repository\Admin\UserRepository;
use App\Repository\Master\EmployeeRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/master/employee')]
class EmployeeController extends AbstractController
{
    #[Route('/_list', name: 'app_master_employee__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, EmployeeRepository $employeeRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(EmployeeGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $employees) = $employeeRepository->fetchData($criteria);

        return $this->renderForm("master/employee/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'employees' => $employees,
        ]);
    }

    #[Route('/', name: 'app_master_employee_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("master/employee/index.html.twig");
    }

    #[Route('/new', name: 'app_master_employee_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EmployeeRepository $employeeRepository): Response
    {
        $employee = new Employee();
        $form = $this->createForm(EmployeeType::class, $employee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $employeeRepository->add($employee, true);

            return $this->redirectToRoute('app_master_employee_show', ['id' => $employee->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/employee/new.html.twig', [
            'employee' => $employee,
            'form' => $form,
        ]);
    }

    #[Route('/add', name: 'app_master_employee_add', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function add(Request $request, EmployeeRepository $employeeRepository, UserRepository $userRepository): Response
    {
        $employee = new Employee();
        if ($request->query->has('user_id')) {
            $user = $userRepository->find($request->query->get('user_id'));
            $employee->setUser($user);
        }
        $form = $this->createForm(EmployeeType::class, $employee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $employeeRepository->add($employee, true);

            return $this->redirectToRoute('app_master_employee_show', ['id' => $employee->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/employee/add.html.twig', [
            'employee' => $employee,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_master_employee_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(Employee $employee): Response
    {
        return $this->render('master/employee/show.html.twig', [
            'employee' => $employee,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_master_employee_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, Employee $employee, EmployeeRepository $employeeRepository): Response
    {
        $form = $this->createForm(EmployeeType::class, $employee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $employeeRepository->add($employee, true);

            return $this->redirectToRoute('app_master_employee_show', ['id' => $employee->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/employee/edit.html.twig', [
            'employee' => $employee,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_master_employee_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, Employee $employee, EmployeeRepository $employeeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $employee->getId(), $request->request->get('_token'))) {
            $employeeRepository->remove($employee, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_master_employee_index', [], Response::HTTP_SEE_OTHER);
    }
}
