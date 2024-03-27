<?php

namespace App\Controller;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Purchase\PurchaseOrderHeader;
use App\Entity\Purchase\PurchaseOrderPaperHeader;
use App\Entity\Purchase\PurchaseRequestHeader;
use App\Entity\Purchase\PurchaseRequestPaperHeader;
use App\Entity\Purchase\PurchaseInvoiceHeader;
use App\Entity\Sale\SaleOrderHeader;
use App\Entity\Sale\SaleInvoiceHeader;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class DefaultController extends AbstractController
{
    #[Route('/_dashboard', name: 'app__dashboard', methods: ['GET', 'POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function _dashboard(EntityManagerInterface $entityManager): Response
    {
        $criteria = new DataCriteria();
        $purchaseRequestHeaderRepository = $entityManager->getRepository(PurchaseRequestHeader::class);
        $purchaseRequestHeaderCount = $purchaseRequestHeaderRepository->fetchCount($criteria, function($qb, $alias) {
            $qb->andWhere("{$alias}.isCanceled = false");
            $qb->andWhere("{$alias}.isRead = false");
            $qb->andWhere("{$alias}.transactionStatus = 'Draft'");
        });
        $purchaseRequestPaperHeaderRepository = $entityManager->getRepository(PurchaseRequestPaperHeader::class);
        $purchaseRequestPaperHeaderCount = $purchaseRequestPaperHeaderRepository->fetchCount($criteria, function($qb, $alias) {
            $qb->andWhere("{$alias}.isCanceled = false");
            $qb->andWhere("{$alias}.isRead = false");
            $qb->andWhere("{$alias}.transactionStatus = 'Draft'");
        });
        $purchaseRequestHeaderCountApproval = $purchaseRequestHeaderRepository->fetchCount($criteria, function($qb, $alias) {
            $qb->andWhere("{$alias}.isCanceled = false");
            $qb->andWhere("{$alias}.isViewed = false");
            $qb->andWhere("{$alias}.transactionStatus = 'Approve'");
        });
        $purchaseRequestPaperHeaderCountApproval = $purchaseRequestPaperHeaderRepository->fetchCount($criteria, function($qb, $alias) {
            $qb->andWhere("{$alias}.isCanceled = false");
            $qb->andWhere("{$alias}.isViewed = false");
            $qb->andWhere("{$alias}.transactionStatus = 'Approve'");
        });
        $purchaseOrderHeaderRepository = $entityManager->getRepository(PurchaseOrderHeader::class);
        $purchaseOrderHeaderCount = $purchaseOrderHeaderRepository->fetchCount($criteria, function($qb, $alias) {
            $qb->andWhere("{$alias}.isCanceled = false");
            $qb->andWhere("{$alias}.isRead = false");
        });
        $purchaseOrderPaperHeaderRepository = $entityManager->getRepository(PurchaseOrderPaperHeader::class);
        $purchaseOrderPaperHeaderCount = $purchaseOrderPaperHeaderRepository->fetchCount($criteria, function($qb, $alias) {
            $qb->andWhere("{$alias}.isCanceled = false");
            $qb->andWhere("{$alias}.isRead = false");
        });
        $purchaseInvoiceHeaderRepository = $entityManager->getRepository(PurchaseInvoiceHeader::class);
        $purchaseInvoiceHeaderCount = $purchaseInvoiceHeaderRepository->fetchCount($criteria, function($qb, $alias) {
            $qb->andWhere("{$alias}.isCanceled = false");
            $qb->andWhere("{$alias}.isRead = false");
        });
        $saleOrderHeaderRepository = $entityManager->getRepository(SaleOrderHeader::class);
        $saleOrderHeaderCount = $saleOrderHeaderRepository->fetchCount($criteria, function($qb, $alias) {
            $qb->andWhere("{$alias}.isCanceled = false");
            $qb->andWhere("{$alias}.isRead = false");
        });
        $saleInvoiceHeaderRepository = $entityManager->getRepository(SaleInvoiceHeader::class);
        $saleInvoiceHeaderCount = $saleInvoiceHeaderRepository->fetchCount($criteria, function($qb, $alias) {
            $qb->andWhere("{$alias}.isCanceled = false");
            $qb->andWhere("{$alias}.isRead = false");
        });

        return $this->render('default/_dashboard.html.twig', [
            'purchaseRequestHeaderCount' => $purchaseRequestHeaderCount,
            'purchaseRequestPaperHeaderCount' => $purchaseRequestPaperHeaderCount,
            'purchaseRequestHeaderCountApproval' => $purchaseRequestHeaderCountApproval,
            'purchaseRequestPaperHeaderCountApproval' => $purchaseRequestPaperHeaderCountApproval,
            'purchaseOrderHeaderCount' => $purchaseOrderHeaderCount,
            'purchaseOrderPaperHeaderCount' => $purchaseOrderPaperHeaderCount,
            'purchaseInvoiceHeaderCount' => $purchaseInvoiceHeaderCount,
            'saleOrderHeaderCount' => $saleOrderHeaderCount,
            'saleInvoiceHeaderCount' => $saleInvoiceHeaderCount,
        ]);
    }

    #[Route('/', name: 'app_index', methods: ['GET'])]
    public function index(): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home_page');
        } else {
            return $this->redirectToRoute('app_login_page');
        }
    }

    #[Route('/home_page', name: 'app_home_page', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function homePage(): Response
    {
        return $this->render('default/home_page.html.twig');
    }

    #[Route('/login_page', name: 'app_login_page', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_ANONYMOUSLY')]
    public function loginPage(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('default/login_page.html.twig', [
            'lastUsername' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/login', name: 'app_login', methods: ['POST'])]
    public function login(): void
    {
    }

    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(): void
    {
    }
}
