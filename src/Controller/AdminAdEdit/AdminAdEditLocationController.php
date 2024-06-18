<?php

namespace App\Controller\AdminAdEdit;

use App\Google\GoogleService;
use App\Helper\ConversionHelper;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAdEditLocationController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/hosting/editor/{id}/location', name: 'admin_edit_location', methods: ['GET', 'POST'])]
    public function location(Request $request, int $id, AdRepository $adRepository, GoogleService $googleService): Response
    {
        $ad = $adRepository->find($id);

        if (!$ad) {
            return $this->redirectToRoute('admin_ad_listing');
        }

        $this->denyAccessUnlessGranted('CAN_EDIT', $ad, "Vous n'êtes pas le propriétaire de cette annonce");
       
        if ($request->isMethod('POST')) {
            $adress = $request->request->get('location');
            $postalCode = $request->request->get('postal_code');
            $locality = $request->request->get('locality');
            $countryName = $request->request->get('country');
            $state = $request->request->get('state');
            $lat = $request->request->get('lat');
            $lnt = $request->request->get('lng');

            if ($locality && $countryName) {
                $countryCode = ConversionHelper::countryNameToAlpha2($countryName);

                if ($countryCode) {
                    $ad->setCity($locality)
                        ->setAdress($adress)
                        ->setPostalCode($postalCode)
                        ->setCountry($countryCode)
                        ->setState($state)
                        ->setLatitude($lat)
                        ->setLongitude($lnt);
                    $this->em->flush();
                    $this->addFlash('success', 'Votre modification a bien été prise en compte');

                    return $this->redirectToRoute('admin_edit_location', ['id' => $ad->getId()]);
                } else {
                    $this->addFlash('error', 'Le pays fourni n\'est pas valide.');
                }
            }
        }

        return $this->render('admin_ad_edit/location.html.twig', [
            'withFooter' => true,
            'ad' => $ad,
            'google_api_key' => $googleService->getGoogleKey(),
        ]);
    }
    
    #[Route('/hosting/editor/{id}/location/mobile', name: 'admin_edit_location_mobile', methods: ['GET', 'POST'])]
    public function locationMobile(Request $request, int $id, AdRepository $adRepository, GoogleService $googleService): Response
    {
        $ad = $adRepository->find($id);

        if (!$ad) {
            return $this->redirectToRoute('admin_ad_listing');
        }

        $this->denyAccessUnlessGranted('CAN_EDIT', $ad, "Vous n'êtes pas le propriétaire de cette annonce");

        if ($request->isMethod('POST')) {
            $adress = $request->request->get('location');
            $postalCode = $request->request->get('postal_code');
            $locality = $request->request->get('locality');
            $countryName = $request->request->get('country');
            $state = $request->request->get('state');
            $lat = $request->request->get('lat');
            $lnt = $request->request->get('lng');

            if ($locality && $countryName) {
                $countryCode = ConversionHelper::countryNameToAlpha2($countryName);

                if ($countryCode) {
                    $ad->setCity($locality)
                        ->setAdress($adress)
                        ->setPostalCode($postalCode)
                        ->setCountry($countryCode)
                        ->setState($state)
                        ->setLatitude($lat)
                        ->setLongitude($lnt);
                    $this->em->flush();
                    $this->addFlash('success', 'Votre modification a bien été prise en compte');

                    return $this->redirectToRoute('admin_edit_location_mobile', ['id' => $ad->getId()]);
                } else {
                    $this->addFlash('error', 'Le pays fourni n\'est pas valide.');
                }
            }
        }

        return $this->render('admin_ad_edit/location_mobile.html.twig', [
            'withFooter' => true,
            'ad' => $ad,
            'google_api_key' => $googleService->getGoogleKey(),
        ]);
    }
}