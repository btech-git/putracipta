<?php

namespace App\Controller\Production;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortDescending;
use App\Entity\Production\ProductPrototype;
use App\Form\Production\ProductPrototypeType;
use App\Grid\Production\ProductPrototypeGridType;
use App\Repository\Production\ProductPrototypeRepository;
use App\Service\Production\ProductPrototypeFormService;
use App\Util\PdfGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/production/product_prototype')]
class ProductPrototypeController extends AbstractController
{
    #[Route('/_list', name: 'app_production_product_prototype__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, ProductPrototypeRepository $productPrototypeRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'productionDate' => SortDescending::class,
            'id' => SortDescending::class,
        ]);
        $form = $this->createForm(ProductPrototypeGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $productPrototypes) = $productPrototypeRepository->fetchData($criteria);

        return $this->renderForm("production/product_prototype/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'productPrototypes' => $productPrototypes,
        ]);
    }

    #[Route('/', name: 'app_production_product_prototype_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("production/product_prototype/index.html.twig");
    }

    #[Route('/new.{_format}', name: 'app_production_product_prototype_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, ProductPrototypeFormService $productPrototypeFormService, $_format = 'html'): Response
    {
        $productPrototype = new ProductPrototype();
        $productPrototypeFormService->initialize($productPrototype, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(ProductPrototypeType::class, $productPrototype);
        $form->handleRequest($request);
        $productPrototypeFormService->finalize($productPrototype, ['transactionFile' => $form->get('transactionFile')->getData()]);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $productPrototypeFormService->save($productPrototype);
            $productPrototypeFormService->uploadFile($productPrototype, $form->get('transactionFile')->getData(), $this->getParameter('kernel.project_dir') . '/public/uploads/product-prototype');

            return $this->redirectToRoute('app_production_product_prototype_show', ['id' => $productPrototype->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("production/product_prototype/new.{$_format}.twig", [
            'productPrototype' => $productPrototype,
            'form' => $form,
            'transactionFileExists' => false,
        ]);
    }

    #[Route('/{id}', name: 'app_production_product_prototype_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(ProductPrototype $productPrototype): Response
    {
        return $this->render('production/product_prototype/show.html.twig', [
            'productPrototype' => $productPrototype,
        ]);
    }

    #[Route('/{id}/edit.{_format}', name: 'app_production_product_prototype_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, ProductPrototype $productPrototype, ProductPrototypeFormService $productPrototypeFormService, $_format = 'html'): Response
    {
        $productPrototypeFormService->initialize($productPrototype, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(ProductPrototypeType::class, $productPrototype);
        $form->handleRequest($request);
        $productPrototypeFormService->finalize($productPrototype, ['transactionFile' => $form->get('transactionFile')->getData()]);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $productPrototypeFormService->save($productPrototype);
            $productPrototypeFormService->uploadFile($productPrototype, $form->get('transactionFile')->getData(), $this->getParameter('kernel.project_dir') . '/public/uploads/product-prototype');

            return $this->redirectToRoute('app_production_product_prototype_show', ['id' => $productPrototype->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("production/product_prototype/edit.{$_format}.twig", [
            'productPrototype' => $productPrototype,
            'form' => $form,
            'transactionFileExists' => file_exists($this->getParameter('kernel.project_dir') . '/public/uploads/product-prototype/' . $productPrototype->getId() . '.' . $productPrototype->getProductionFileExtension()),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_production_product_prototype_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, ProductPrototype $productPrototype, ProductPrototypeRepository $productPrototypeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $productPrototype->getId(), $request->request->get('_token'))) {
            $productPrototypeRepository->remove($productPrototype, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_production_product_prototype_index', [], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/{id}/memo_development', name: 'app_production_product_prototype_memo_development', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function memoDevelopment(ProductPrototype $productPrototype): Response
    {
        $fileName = 'pengembangan_produk_baru.pdf';
        $htmlView = $this->renderView('production/product_prototype/memo_development.html.twig', [
            'productPrototype' => $productPrototype,
        ]);

        $pdfGenerator = new PdfGenerator($this->getParameter('kernel.project_dir') . '/public/');
        $pdfGenerator->generate($htmlView, $fileName, [
            fn($html, $chrootDir) => preg_replace('/<link rel="stylesheet"(.+)href=".+">/', '<link rel="stylesheet"\1href="' . $chrootDir . 'build/memo.css">', $html),
            fn($html, $chrootDir) => preg_replace('/<img id="logo"(.+)src=".+">/', '<img id="logo"\1src="' . $chrootDir . 'images/Logo.jpg">', $html),
            fn($html, $chrootDir) => preg_replace('/<img id="upload"(.+)src=".+">/', '<img id="upload"\1src="' . $chrootDir . 'uploads/product-prototype/' . $productPrototype->getId() . '.' . $productPrototype->getProductionFileExtension() . '">', $html),
        ]);
    }
    
    #[Route('/{id}/memo_prototype', name: 'app_production_product_prototype_memo_prototype', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function memoPrototype(ProductPrototype $productPrototype): Response
    {
        $fileName = 'form_produk_baru.pdf';
        $htmlView = $this->renderView('production/product_prototype/memo_prototype.html.twig', [
            'productPrototype' => $productPrototype,
        ]);

        $pdfGenerator = new PdfGenerator($this->getParameter('kernel.project_dir') . '/public/');
        $pdfGenerator->generate($htmlView, $fileName, [
            fn($html, $chrootDir) => preg_replace('/<link rel="stylesheet"(.+)href=".+">/', '<link rel="stylesheet"\1href="' . $chrootDir . 'build/memo.css">', $html),
            fn($html, $chrootDir) => preg_replace('/<img id="logo"(.+)src=".+">/', '<img id="logo"\1src="' . $chrootDir . 'images/Logo.jpg">', $html),
            fn($html, $chrootDir) => preg_replace('/<img id="upload"(.+)src=".+">/', '<img id="upload"\1src="' . $chrootDir . 'uploads/product-prototype/' . $productPrototype->getId() . '.' . $productPrototype->getProductionFileExtension() . '">', $html),
        ]);
    }
}
