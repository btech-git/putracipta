<?php

namespace App\Controller\Master;

use App\Entity\Master\AccountCategory;
use App\Form\Master\AccountCategoryType;
use App\Repository\Master\AccountCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/master/account/category')]
class AccountCategoryController extends AbstractController
{
    #[Route('/', name: 'app_master_account_category_index', methods: ['GET'])]
    public function index(AccountCategoryRepository $accountCategoryRepository): Response
    {
        return $this->render('master/account_category/index.html.twig', [
            'account_categories' => $accountCategoryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_master_account_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AccountCategoryRepository $accountCategoryRepository): Response
    {
        $accountCategory = new AccountCategory();
        $form = $this->createForm(AccountCategoryType::class, $accountCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $accountCategoryRepository->add($accountCategory, true);

            return $this->redirectToRoute('app_master_account_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/account_category/new.html.twig', [
            'account_category' => $accountCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_master_account_category_show', methods: ['GET'])]
    public function show(AccountCategory $accountCategory): Response
    {
        return $this->render('master/account_category/show.html.twig', [
            'account_category' => $accountCategory,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_master_account_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, AccountCategory $accountCategory, AccountCategoryRepository $accountCategoryRepository): Response
    {
        $form = $this->createForm(AccountCategoryType::class, $accountCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $accountCategoryRepository->add($accountCategory, true);

            return $this->redirectToRoute('app_master_account_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/account_category/edit.html.twig', [
            'account_category' => $accountCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_master_account_category_delete', methods: ['POST'])]
    public function delete(Request $request, AccountCategory $accountCategory, AccountCategoryRepository $accountCategoryRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$accountCategory->getId(), $request->request->get('_token'))) {
            $accountCategoryRepository->remove($accountCategory, true);
        }

        return $this->redirectToRoute('app_master_account_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
