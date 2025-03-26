<?php

namespace App\Services;

use App\Entities\Team;
use App\Entities\Tournament;
use App\Entities\Game;
use Doctrine\ORM\EntityManager;
use Faker\Factory as Faker;
use Faker\Generator;
use InvalidArgumentException;

class TeamService
{
    private EntityManager $entityManager;
    private Generator $faker;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->faker = Faker::create();
    }

    public function generateTeams(Tournament $tournament, int $count): array
    {
        try {
            if ($count < 2 || $count > 12) {
                throw new InvalidArgumentException('Number of teams must be between 2 and 12');
            }

            $teams = [];
            $usedNames = [];

            for ($i = 0; $i < $count; $i++) {
                try {
                    $name = $this->generateUniqueTeamName($usedNames);
                    $team = new Team($name);
                    $team->setTournament($tournament);

                    $this->entityManager->persist($team);
                    $teams[] = $team;
                    $usedNames[] = $name;
                } catch (\Exception $e) {
                    error_log("Error creating team {$name}: " . $e->getMessage());
                    throw new \RuntimeException('Failed to create team: ' . $e->getMessage());
                }
            }

            $this->entityManager->flush();
            return $teams;
        } catch (\Exception $e) {
            error_log("Error in team generation: " . $e->getMessage());
            throw new \RuntimeException('Failed to generate teams: ' . $e->getMessage());
        }
    }

    private function generateUniqueTeamName(array $usedNames): string
    {
        $suffixes = ['Wolves', 'Eagles', 'Dragons', 'Storm', 'Falcons', 'Titans', 'Sharks', 'Raptors', 'Panthers'];

        do {
            try {
                $name = ucfirst($this->faker->colorName) . ' ' . $this->faker->randomElement($suffixes);

                // Check if name is already used in this batch
                if (in_array($name, $usedNames)) {
                    continue;
                }

                return $name;
            } catch (\Exception $e) {
                error_log("Error generating team name: " . $e->getMessage());
                continue;
            }
        } while (true);
    }

    public function updateTeamStats(Game $game): void
    {
        $team1 = $game->getTeam1();
        $team2 = $game->getTeam2();
        $winner = $game->getWinner();

        $team1Method = $team1 === $winner ? 'incrementWins' : 'incrementLosses';
        $team2Method = $team2 === $winner ? 'incrementWins' : 'incrementLosses';

        $team1->$team1Method();
        $team2->$team2Method();

        $this->entityManager->flush();
    }

    public function getTeam(int $id): ?Team
    {
        return $this->entityManager->getRepository(Team::class)->find($id);
    }
}