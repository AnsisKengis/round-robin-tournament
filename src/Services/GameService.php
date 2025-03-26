<?php

namespace App\Services;

use App\Entities\Game;
use App\Entities\Team;
use App\Entities\Tournament;
use App\Services\TeamService;
use Doctrine\ORM\EntityManager;
use InvalidArgumentException;

class GameService
{
    private EntityManager $entityManager;
    private TeamService $teamService;

    public function __construct(
        EntityManager $entityManager,
        TeamService $teamService
        )
    {
        $this->entityManager = $entityManager;
        $this->teamService = $teamService;
    }

    public function generateGames(Tournament $tournament): array
    {
        try {
            $games = [];
            $teams = $tournament->getTeams()->toArray();
            $teamCount = count($teams);

            if ($teamCount < 2) {
                throw new InvalidArgumentException('Tournament must have at least 2 teams');
            }

            if ($teamCount % 2 !== 0) {
                $teams[] = 'BYE';
                $teamCount++;
            }

            $numberOfRounds = $teamCount - 1;

            // Once starting generating games and scores we change tournament status to in_progress;
            $tournament->setStatus('in_progress');

            for ($round = 0; $round < $numberOfRounds; $round++) {
                for ($i = 0; $i < floor($teamCount / 2); $i++) {
                    $team1 = $teams[$i];
                    $team2 = $teams[$teamCount - 1 - $i];

                    if (!$team1 instanceof Team || !$team2 instanceof Team) {
                        continue;
                    }

                    try {
                        $game = new Game($tournament, $team1, $team2);
                        $game->setRound($round + 1);
                        $game->setStatus('completed');

                        try {
                            $game = $this->simulateGameAndDetermineWinner($game);
                            $this->entityManager->persist($game);
                            $this->teamService->updateTeamStats($game);
                            $games[] = $game;
                        } catch (\Exception $e) {
                            error_log("Error simulating game: " . $e->getMessage());
                            throw new \RuntimeException(message: 'Failed to simulate games: ' . $e->getMessage());
                        }
                    } catch (InvalidArgumentException $e) {
                        error_log("Error creating game: " . $e->getMessage());
                        continue;
                    }
                }

                $teams = $this->rotateTeams($teams);
            }

            $this->entityManager->flush();
            return $games;
        } catch (\Exception $e) {
            error_log("Error in game generation: " . $e->getMessage());
            throw new \RuntimeException(message: 'Failed to generate tournament games: ' . $e->getMessage());
        }
    }

    private function simulateGameAndDetermineWinner($game): Game
    {
        try {
            do {
                $team1Score = rand(80, 130);
                $team2Score = rand(80, 130);
            } while ($team1Score === $team2Score);

            $game->setTeam1Score($team1Score);
            $game->setTeam2Score($team2Score);

            // Determine winner based on scores
            $game->setWinner($team1Score > $team2Score ? $game->getTeam1() : $game->getTeam2());

            return $game;
        } catch (\Exception $e) {
            error_log("Error simulating game: " . $e->getMessage());
            throw $e;
        }
    }

    private function rotateTeams(array $teams): array
    {
        if (count($teams) < 2) {
            return $teams;
        }

        return array_merge(
            [$teams[0]], // Keep the first element fixed
            array_slice($teams, -1), // Last element comes next
            array_slice($teams, 1, count($teams) - 2) // Followed by the middle part
        );
    }
}
