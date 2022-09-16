<?php

namespace App\Controller\AccessPoint;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\AccessPoint\ItemBoxKey;
use App\Form\AccessPoint\ItemBoxKeyType;
use App\Grid\AccessPoint\ItemBoxKeyGridType;
use App\Repository\AccessPoint\ItemBoxKeyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/access_point/item_box_key')]
class ItemBoxKeyController extends AbstractController
{
    #[Route('/_list', name: 'app_access_point_item_box_key__list', methods: ['GET'])]
    public function _list(Request $request, ItemBoxKeyRepository $itemBoxKeyRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(ItemBoxKeyGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $itemBoxKeys) = $itemBoxKeyRepository->fetchData($criteria);

        return $this->renderForm("access_point/item_box_key/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'itemBoxKeys' => $itemBoxKeys,
        ]);
    }

    #[Route('/', name: 'app_access_point_item_box_key_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render("access_point/item_box_key/index.html.twig");
    }

    #[Route('/new', name: 'app_access_point_item_box_key_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ItemBoxKeyRepository $itemBoxKeyRepository): Response
    {
        $itemBoxKey = new ItemBoxKey();
        $form = $this->createForm(ItemBoxKeyType::class, $itemBoxKey);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $itemBoxKeyRepository->add($itemBoxKey, true);

            return $this->redirectToRoute('app_access_point_item_box_key_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('access_point/item_box_key/new.html.twig', [
            'itemBoxKey' => $itemBoxKey,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_access_point_item_box_key_show', methods: ['GET'])]
    public function show(ItemBoxKey $itemBoxKey): Response
    {
        return $this->render('access_point/item_box_key/show.html.twig', [
            'itemBoxKey' => $itemBoxKey,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_access_point_item_box_key_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ItemBoxKey $itemBoxKey, ItemBoxKeyRepository $itemBoxKeyRepository): Response
    {
        $form = $this->createForm(ItemBoxKeyType::class, $itemBoxKey);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $itemBoxKeyRepository->add($itemBoxKey, true);

            return $this->redirectToRoute('app_access_point_item_box_key_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('access_point/item_box_key/edit.html.twig', [
            'itemBoxKey' => $itemBoxKey,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_access_point_item_box_key_delete', methods: ['POST'])]
    public function delete(Request $request, ItemBoxKey $itemBoxKey, ItemBoxKeyRepository $itemBoxKeyRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $itemBoxKey->getId(), $request->request->get('_token'))) {
            $itemBoxKeyRepository->remove($itemBoxKey, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_access_point_item_box_key_index', [], Response::HTTP_SEE_OTHER);
    }
}
