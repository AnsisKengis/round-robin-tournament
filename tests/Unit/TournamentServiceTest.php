<?php

namespace App\Tests\Unit;

use App\Entities\Tournament;
use App\Services\TournamentService;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use RuntimeException;

class TournamentServiceTest extends TestCase
{
    private TournamentService $tournamentService;
    private EntityManager|MockObject $entityManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->entityManager = $this->createMock(EntityManager::class);
        $this->tournamentService = new TournamentService($this->entityManager);
    }

    public function testCreateTournament(): void
    {
        $tournamentName = 'Test Tournament';

        // Set up expectations
        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(Tournament::class));

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        // Execute
        $tournament = $this->tournamentService->createTournament($tournamentName);

        // Assert
        $this->assertInstanceOf(Tournament::class, $tournament);
        $this->assertEquals($tournamentName, $tournament->getName());
        $this->assertEquals('upcoming', $tournament->getStatus());
        $this->assertCount(0, $tournament->getTeams());
        $this->assertCount(0, $tournament->getGames());
    }

    public function testGetTournament(): void
    {
        $tournamentId = 1;
        $expectedTournament = new Tournament('Test Tournament');
        $repository = $this->createMock(\Doctrine\ORM\EntityRepository::class);

        $this->entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with(Tournament::class)
            ->willReturn($repository);

        $repository
            ->expects($this->once())
            ->method('find')
            ->with($tournamentId)
            ->willReturn($expectedTournament);

        $tournament = $this->tournamentService->getTournament($tournamentId);

        $this->assertEquals($expectedTournament, $tournament);
    }

    public function testGetNonExistentTournament(): void
    {
        // Tournament with id large enough to not exist in database
        $tournamentId = 99999;
        $repository = $this->createMock(\Doctrine\ORM\EntityRepository::class);

        $this->entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with(Tournament::class)
            ->willReturn($repository);

        $repository
            ->expects($this->once())
            ->method('find')
            ->with($tournamentId)
            ->willReturn(null);

        $tournament = $this->tournamentService->getTournament($tournamentId);

        $this->assertNull($tournament);
    }

    public function testCreateTournamentWithError(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Failed to generate tournament: Database error');

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->willThrowException(new \Exception('Database error'));

        $this->tournamentService->createTournament('Test Tournament');
    }
} 