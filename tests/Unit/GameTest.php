<?php

namespace App\Tests\Unit;

use App\Entities\Game;
use App\Entities\Team;
use App\Entities\Tournament;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class GameTest extends TestCase
{
    private Game $game;
    private Tournament $tournament;
    private Team|MockObject $team1;
    private Team|MockObject $team2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tournament = new Tournament('Test Tournament');

        // Create mock teams with IDs
        $this->team1 = $this->createMock(Team::class);
        $this->team2 = $this->createMock(Team::class);

        // Set up the mock teams
        $this->team1->method('getId')->willReturn(1);
        $this->team2->method('getId')->willReturn(2);
        $this->team1->method('getName')->willReturn('Team 1');
        $this->team2->method('getName')->willReturn('Team 2');
        $this->team1->method('getTournament')->willReturn($this->tournament);
        $this->team2->method('getTournament')->willReturn($this->tournament);

        $this->game = new Game($this->tournament, $this->team1, $this->team2);
    }

    public function testGameCreation(): void
    {
        $this->assertEquals($this->tournament, $this->game->getTournament());
        $this->assertEquals($this->team1, $this->game->getTeam1());
        $this->assertEquals($this->team2, $this->game->getTeam2());
        $this->assertEquals('upcoming', $this->game->getStatus());
        $this->assertNull($this->game->getTeam1Score());
        $this->assertNull($this->game->getTeam2Score());
        $this->assertNull($this->game->getWinner());
    }

    public function testCannotCreateGameWithSameTeam(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Team 1 and Team 2 cannot be the same team');

        // Create a mock team with the same ID
        $sameTeam = $this->createMock(Team::class);
        $sameTeam->method('getId')->willReturn(1);
        $sameTeam->method('getName')->willReturn('Team 1');
        $sameTeam->method('getTournament')->willReturn($this->tournament);

        new Game($this->tournament, $this->team1, $sameTeam);
    }

    public function testSetScores(): void
    {
        $this->game->setTeam1Score(100);
        $this->game->setTeam2Score(90);

        $this->assertEquals(100, $this->game->getTeam1Score());
        $this->assertEquals(90, $this->game->getTeam2Score());
    }

    public function testSetWinner(): void
    {
        $this->game->setTeam1Score(100);
        $this->game->setTeam2Score(90);
        $this->game->setWinner($this->team1);

        $this->assertEquals($this->team1, $this->game->getWinner());
    }

    public function testCannotSetWinnerFromDifferentTeam(): void
    {
        $otherTeam = $this->createMock(Team::class);
        $otherTeam->method('getId')->willReturn(3);
        $otherTeam->method('getName')->willReturn('Other Team');
        $otherTeam->method('getTournament')->willReturn($this->tournament);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Winner must be one of the teams playing in this game');

        $this->game->setWinner($otherTeam);
    }

    public function testGameStatus(): void
    {
        $this->assertTrue($this->game->isUpcoming());

        $this->game->setStatus('in_progress');
        $this->assertTrue($this->game->isInProgress());

        $this->game->setStatus('completed');
        $this->assertTrue($this->game->isCompleted());
    }

    public function testInvalidStatus(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid game status');

        $this->game->setStatus('invalid_status');
    }

    public function testSetRound(): void
    {
        $this->game->setRound(1);
        $this->assertEquals(1, $this->game->getRound());
    }
}