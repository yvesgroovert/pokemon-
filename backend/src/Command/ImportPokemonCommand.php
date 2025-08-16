<?php

namespace App\Command;

use App\Entity\Gallery;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:import-pokemon',
    description: 'Importe les 151 premiers Pokémon depuis la PokeAPI vers la base de données locale.'
)]
class ImportPokemonCommand extends Command
{
    private HttpClientInterface $client;
    private EntityManagerInterface $em;

    public function __construct(HttpClientInterface $client, EntityManagerInterface $em)
    {
        parent::__construct();
        $this->client = $client;
        $this->em = $em;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>🔄 Démarrage de l’import des Pokémon...</info>');

        $importedCount = 0;

        try {
            $response = $this->client->request('GET', 'https://pokeapi.co/api/v2/pokemon?limit=151');
            $pokemonList = $response->toArray()['results'] ?? [];

            foreach ($pokemonList as $pokemonData) {
                $details = $this->fetchPokemonDetails($pokemonData['url']);

                if (!$details) {
                    $output->writeln("<comment>⚠️ Impossible de récupérer les détails de {$pokemonData['name']}</comment>");
                    continue;
                }

                $pokeApiId = $details['id'];
                $name = $details['name'];
                $sprite = $details['sprites']['front_default'] ?? null;

                $types = array_map(fn($t) => $t['type']['name'], $details['types']);
                $typesStr = implode(',', $types);

                if ($this->pokemonExists($pokeApiId)) {
                    $output->writeln("⏭️  <comment>$name (ID $pokeApiId) déjà existant. Skippé.</comment>");
                    continue;
                }

                $this->persistPokemon($pokeApiId, $name, $sprite, $typesStr);
                $output->writeln("✅ <info>$name (ID $pokeApiId) importé.</info>");
                $importedCount++;
            }

            $this->em->flush();
            $output->writeln("<info>✅ Import terminé : $importedCount Pokémon ajoutés à la base.</info>");

            return Command::SUCCESS;
        } catch (TransportExceptionInterface $e) {
            $output->writeln("<error>Erreur réseau : {$e->getMessage()}</error>");
        } catch (\Throwable $e) {
            $output->writeln("<error>Erreur inattendue : {$e->getMessage()}</error>");
        }

        return Command::FAILURE;
    }

    private function fetchPokemonDetails(string $url): ?array
    {
        try {
            $response = $this->client->request('GET', $url);
            return $response->toArray();
        } catch (\Throwable) {
            return null;
        }
    }

    private function pokemonExists(int $pokeApiId): bool
    {
        return (bool) $this->em->getRepository(Gallery::class)->findOneBy(['PokeApiId' => $pokeApiId]);
    }

    private function persistPokemon(int $id, string $name, ?string $sprite, string $types): void
    {
        $pokemon = new Gallery();
        $pokemon->setPokeApiId($id);
        $pokemon->setName($name);
        $pokemon->setSpriteUrl($sprite);
        $pokemon->setTypes($types);

        $this->em->persist($pokemon);
    }
}
