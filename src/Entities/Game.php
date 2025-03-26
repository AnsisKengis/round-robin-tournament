<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity]
#[ORM\Table(name: 'games')]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'games')]
    #[ORM\JoinColumn(name: 'tournament_id', nullable: false)]
    private Tournament $tournament;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Team $team1;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Team $team2;

    #[ORM\Column]
    private int $round;

    #[ORM\Column(length: 20, options: ["check" => "status IN ('upcoming', 'in_progress', 'completed')"])]
    private string $status = 'upcoming';

    #[ORM\Column(nullable: true)]
    private ?int $team1Score = null;

    #[ORM\Column(nullable: true)]
    private ?int $team2Score = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Team $winner = null;

    public function __construct(Tournament $tournament, Team $team1, Team $team2)
    {
        try {
            if ($team1->getId() === $team2->getId()) {
                throw new InvalidArgumentException('Team 1 and Team 2 cannot be the same team');
            }

            if ($team1->getTournament()->getId() !== $tournament->getId() || 
                $team2->getTournament()->getId() !== $tournament->getId()) {
                throw new InvalidArgumentException('Teams must belong to the same tournament');
            }

            $this->tournament = $tournament;
            $this->team1 = $team1;
            $this->team2 = $team2;
        } catch (\Error $e) {
            throw new InvalidArgumentException('Invalid team or tournament data: ' . $e->getMessage());
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTournament(): Tournament
    {
        return $this->tournament;
    }

    public function getTeam1(): Team
    {
        return $this->team1;
    }

    public function getTeam2(): Team
    {
        return $this->team2;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        try {
            $validStatuses = ['upcoming', 'in_progress', 'completed'];
            if (!in_array($status, $validStatuses)) {
                throw new InvalidArgumentException('Invalid game status. Must be one of: ' . implode(', ', $validStatuses));
            }
            $this->status = $status;
            return $this;
        } catch (\TypeError $e) {
            throw new InvalidArgumentException('Status must be a string');
        }
    }

    public function getTeam1Score(): ?int
    {
        return $this->team1Score;
    }

    public function setTeam1Score(?int $team1Score): self
    {
        try {
            if ($team1Score !== null && $team1Score < 0) {
                throw new InvalidArgumentException('Score cannot be negative');
            }
            $this->team1Score = $team1Score;
            return $this;
        } catch (\TypeError $e) {
            throw new InvalidArgumentException('Score must be an integer or null');
        }
    }

    public function getTeam2Score(): ?int
    {
        return $this->team2Score;
    }

    public function setTeam2Score(?int $team2Score): self
    {
        try {
            if ($team2Score !== null && $team2Score < 0) {
                throw new InvalidArgumentException('Score cannot be negative');
            }
            $this->team2Score = $team2Score;
            return $this;
        } catch (\TypeError $e) {
            throw new InvalidArgumentException('Score must be an integer or null');
        }
    }

    public function getWinner(): ?Team
    {
        return $this->winner;
    }

    public function setWinner(?Team $winner): self
    {
        try {
            if ($winner !== null) {
                if ($winner->getId() !== $this->team1->getId() && $winner->getId() !== $this->team2->getId()) {
                    throw new InvalidArgumentException('Winner must be one of the teams playing in this game');
                }

                // Validate scores are set and match the winner
                if ($this->team1Score !== null && $this->team2Score !== null) {
                    $actualWinner = $this->team1Score > $this->team2Score ? $this->team1 : $this->team2;
                    if ($winner->getId() !== $actualWinner->getId()) {
                        throw new InvalidArgumentException('Winner does not match the game scores');
                    }
                }
            }
            
            $this->winner = $winner;
            return $this;
        } catch (\Error $e) {
            throw new InvalidArgumentException('Invalid winner data: ' . $e->getMessage());
        }
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isUpcoming(): bool
    {
        return $this->status === 'upcoming';
    }

    public function getRound(): int
    {
        return $this->round;
    }

    public function setRound(int $round): self
    {
        $this->round = $round;
        return $this;
    }
}