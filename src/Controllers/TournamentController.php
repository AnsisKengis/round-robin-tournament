<?php

namespace App\Controllers;

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

    public function index(): string
    {
        return $this->render('tournament/index');
    }

    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/');
        }

        try {
            $tournamentName = $_POST['tournament_name'] ?? '';
            $teamCount = (int)($_POST['team_count'] ?? 0);

            if (empty($tournamentName)) {
                throw new InvalidArgumentException('Tournament name is required');
            }

            if ($teamCount < 2 || $teamCount > 12) {
                throw new InvalidArgumentException('Number of teams must be between 2 and 12');
            }

            // Create tournament
            $tournament = $this->tournamentService->createTournament($tournamentName);

            // Generate teams
            $teams = $this->teamService->generateTeams($tournament, $teamCount);

            // Refresh the tournament entity to load the new teams
            $this->entityManager->refresh($tournament);

            // Generate games
            $games = $this->gameService->generateGames($tournament);

            $tournament->setStatus('completed');
            $this->entityManager->flush();

            $_SESSION['message'] = "Tournament '{$tournamentName}' created successfully with {$teamCount} teams!";
            $this->redirect('/tournament/results?id=' . $tournament->getId());
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('/');
        }
    }

    public function results(): string
    {
        $tournamentId = (int)($_GET['id'] ?? 0);
        $tournament = $this->tournamentService->getTournament($tournamentId);

        if (!$tournament) {
            $_SESSION['error'] = 'Tournament not found';
            $this->redirect('/');
        }

        $teams = $tournament->getTeams();
        $games = $tournament->getGames();

        return $this->render('tournament/results', [
            'tournament' => $tournament,
            'teams' => $teams,
            'games' => $games
        ]);
    }
}