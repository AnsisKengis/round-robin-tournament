<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

#[ORM\Entity]
#[ORM\Table(name: 'teams')]
class Team
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column]
    private int $wins = 0;

    #[ORM\Column]
    private int $losses = 0;

    #[ORM\ManyToOne(inversedBy: 'teams')]
    #[ORM\JoinColumn(nullable: false)]
    private Tournament $tournament;

    public function __construct(string $name)
    {
        try {
            if (empty(trim($name))) {
                throw new InvalidArgumentException('Team name cannot be empty');
            }

            $name = trim($name);

            $this->name = $name;
        } catch (\TypeError $e) {
            throw new InvalidArgumentException('Invalid team name format');
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getWins(): int
    {
        return $this->wins;
    }

    public function incrementWins(): self
    {
        $this->wins++;
        return $this;
    }

    public function getLosses(): int
    {
        return $this->losses;
    }

    public function incrementLosses(): self
    {
        $this->losses++;
        return $this;
    }

    public function getTournament(): Tournament
    {
        return $this->tournament;
    }

    public function setTournament(Tournament $tournament): self
    {
        $this->tournament = $tournament;
        return $this;
    }
} 