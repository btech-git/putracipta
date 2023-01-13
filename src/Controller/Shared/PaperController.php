<?php

namespace App\Controller\Shared;

use App\Common\Data\Criteria\DataCriteria;
use App\Grid\Shared\PaperGridType;
use App\Repository\Master\PaperRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shared/paper')]
class PaperController extends AbstractController
{
    #[Route('/_list', name: 'app_shared_paper__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, PaperRepository $paperRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(PaperGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $papers) = $paperRepository->fetchData($criteria, function($qb, $alias) {
            $qb->andWhere("{$alias}.isInactive = false");
        });

        return $this->renderForm("shared/paper/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'papers' => $papers,
        ]);
    }
}
