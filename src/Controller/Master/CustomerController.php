<?php

namespace App\Controller\Master;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Master\Customer;
use App\Form\Master\CustomerType;
use App\Grid\Master\CustomerGridType;
use App\Repository\Master\CustomerRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/master/customer')]
class CustomerController extends AbstractController
{
    #[Route('/_list', name: 'app_master_customer__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, CustomerRepository $customerRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(CustomerGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $customers) = $customerRepository->fetchData($criteria);

        return $this->renderForm("master/customer/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'customers' => $customers,
        ]);
    }

    #[Route('/', name: 'app_master_customer_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("master/customer/index.html.twig");
    }

    #[Route('/new', name: 'app_master_customer_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, CustomerRepository $customerRepository): Response
    {
        $customer = new Customer();
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $customerRepository->add($customer, true);

            return $this->redirectToRoute('app_master_customer_show', ['id' => $customer->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/customer/new.html.twig', [
            'customer' => $customer,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_master_customer_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(Customer $customer): Response
    {
        return $this->render('master/customer/show.html.twig', [
            'customer' => $customer,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_master_customer_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, Customer $customer, CustomerRepository $customerRepository): Response
    {
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $customerRepository->add($customer, true);

            return $this->redirectToRoute('app_master_customer_show', ['id' => $customer->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/customer/edit.html.twig', [
            'customer' => $customer,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_master_customer_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, Customer $customer, CustomerRepository $customerRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $customer->getId(), $request->request->get('_token'))) {
            $customerRepository->remove($customer, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_master_customer_index', [], Response::HTTP_SEE_OTHER);
    }
}
