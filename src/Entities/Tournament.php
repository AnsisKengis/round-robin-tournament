<?php

namespace App\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

#[ORM\Entity]
#[ORM\Table(name: 'tournaments')]
class Tournament
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(length: 20, options: ["check" => "status IN ('upcoming', 'in_progress', 'completed')"])]
    private string $status = 'upcoming';

    #[ORM\OneToMany(mappedBy: 'tournament', targetEntity: Team::class, cascade: ['persist'])]
    private Collection $teams;

    #[ORM\OneToMany(mappedBy: 'tournament', targetEntity: Game::class, cascade: ['persist'])]
    private Collection $games;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->teams = new ArrayCollection();
        $this->games = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $validStatuses = ['upcoming', 'in_progress', 'completed'];
        if (!in_array($status, $validStatuses)) {
            throw new InvalidArgumentException('Invalid tournament status. Must be one of: ' . implode(', ', $validStatuses));
        }
        $this->status = $status;
        return $this;
    }

    /**
     * @return Collection<int, Team>
     */
    public function getTeams(): Collection
    {
        return $this->teams;
    }

    /**
     * @return Collection<int, Game>
     */
    public function getGames(): Collection
    {
        return $this->games;
    }
}