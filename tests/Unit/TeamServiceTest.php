<?php

namespace App\Tests\Unit;

use App\Entities\Team;
use App\Entities\Tournament;
use App\Services\TeamService;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use RuntimeException;
use App\Entities\Game;

class TeamServiceTest extends TestCase
{
    private TeamService $teamService;
    private EntityManager|MockObject $entityManager;
    private Tournament $tournament;

    protected function setUp(): void
    {
        parent::setUp();
        $this->entityManager = $this->createMock(EntityManager::class);
        $this->teamService = new TeamService($this->entityManager);
        $this->tournament = new Tournament('Test Tournament');
    }

    public function testGenerateTeams(): void
    {
        $count = 4;
        $expectedTeams = [];

        // Create expected teams
        for ($i = 0; $i < $count; $i++) {
            $team = new Team("Team " . ($i + 1));
            $team->setTournament($this->tournament);
            $expectedTeams[] = $team;
        }

        // Set up expectations
        $this->entityManager
            ->expects($this->exactly($count))
            ->method('persist')
            ->with($this->isInstanceOf(Team::class));

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        // Execute
        $teams = $this->teamService->generateTeams($this->tournament, $count);

        // Assert
        $this->assertCount($count, $teams);

        foreach ($teams as $team) {
            $this->assertInstanceOf(Team::class, $team);
            $this->assertEquals($this->tournament, $team->getTournament());
        }
    }

    public function testGenerateTeamsWithInvalidCount(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Failed to generate teams: Number of teams must be between 2 and 12');

        $this->teamService->generateTeams($this->tournament, 1);
    }

    public function testGetTeam(): void
    {
        $teamId = 1;
        $expectedTeam = new Team('Test Team');
        $repository = $this->createMock(\Doctrine\ORM\EntityRepository::class);

        $this->entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with(Team::class)
            ->willReturn($repository);

        $repository
            ->expects($this->once())
            ->method('find')
            ->with($teamId)
            ->willReturn($expectedTeam);

        $team = $this->teamService->getTeam($teamId);

        $this->assertEquals($expectedTeam, $team);
    }

    public function testUpdateTeamStats(): void
    {
        // Create mocks
        $game = $this->createMock(Game::class);
        $team1 = $this->createMock(Team::class);
        $team2 = $this->createMock(Team::class);

        // Setup game relationships first
        $game->method('getTeam1')->willReturn($team1);
        $game->method('getTeam2')->willReturn($team2);
        $game->method('getWinner')->willReturn($team1);

        // Expect the increment methods to be called once
        $team1->expects($this->once())->method('incrementWins');
        $team2->expects($this->once())->method('incrementLosses');

        // Expect database to be updated
        $this->entityManager->expects($this->once())->method('flush');

        $this->teamService->updateTeamStats($game);
    }
}
