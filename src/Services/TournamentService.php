<?php

namespace App\Services;

use App\Entities\Tournament;
use Doctrine\ORM\EntityManager;

class TournamentService
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createTournament(string $name): Tournament
    {
        try {
            $tournament = new Tournament($name);

            $this->entityManager->persist($tournament);
            $this->entityManager->flush();

            return $tournament;
        }

        catch (\Exception $e) {
            error_log("Error in tournament generation: " . $e->getMessage());
            throw new \RuntimeException(message: 'Failed to generate tournament: ' . $e->getMessage());
        }
    }

    public function getTournament(int $id): ?Tournament
    {
        return $this->entityManager->getRepository(Tournament::class)->find($id);
    }
}