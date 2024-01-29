<?php

namespace App\Controller\Master;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Master\Product;
use App\Form\Master\ProductType;
use App\Grid\Master\ProductGridType;
use App\Repository\Master\ProductRepository;
use App\Service\Master\ProductFormService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/master/product')]
class ProductController extends AbstractController
{
    #[Route('/_list', name: 'app_master_product__list', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_PRODUCT_ADD') or is_granted('ROLE_PRODUCT_EDIT')")]
    public function _list(Request $request, ProductRepository $productRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(ProductGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $products) = $productRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            if (isset($request->request->get('product_grid')['filter']['customer:company']) && isset($request->request->get('product_grid')['sort']['customer:company'])) {
                $qb->innerJoin("{$alias}.customer", 'c');
                $add['filter']($qb, 'c', 'company', $request->request->get('product_grid')['filter']['customer:company']);
                $add['sort']($qb, 'c', 'company', $request->request->get('product_grid')['sort']['customer:company']);
            }
        });

        return $this->renderForm("master/product/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'products' => $products,
        ]);
    }

    #[Route('/', name: 'app_master_product_index', methods: ['GET'])]
    #[Security("is_granted('ROLE_PRODUCT_ADD') or is_granted('ROLE_PRODUCT_EDIT')")]
    public function index(): Response
    {
        return $this->render("master/product/index.html.twig");
    }

    #[Route('/new', name: 'app_master_product_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PRODUCT_ADD')]
    public function new(Request $request, ProductFormService $productFormService): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        $productFormService->finalize($product, ['transactionFile' => $form->get('transactionFile')->getData()]);

        if (IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $productFormService->save($product);
            $productFormService->uploadFile($product, $form->get('transactionFile')->getData(), $this->getParameter('kernel.project_dir') . '/public/uploads/product');

            return $this->redirectToRoute('app_master_product_show', ['id' => $product->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/product/new.html.twig', [
            'product' => $product,
            'form' => $form,
            'transactionFileExists' => false,
        ]);
    }

    #[Route('/{id}', name: 'app_master_product_show', methods: ['GET'])]
    #[Security("is_granted('ROLE_PRODUCT_ADD') or is_granted('ROLE_PRODUCT_EDIT')")]
    public function show(Product $product): Response
    {
        return $this->render('master/product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_master_product_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PRODUCT_EDIT')]
    public function edit(Request $request, Product $product, ProductFormService $productFormService): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        $productFormService->finalize($product, ['transactionFile' => $form->get('transactionFile')->getData()]);

        if (IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $productFormService->save($product);
            $productFormService->uploadFile($product, $form->get('transactionFile')->getData(), $this->getParameter('kernel.project_dir') . '/public/uploads/product');

            return $this->redirectToRoute('app_master_product_show', ['id' => $product->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
            'transactionFileExists' => file_exists($this->getParameter('kernel.project_dir') . '/public/uploads/product/' . $product->getId() . '.' . $product->getFileExtension()),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_master_product_delete', methods: ['POST'])]
    #[IsGranted('ROLE_PRODUCT_EDIT')]
    public function delete(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
            $productRepository->remove($product, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_master_product_index', [], Response::HTTP_SEE_OTHER);
    }
}
