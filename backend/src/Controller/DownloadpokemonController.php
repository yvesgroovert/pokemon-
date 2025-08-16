<?php
namespace App\Controller;

use App\Entity\Gallery;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DownloadpokemonController extends AbstractController
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    #[Route('/import-pokemon', name: 'import_pokemon')]
    public function importPokemon(EntityManagerInterface $em): JsonResponse
    {
        try {
            $response = $this->client->request('GET', 'https://pokeapi.co/api/v2/pokemon?limit=151');
            $data = $response->toArray();

            foreach ($data['results'] as $pokemonData) {
                $detailsResponse = $this->client->request('GET', $pokemonData['url']);
                $details = $detailsResponse->toArray();

                $pokeApiId = $details['id'];
                $name = $details['name'];
                $sprite = $details['sprites']['front_default'] ?? null;

                $typesArray = array_map(function ($typeInfo) {
                    return $typeInfo['type']['name'];
                }, $details['types']);

                $types = implode(',', $typesArray);

                // Vérifier si déjà existant
                $existing = $em->getRepository(Gallery::class)->findOneBy(['PokeApiId' => $pokeApiId]);
                if ($existing) {
                    continue;
                }

                $pokemon = new Gallery();
                $pokemon->setPokeApiId($pokeApiId);
                $pokemon->setName($name);
                $pokemon->setSpriteUrl($sprite);
                $pokemon->setTypes($types);

                $em->persist($pokemon);
            }

            $em->flush();

            return new JsonResponse(['message' => 'Importation terminée.']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }
} 
//
