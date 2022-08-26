<?php

namespace App\Controller;

use App\Entity\Sample;
use App\Form\SampleType;
use App\Form\Type\DataGridType;
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
        $form = $this->createForm(DataGridType::class, []);
        $form->handleRequest($request);
        $samples = $sampleRepository->match($form->get('filter')->getData(), $form->get('sort')->getData(), $form->get('pageSize')->getData(), $form->get('pageNumber')->getData());
        dump($form->getData());

        return $this->renderForm("sample/index.{$_format}.twig", [
            'form' => $form,
            'samples' => $samples,
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
