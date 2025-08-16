<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class DownloadpokemonController extends AbstractController
{
    #[Route('/downloadpokemon', name: 'app_downloadpokemon')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/DownloadpokemonController.php',
        ]);
    }
}
