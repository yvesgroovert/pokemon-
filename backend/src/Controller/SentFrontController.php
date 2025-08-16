<?php

namespace App\Controller;

use App\Repository\GalleryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

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
                'types' => method_exists($gallery, 'getTypes') ? $gallery->getTypes() : null,
                // Ajoute le spriteUrl en fonction du pokeId (ici j'utilise getId() comme exemple)
                'spriteUrl' => 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/' . $gallery->getId() . '.png',
            ];
        }, $galleries);

        return $this->json($data);
    }
}
