<?php

namespace App\Controller\Master;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Master\Product;
use App\Form\Master\ProductType;
use App\Grid\Master\ProductGridType;
use App\Repository\Master\ProductRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/master/product')]
class ProductController extends AbstractController
{
    #[Route('/_list', name: 'app_master_product__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, ProductRepository $productRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(ProductGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $products) = $productRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            if (isset($request->query->get('product_grid')['filter']['customer:company']) && isset($request->query->get('product_grid')['sort']['customer:company'])) {
                $qb->innerJoin("{$alias}.customer", 'c');
                $add['filter']($qb, 'c', 'company', $request->query->get('product_grid')['filter']['customer:company']);
                $add['sort']($qb, 'c', 'company', $request->query->get('product_grid')['sort']['customer:company']);
            }
        });

        return $this->renderForm("master/product/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'products' => $products,
        ]);
    }

    #[Route('/', name: 'app_master_product_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("master/product/index.html.twig");
    }

    #[Route('/new', name: 'app_master_product_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, ProductRepository $productRepository): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productRepository->add($product, true);

            return $this->redirectToRoute('app_master_product_show', ['id' => $product->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_master_product_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(Product $product): Response
    {
        return $this->render('master/product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_master_product_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productRepository->add($product, true);

            return $this->redirectToRoute('app_master_product_show', ['id' => $product->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_master_product_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
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
