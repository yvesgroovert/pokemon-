<?php

namespace App\Controller;

use App\Repository\GalleryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

final class SentFrontController extends AbstractController
{
    #[Route('/sent/front', name: 'app_sent_front', methods: ['GET'])]
    public function index(GalleryRepository $galleryRepository): JsonResponse
    {
        $galleries = $galleryRepository->findAll();

        $data = array_map(function ($gallery) {
            return [
                'id' => $gallery->getId(),
                'name' => $gallery->getName(),
                'types' => $gallery->getTypes(),
                'imageUrl' => $gallery->getSpriteUrl(),
                'pokeApiId' => $gallery->getPokeApiId(),
            ];
        }, $galleries);

        return $this->json($data);
    }
}

//