<?php

namespace App\Controllers;

use App\Entities\Tournament;
use App\Services\TournamentService;
use App\Services\TeamService;
use App\Services\GameService;
use Doctrine\ORM\EntityManager;
use InvalidArgumentException;

class TournamentController extends BaseController
{
    private TournamentService $tournamentService;
    private TeamService $teamService;
    private GameService $gameService;

    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager);
        $this->tournamentService = new TournamentService($entityManager);
        $this->teamService = new TeamService($entityManager);
        $this->gameService = new GameService($entityManager, $this->teamService);
    }

    public function create(array $data): string
    {
        try {
            $name = $data['name'] ?? '';
            $teamCount = (int)($data['teamCount'] ?? 0);

            if (empty($name) || $teamCount < 2 || $teamCount > 12) {
                throw new InvalidArgumentException('Invalid tournament data');
            }

            $tournament = $this->tournamentService->createTournament($name);
            $teams = $this->teamService->generateTeams($tournament, $teamCount);

            // Refresh the tournament entity to load the new teams
            $this->entityManager->refresh($tournament);

            $this->gameService->generateGames($tournament);

            $tournament->setStatus('completed');
            $this->entityManager->flush();

            return json_encode([
                'status' => 'success',
                'tournamentId' => $tournament->getId(),
                'redirectUrl' => "/tournament/{$tournament->getId()}"
            ]);
        } catch (\Exception $e) {
            http_response_code(400);
            return json_encode(['error' => $e->getMessage()]);
        }
    }

    public function results(?int $id = null): string
    {
        try {
            if (!$id) {
                throw new InvalidArgumentException('Tournament ID is required');
            }

            $tournament = $this->tournamentService->getTournament($id);
            if (!$tournament) {
                throw new InvalidArgumentException('Tournament not found');
            }

            $teams = $tournament->getTeams()->toArray();
            $games = $tournament->getGames()->toArray();

            $data = [
                'tournament' => [
                    'id' => $tournament->getId(),
                    'name' => $tournament->getName(),
                    'status' => $tournament->getStatus()
                ],
                'teams' => array_map(function($team) {
                    return [
                        'id' => $team->getId(),
                        'name' => $team->getName(),
                        'wins' => $team->getWins(),
                        'losses' => $team->getLosses(),
                        'winRate' => $this->calculateWinRate($team)
                    ];
                }, $teams),
                'games' => array_map(function($game) {
                    return [
                        'id' => $game->getId(),
                        'round' => $game->getRound(),
                        'team1' => [
                            'name' => $game->getTeam1()->getName(),
                            'score' => $game->getTeam1Score()
                        ],
                        'team2' => [
                            'name' => $game->getTeam2()->getName(),
                            'score' => $game->getTeam2Score()
                        ],
                        'winner' => $game->getWinner() ? $game->getWinner()->getName() : null,
                        'status' => $game->getStatus()
                    ];
                }, $games)
            ];

            return json_encode($data);
        } catch (\Exception $e) {
            http_response_code(400);
            return json_encode(['error' => $e->getMessage()]);
        }
    }

    private function calculateWinRate($team): float
    {
        $totalGames = $team->getWins() + $team->getLosses();
        if ($totalGames === 0) {
            return 0.0;
        }
        return round(($team->getWins() / $totalGames) * 100, 1);
    }
}
