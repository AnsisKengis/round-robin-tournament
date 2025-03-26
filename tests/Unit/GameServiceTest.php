<?php

namespace App\Tests\Unit;

use App\Entities\Game;
use App\Entities\Team;
use App\Entities\Tournament;
use App\Services\GameService;
use App\Services\TeamService;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use RuntimeException;

class GameServiceTest extends TestCase
{
    private GameService $gameService;
    private EntityManager|MockObject $entityManager;
    private TeamService|MockObject $teamService;
    private Tournament $tournament;
    private array $teams;

    protected function setUp(): void
    {
        parent::setUp();
        $this->entityManager = $this->createMock(EntityManager::class);
        $this->teamService = $this->createMock(TeamService::class);
        $this->gameService = new GameService($this->entityManager, $this->teamService);
        $this->tournament = new Tournament('Test Tournament');

        // Create mock teams
        $this->teams = [];
        for ($i = 0; $i < 4; $i++) {
            $team = $this->createMock(Team::class);
            $team->method('getId')->willReturn($i + 1);
            $team->method('getName')->willReturn("Team " . ($i + 1));
            $team->method('getTournament')->willReturn($this->tournament);
            $this->teams[] = $team;
        }
    }

    public function testGenerateGamesWithEvenNumberOfTeams(): void
    {
        // Set up tournament teams
        $this->tournament->getTeams()->add($this->teams[0]);
        $this->tournament->getTeams()->add($this->teams[1]);
        $this->tournament->getTeams()->add($this->teams[2]);
        $this->tournament->getTeams()->add($this->teams[3]);

        // Set up expectations
        $this->entityManager
            ->expects($this->exactly(6)) // 3 rounds * 2 games per round
            ->method('persist')
            ->with($this->isInstanceOf(Game::class));

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->teamService
            ->expects($this->exactly(6))
            ->method('updateTeamStats')
            ->with($this->isInstanceOf(Game::class));

        // Execute
        $games = $this->gameService->generateGames($this->tournament);

        // Assert
        $this->assertCount(6, $games); // 3 rounds * 2 games per round
        foreach ($games as $game) {
            $this->assertInstanceOf(Game::class, $game);
            $this->assertEquals($this->tournament, $game->getTournament());
            $this->assertNotNull($game->getTeam1Score());
            $this->assertNotNull($game->getTeam2Score());
            $this->assertNotNull($game->getWinner());
            $this->assertEquals('completed', $game->getStatus());
        }
    }

    public function testGenerateGamesWithOddNumberOfTeams(): void
    {
        // Set up tournament teams (3 teams)
        $this->tournament->getTeams()->add($this->teams[0]);
        $this->tournament->getTeams()->add($this->teams[1]);
        $this->tournament->getTeams()->add($this->teams[2]);

        // Set up expectations
        $this->entityManager
            ->expects($this->exactly(3)) // 3 rounds * 1 game per round
            ->method('persist')
            ->with($this->isInstanceOf(Game::class));

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->teamService
            ->expects($this->exactly(3))
            ->method('updateTeamStats')
            ->with($this->isInstanceOf(Game::class));

        // Execute
        $games = $this->gameService->generateGames($this->tournament);

        // Assert
        $this->assertCount(3, $games); // 3 rounds * 1 game per round
        foreach ($games as $game) {
            $this->assertInstanceOf(Game::class, $game);
            $this->assertEquals($this->tournament, $game->getTournament());
            $this->assertNotNull($game->getTeam1Score());
            $this->assertNotNull($game->getTeam2Score());
            $this->assertNotNull($game->getWinner());
            $this->assertEquals('completed', $game->getStatus());
        }
    }

    public function testCannotGenerateGamesWithLessThanTwoTeams(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Failed to generate tournament games: Tournament must have at least 2 teams');

        // Set up tournament with only one team
        $this->tournament->getTeams()->add($this->teams[0]);

        $this->gameService->generateGames($this->tournament);
    }
}
