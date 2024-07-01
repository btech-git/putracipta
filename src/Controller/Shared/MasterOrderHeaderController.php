<?php

namespace App\Controller\Shared;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortDescending;
use App\Entity\Production\QualityControlSortingHeader;
use App\Grid\Production\MasterOrderHeaderGridType;
use App\Repository\Production\MasterOrderHeaderRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shared/master_order_header')]
class MasterOrderHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_shared_master_order_header__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, MasterOrderHeaderRepository $masterOrderHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'transactionDate' => SortDescending::class,
            'id' => SortDescending::class,
        ]);
        $form = $this->createForm(MasterOrderHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $masterOrderHeaders) = $masterOrderHeaderRepository->fetchData($criteria, function($qb, $alias, $add, $new) use ($request) {
            if (isset($request->request->get('master_order_header_grid')['filter']['customer:company']) && isset($request->request->get('master_order_header_grid')['sort']['customer:company'])) {
                $qb->innerJoin("{$alias}.customer", 'c');
                $add['filter']($qb, 'c', 'company', $request->request->get('master_order_header_grid')['filter']['customer:company']);
                $add['sort']($qb, 'c', 'company', $request->request->get('master_order_header_grid')['sort']['customer:company']);
            }
            if (isset($request->request->get('master_order_header_grid')['filter']['designCode:code']) && isset($request->request->get('master_order_header_grid')['sort']['designCode:code'])) {
                $qb->innerJoin("{$alias}.designCode", 'd');
                $add['filter']($qb, 'd', 'code', $request->request->get('master_order_header_grid')['filter']['designCode:code']);
                $add['sort']($qb, 'd', 'code', $request->request->get('master_order_header_grid')['sort']['designCode:code']);
            }
            if (isset($request->request->get('master_order_header_grid')['filter']['designCode:variant']) && isset($request->request->get('master_order_header_grid')['sort']['designCode:variant'])) {
                $qb->innerJoin("{$alias}.designCode", 'i');
                $add['filter']($qb, 'i', 'variant', $request->request->get('master_order_header_grid')['filter']['designCode:variant']);
                $add['sort']($qb, 'i', 'variant', $request->request->get('master_order_header_grid')['sort']['designCode:variant']);
            }
            if (isset($request->request->get('master_order_header_grid')['filter']['designCode:version']) && isset($request->request->get('master_order_header_grid')['sort']['designCode:version'])) {
                $qb->innerJoin("{$alias}.designCode", 's');
                $add['filter']($qb, 's', 'version', $request->request->get('master_order_header_grid')['filter']['designCode:version']);
                $add['sort']($qb, 's', 'version', $request->request->get('master_order_header_grid')['sort']['designCode:version']);
            }
            
            $sub = $new(QualityControlSortingHeader::class, 'q');
            $sub->andWhere("IDENTITY(q.masterOrderHeader) = {$alias}.id");
            $qb->andWhere($qb->expr()->not($qb->expr()->exists($sub->getDQL())));
            $qb->andWhere("{$alias}.isCanceled = false");
            
        });

        return $this->renderForm("shared/master_order_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'masterOrderHeaders' => $masterOrderHeaders,
        ]);
    }
}
