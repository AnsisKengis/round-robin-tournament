<?php

namespace App\Tests\Unit;

use App\Entities\Team;
use App\Entities\Tournament;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class TeamTest extends TestCase
{
    private Team $team;
    private Tournament $tournament;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tournament = new Tournament('Test Tournament');
        $this->team = new Team('Test Team');
        $this->team->setTournament(tournament: $this->tournament);
    }

    public function testTeamCreation(): void
    {
        $this->assertEquals('Test Team', $this->team->getName());
        $this->assertEquals(0, $this->team->getWins());
        $this->assertEquals(0, $this->team->getLosses());
        $this->assertEquals($this->tournament, $this->team->getTournament());
    }

    public function testTeamNameCannotBeEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Team name cannot be empty');

        new Team(name: '');
    }

    public function testIncrementWins(): void
    {
        $this->team->incrementWins();
        $this->assertEquals(1, $this->team->getWins());

        $this->team->incrementWins();
        $this->assertEquals(2, $this->team->getWins());
    }

    public function testIncrementLosses(): void
    {
        $this->team->incrementLosses();
        $this->assertEquals(1, $this->team->getLosses());

        $this->team->incrementLosses();
        $this->assertEquals(2, $this->team->getLosses());
    }

    public function testTeamStatsAreIndependent(): void
    {
        $team2 = new Team('Team 2');
        $team2->setTournament($this->tournament);

        $this->team->incrementWins();
        $this->team->incrementLosses();

        $team2->incrementWins();
        $team2->incrementLosses();

        $this->assertEquals(1, $this->team->getWins());
        $this->assertEquals(1, $this->team->getLosses());
        $this->assertEquals(1, $team2->getWins());
        $this->assertEquals(1, $team2->getLosses());
    }
}
