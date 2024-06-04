<?php

namespace App\Controller;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Criteria\DataCriteriaPagination;
use App\Entity\Purchase\PurchaseOrderHeader;
use App\Entity\Purchase\PurchaseOrderPaperHeader;
use App\Entity\Purchase\PurchaseRequestHeader;
use App\Entity\Purchase\PurchaseRequestPaperHeader;
use App\Entity\Purchase\PurchaseInvoiceHeader;
use App\Entity\Sale\SaleOrderHeader;
use App\Entity\Sale\SaleInvoiceHeader;
use App\Entity\Stock\InventoryRequestHeader;
use App\Grid\Shared\DashboardPurchaseOrderGridType;
use App\Grid\Shared\DashboardPurchaseOrderPaperGridType;
use App\Grid\Shared\DashboardSaleOrderGridType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class DefaultController extends AbstractController
{
    #[Route('/_dashboard', name: 'app__dashboard', methods: ['GET', 'POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function _dashboard(Request $request, EntityManagerInterface $entityManager): Response
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
        $inventoryRequestHeaderRepository = $entityManager->getRepository(InventoryRequestHeader::class);
        $inventoryRequestHeaderCount = $inventoryRequestHeaderRepository->fetchCount($criteria, function($qb, $alias) {
            $qb->andWhere("{$alias}.isCanceled = false");
            $qb->andWhere("{$alias}.isRead = false");
        });

        $criteria = new DataCriteria();
        $saleForm = $this->createForm(DashboardSaleOrderGridType::class, $criteria);
        $saleForm->handleRequest($request);

        list($saleCount, $saleOrderHeaders) = $saleOrderHeaderRepository->fetchData($criteria);

        $criteria = new DataCriteria();
        $dataCriteriaPagination = new DataCriteriaPagination();
        $dataCriteriaPagination->setSize(1000);
        $criteria->setPagination($dataCriteriaPagination);
        $saleOrderHeaderItems = $saleOrderHeaderRepository->fetchObjects($criteria, function($qb, $alias) use ($saleOrderHeaders) {
            $qb->leftJoin("{$alias}.saleOrderDetails", 'd');
            $qb->leftJoin("d.masterOrderProductDetails", 'md');
            $qb->leftJoin("md.masterOrderHeader", 'mh');
            $qb->leftJoin("md.deliveryDetails", 'dd');
            $qb->leftJoin("dd.deliveryHeader", 'dh');
            $qb->leftJoin("dd.saleInvoiceDetails", 'sd');
            $qb->leftJoin("sd.saleInvoiceHeader", 'sh');
            $qb->leftJoin("sh.salePaymentDetails", 'pd');
            $qb->leftJoin("pd.salePaymentHeader", 'ph');
            $qb->andWhere("{$alias} IN (:saleOrderHeaders)")->setParameter('saleOrderHeaders', $saleOrderHeaders);
        });
        $saleOrderHeaderData = [];
        foreach ($saleOrderHeaderItems as $saleOrderHeaderItem) {
            $saleOrderHeaderData[$saleOrderHeaderItem->getId()] = $saleOrderHeaderItem;
        }
        
        $criteria = new DataCriteria();
        $purchaseForm = $this->createForm(DashboardPurchaseOrderGridType::class, $criteria);
        $purchaseForm->handleRequest($request);

        list($purchaseCount, $purchaseOrderHeaders) = $purchaseOrderHeaderRepository->fetchData($criteria);

        $criteria = new DataCriteria();
        $dataCriteriaPagination = new DataCriteriaPagination();
        $dataCriteriaPagination->setSize(1000);
        $criteria->setPagination($dataCriteriaPagination);
        $purchaseOrderHeaderItems = $purchaseOrderHeaderRepository->fetchObjects($criteria, function($qb, $alias) use ($purchaseOrderHeaders) {
            $qb->leftJoin("{$alias}.purchaseOrderDetails", 'd');
            $qb->leftJoin("d.receiveDetails", 'dd');
            $qb->leftJoin("dd.receiveHeader", 'dh');
            $qb->leftJoin("dd.purchaseInvoiceDetails", 'sd');
            $qb->leftJoin("sd.purchaseInvoiceHeader", 'sh');
            $qb->leftJoin("sh.purchasePaymentDetails", 'pd');
            $qb->leftJoin("pd.purchasePaymentHeader", 'ph');
            $qb->andWhere("{$alias} IN (:purchaseOrderHeaders)")->setParameter('purchaseOrderHeaders', $purchaseOrderHeaders);
        });
        $purchaseOrderHeaderData = [];
        foreach ($purchaseOrderHeaderItems as $purchaseOrderHeaderItem) {
            $purchaseOrderHeaderData[$purchaseOrderHeaderItem->getId()] = $purchaseOrderHeaderItem;
        }
        
        $criteria = new DataCriteria();
        $purchasePaperForm = $this->createForm(DashboardPurchaseOrderPaperGridType::class, $criteria);
        $purchasePaperForm->handleRequest($request);

        list($purchasePaperCount, $purchaseOrderPaperHeaders) = $purchaseOrderPaperHeaderRepository->fetchData($criteria);

        $criteria = new DataCriteria();
        $dataCriteriaPagination = new DataCriteriaPagination();
        $dataCriteriaPagination->setSize(1000);
        $criteria->setPagination($dataCriteriaPagination);
        $purchaseOrderPaperHeaderItems = $purchaseOrderPaperHeaderRepository->fetchObjects($criteria, function($qb, $alias) use ($purchaseOrderPaperHeaders) {
            $qb->leftJoin("{$alias}.purchaseOrderPaperDetails", 'd');
            $qb->leftJoin("d.receiveDetails", 'dd');
            $qb->leftJoin("dd.receiveHeader", 'dh');
            $qb->leftJoin("dd.purchaseInvoiceDetails", 'sd');
            $qb->leftJoin("sd.purchaseInvoiceHeader", 'sh');
            $qb->leftJoin("sh.purchasePaymentDetails", 'pd');
            $qb->leftJoin("pd.purchasePaymentHeader", 'ph');
            $qb->andWhere("{$alias} IN (:purchaseOrderPaperHeaders)")->setParameter('purchaseOrderPaperHeaders', $purchaseOrderPaperHeaders);
        });
        $purchaseOrderPaperHeaderData = [];
        foreach ($purchaseOrderPaperHeaderItems as $purchaseOrderPaperHeaderItem) {
            $purchaseOrderPaperHeaderData[$purchaseOrderPaperHeaderItem->getId()] = $purchaseOrderPaperHeaderItem;
        }
        
        return $this->renderForm('default/_dashboard.html.twig', [
            'purchaseRequestHeaderCount' => $purchaseRequestHeaderCount,
            'purchaseRequestPaperHeaderCount' => $purchaseRequestPaperHeaderCount,
            'purchaseRequestHeaderCountApproval' => $purchaseRequestHeaderCountApproval,
            'purchaseRequestPaperHeaderCountApproval' => $purchaseRequestPaperHeaderCountApproval,
            'purchaseOrderHeaderCount' => $purchaseOrderHeaderCount,
            'purchaseOrderPaperHeaderCount' => $purchaseOrderPaperHeaderCount,
            'purchaseInvoiceHeaderCount' => $purchaseInvoiceHeaderCount,
            'saleOrderHeaderCount' => $saleOrderHeaderCount,
            'saleInvoiceHeaderCount' => $saleInvoiceHeaderCount,
            'inventoryRequestHeaderCount' => $inventoryRequestHeaderCount,
            'saleOrderHeaders' => $saleOrderHeaders,
            'saleOrderHeaderData' => $saleOrderHeaderData,
            'saleForm' => $saleForm,
            'saleCount' => $saleCount,
            'purchaseOrderHeaders' => $purchaseOrderHeaders,
            'purchaseOrderHeaderData' => $purchaseOrderHeaderData,
            'purchaseForm' => $purchaseForm,
            'purchaseCount' => $purchaseCount,
            'purchaseOrderPaperHeaders' => $purchaseOrderPaperHeaders,
            'purchaseOrderPaperHeaderData' => $purchaseOrderPaperHeaderData,
            'purchasePaperForm' => $purchasePaperForm,
            'purchasePaperCount' => $purchasePaperCount,
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
