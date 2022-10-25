<?php

namespace App\Controller\Master;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Master\Brand;
use App\Form\Master\BrandType;
use App\Grid\Master\BrandGridType;
use App\Repository\Master\BrandRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/master/brand')]
class BrandController extends AbstractController
{
    #[Route('/_list', name: 'app_master_brand__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, BrandRepository $brandRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(BrandGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $brands) = $brandRepository->fetchData($criteria);

        return $this->renderForm("master/brand/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'brands' => $brands,
        ]);
    }

    #[Route('/', name: 'app_master_brand_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("master/brand/index.html.twig");
    }

    #[Route('/new', name: 'app_master_brand_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, BrandRepository $brandRepository): Response
    {
        $brand = new Brand();
        $form = $this->createForm(BrandType::class, $brand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $brandRepository->add($brand, true);

            return $this->redirectToRoute('app_master_brand_show', ['id' => $brand->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/brand/new.html.twig', [
            'brand' => $brand,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_master_brand_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(Brand $brand): Response
    {
        return $this->render('master/brand/show.html.twig', [
            'brand' => $brand,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_master_brand_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, Brand $brand, BrandRepository $brandRepository): Response
    {
        $form = $this->createForm(BrandType::class, $brand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $brandRepository->add($brand, true);

            return $this->redirectToRoute('app_master_brand_show', ['id' => $brand->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/brand/edit.html.twig', [
            'brand' => $brand,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_master_brand_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, Brand $brand, BrandRepository $brandRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $brand->getId(), $request->request->get('_token'))) {
            $brandRepository->remove($brand, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_master_brand_index', [], Response::HTTP_SEE_OTHER);
    }
}
