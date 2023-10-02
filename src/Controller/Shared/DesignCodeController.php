<?php

namespace App\Controller\Shared;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortAscending;
use App\Entity\Master\DesignCodeProcessDetail;
use App\Grid\Shared\DesignCodeGridType;
use App\Repository\Master\DesignCodeRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shared/design_code')]
class DesignCodeController extends AbstractController
{
    #[Route('/_list', name: 'app_shared_design_code__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, DesignCodeRepository $designCodeRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'name' => SortAscending::class,
        ]);
        $form = $this->createForm(DesignCodeGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $designCodes) = $designCodeRepository->fetchData($criteria, function($qb, $alias) use ($request) {
            
            $customerId = '';
            if (isset($request->query->get('master_order_header')['customer'])) {
                $customerId = $request->query->get('master_order_header')['customer'];
            } elseif (isset($request->query->get('product_prototype')['customer'])) {
                $customerId = $request->query->get('product_prototype')['customer'];
            }
            
            if (!empty($customerId)) {
                $qb->andWhere("IDENTITY({$alias}.customer) = :customerId");
                $qb->setParameter('customerId', $customerId);
            }
            
            $qb->andWhere("EXISTS(SELECT d FROM "  . DesignCodeProcessDetail::class . " d WHERE IDENTITY(d.designCode) = {$alias}.id)");
            
            $qb->andWhere("{$alias}.isInactive = false");
        });

        return $this->renderForm("shared/design_code/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'designCodes' => $designCodes,
        ]);
    }
}
