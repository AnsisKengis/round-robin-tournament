<?php

namespace App\Tests\Unit;

use App\Entities\Tournament;
use PHPUnit\Framework\TestCase;

class TournamentTest extends TestCase
{
    private Tournament $tournament;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tournament = new Tournament('Test Tournament');
    }

    public function testTournamentCreation(): void
    {
        $this->assertEquals('Test Tournament', $this->tournament->getName());
        $this->assertEquals('upcoming', $this->tournament->getStatus());
        $this->assertCount(0, $this->tournament->getTeams());
        $this->assertCount(0, $this->tournament->getGames());
    }

    public function testSetStatus(): void
    {
        $this->tournament->setStatus('in_progress');
        $this->assertEquals('in_progress', $this->tournament->getStatus());

        $this->tournament->setStatus('completed');
        $this->assertEquals('completed', $this->tournament->getStatus());
    }
}