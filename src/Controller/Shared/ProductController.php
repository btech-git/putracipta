<?php

namespace App\Controller\Shared;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortAscending;
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
        $criteria->setSort([
            'name' => SortAscending::class,
        ]);
        $form = $this->createForm(ProductGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $products) = $productRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            $customerId = '';
            if (isset($request->query->get('sale_order_header')['customer'])) {
                $customerId = $request->query->get('sale_order_header')['customer'];
            }
            if (!empty($customerId)) {
                $qb->andWhere("IDENTITY({$alias}.customer) = :customerId");
                $qb->setParameter('customerId', $customerId);
            }
            if (isset($request->query->get('product_grid')['filter']['unit:name']) && isset($request->query->get('product_grid')['sort']['unit:name'])) {
                $qb->innerJoin("{$alias}.unit", 'u');
                $add['filter']($qb, 'u', 'name', $request->query->get('product_grid')['filter']['unit:name']);
                $add['sort']($qb, 'u', 'name', $request->query->get('product_grid')['sort']['unit:name']);
            }
            $qb->andWhere("{$alias}.isInactive = false");
        });

        return $this->renderForm("shared/product/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'products' => $products,
        ]);
    }
}
