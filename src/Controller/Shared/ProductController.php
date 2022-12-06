<?php

namespace App\Controller\Shared;

use App\Common\Data\Criteria\DataCriteria;
use App\Grid\Shared\ProductGridType;
use App\Repository\Master\ProductRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shared/product')]
class ProductController extends AbstractController
{
    #[Route('/_list', name: 'app_shared_product__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, ProductRepository $productRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(ProductGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $products) = $productRepository->fetchData($criteria);

        return $this->renderForm("shared/product/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'products' => $products,
        ]);
    }
}
