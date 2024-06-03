<?php

namespace App\Controller\AdminAd;

use App\Entity\Ad;
use App\Helper\ConversionHelper;
use App\Repository\TypeRepository;
use App\Form\AdFormType\AdStep1FormType;
use App\Form\AdFormType\AdStep2FormType;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\AdFormType\AdStep10FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAdCreateController extends AbstractController
{
    public function __construct(protected EntityManagerInterface $em)
    {        
    }
    
    #[Route('/become-a-host', name: 'admin_ad_intro')]
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté pour accéder à cette page')]
    public function intro(): Response
    {
        return $this->render('admin_ad/intro.html.twig', [
            'withFooter' => true
        ]);
    }

    #[Route('/become-a-host/structure', name: 'admin_ad_structure')]
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté pour accéder à cette page')]
    public function structure(Request $request, TypeRepository $typeRepository): Response
    {
        $types = $typeRepository->findAll();
        $ad = new Ad;
        $form = $this->createForm(AdStep10FormType::class, $ad, [
            'types' => $types,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($ad);
            $this->em->flush();
            return $this->redirectToRoute('admin_ad_floor', ['id' => $ad->getId()]);
        }

        return $this->render('admin_ad/structure.html.twig', [
            'form' => $form,
            'withFooter' => true,
            'types' => $types
        ]);
    }

    #[Route('/become-a-host/floor-plan/{id}', name: 'admin_ad_floor')]
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté pour accéder à cette page')]
    public function floor(Request $request, int $id): Response
    {
        $ad = $this->em->getRepository(Ad::class)->find($id);

        if (!$ad) {
            return $this->redirectToRoute('admin_ad_floor');
        }
        $form = $this->createForm(AdStep1FormType::class, $ad);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($ad);
            $this->em->flush();
            return $this->redirectToRoute('admin_ad_guest', ['id' => $ad->getId()]);
        }

        return $this->render('admin_ad/floor.html.twig', [
            'form' => $form,
            'withFooter' => true
        ]);
    }

    #[Route('/become-a-host/guests/{id}', name: 'admin_ad_guest')]
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté pour accéder à cette page')]
    public function guest(Request $request, int $id): Response
    {
        $ad =  $this->em->getRepository(Ad::class)->find($id);

        if (!$ad) {
            return $this->redirectToRoute('admin_ad_floor');
        }
        $form = $this->createForm(AdStep2FormType::class, $ad);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($ad);
            $this->em->flush();

            return $this->redirectToRoute('admin_ad_location', ['id' => $ad->getId()]);
        }

        return $this->render('admin_ad/guest.html.twig', [
            'form' => $form,
            'withFooter' => true
        ]);
    }

    #[Route('/become-a-host/location/{id}', name: 'admin_ad_location')]
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté pour accéder à cette page')]
    public function location(Request $request, int $id)
    {
        $ad = $this->em->getRepository(Ad::class)->find($id);

        if (!$ad) {
            return $this->redirectToRoute('admin_ad_floor');
        }

        // $form = $this->createForm(AdStep3FormType::class, $ad);

        // $form->handleRequest($request);

        // if ($form->isSubmitted() && $form->isValid()) {
        //     $em->persist($ad);
        //     $em->flush();

        //     return $this->redirectToRoute('admin_ad_stand', ['id' => $ad->getId()]);
        // }

        if ($request->isMethod('POST')) {
            $locality = $request->request->get('locality');
            $countryName = $request->request->get('country');
            $state = $request->request->get('state');
            $lat = $request->request->get('lat');
            $lnt = $request->request->get('lng');

            if ($locality && $countryName) {
                $countryCode = ConversionHelper::countryNameToAlpha2($countryName);

                if ($countryCode) {
                    $ad->setCity($locality)
                        ->setCountry($countryCode)
                        ->setState($state)
                        ->setLatitude($lat)
                        ->setLongitude($lnt);
                    $this->em->persist($ad);
                    $this->em->flush();

                    return $this->redirectToRoute('admin_ad_stand', ['id' => $ad->getId()]);
                } else {
                    $this->addFlash('error', 'Le pays fourni n\'est pas valide.');
                }
            }
        }
        return $this->render('admin_ad/location.html.twig', [
            // 'form' => $form,
            'ad' => $ad,
            'withFooter' => true,
            'google_api_key' => 'AIzaSyCY6zB0itdFxJlLSpzgipkKTS1EdyHnCSk',
        ]);
    }
}
