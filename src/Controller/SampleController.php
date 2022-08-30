<?php

namespace App\Controller;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Sample;
use App\Form\SampleType;
use App\Grid\SampleGridType;
use App\Repository\SampleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sample')]
class SampleController extends AbstractController
{
    #[Route('/.{_format}', name: 'app_sample_index', methods: ['GET', 'POST'])]
    public function index(Request $request, SampleRepository $sampleRepository, $_format = 'html'): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(SampleGridType::class, $criteria);
        $form->handleRequest($request);
        dump($form->createView());

        return $this->renderForm("sample/index.{$_format}.twig", [
            'form' => $form,
            'count' => $sampleRepository->countBy($criteria),
            'samples' => $sampleRepository->matchBy($criteria),
        ]);
    }

    #[Route('/new', name: 'app_sample_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SampleRepository $sampleRepository): Response
    {
        $sample = new Sample();
        $form = $this->createForm(SampleType::class, $sample);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sampleRepository->add($sample, true);

            return $this->redirectToRoute('app_sample_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sample/new.html.twig', [
            'sample' => $sample,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sample_show', methods: ['GET'])]
    public function show(Sample $sample): Response
    {
        return $this->render('sample/show.html.twig', [
            'sample' => $sample,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_sample_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Sample $sample, SampleRepository $sampleRepository): Response
    {
        $form = $this->createForm(SampleType::class, $sample);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sampleRepository->add($sample, true);

            return $this->redirectToRoute('app_sample_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sample/edit.html.twig', [
            'sample' => $sample,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sample_delete', methods: ['POST'])]
    public function delete(Request $request, Sample $sample, SampleRepository $sampleRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sample->getId(), $request->request->get('_token'))) {
            $sampleRepository->remove($sample, true);
        }

        return $this->redirectToRoute('app_sample_index', [], Response::HTTP_SEE_OTHER);
    }
}
