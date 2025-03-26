<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tournament Results</title>
    <link rel="stylesheet" href="/main.css">
</head>
<body>
    <div class="container">
        <h1>Tournament Results</h1>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="message"><?php echo htmlspecialchars($_SESSION['message']); ?></div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="error"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="tournament-info">
            <h2><?php echo htmlspecialchars($tournament->getName()); ?></h2>
            <p>Status: <?php echo htmlspecialchars($tournament->getStatus()); ?></p>
        </div>

        <div class="games-section">
            <?php
            $gamesByRound = [];
            foreach ($games as $game) {
                $round = $game->getRound();
                if (!isset($gamesByRound[$round])) {
                    $gamesByRound[$round] = [];
                }
                $gamesByRound[$round][] = $game;
            }
            ksort($gamesByRound);
            ?>

            <?php foreach ($gamesByRound as $round => $roundGames): ?>
                <div class="round-section">
                    <h2>Round <?php echo $round; ?></h2>
                    <div class="games-grid">
                        <?php foreach ($roundGames as $game): ?>
                            <div class="game-box">
                                <div class="team-row <?php echo $game->getWinner() === $game->getTeam1() ? 'winner' : ''; ?>">
                                    <?php echo htmlspecialchars($game->getTeam1()->getName()); ?>
                                    <span class="score"><?php echo $game->getTeam1Score(); ?></span>
                                </div>
                                <div class="team-row <?php echo $game->getWinner() === $game->getTeam2() ? 'winner' : ''; ?>">
                                    <?php echo htmlspecialchars($game->getTeam2()->getName()); ?>
                                    <span class="score"><?php echo $game->getTeam2Score(); ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="teams-section">
            <h2>Team Standings</h2>
            <table class="teams-table">
                <thead>
                    <tr>
                        <th>Team Name</th>
                        <th>Wins</th>
                        <th>Losses</th>
                        <th>Win Rate</th>
                    </tr>
                </thead>
                <tbody>
            <?php 
            // Convert Collection to array and sort by wins
            $teamsArray = $teams->toArray();
            usort($teamsArray, function($a, $b) {
                // Sort by wins (descending)
                if ($b->getWins() !== $a->getWins()) {
                    return $b->getWins() - $a->getWins();
                }
                // If wins are equal, sort by win rate (descending)
                $aRate = $a->getWins() / ($a->getWins() + $a->getLosses());
                $bRate = $b->getWins() / ($b->getWins() + $b->getLosses());
                return $bRate <=> $aRate;
            });

            foreach ($teamsArray as $index => $team): 
                $position = $index + 1;
                $positionClass = '';
                if ($position === 1) $positionClass = 'gold';
                if ($position === 2) $positionClass = 'silver';
                if ($position === 3) $positionClass = 'bronze';
            ?>
                <tr class="<?php echo $positionClass; ?>">
                    <td><?php echo htmlspecialchars($team->getName()); ?></td>
                    <td><?php echo $team->getWins(); ?></td>
                    <td><?php echo $team->getLosses(); ?></td>
                    <td>
                        <?php 
                        $totalGames = $team->getWins() + $team->getLosses();
                        echo $totalGames > 0 ? round(($team->getWins() / $totalGames) * 100, 1) . '%' : '0%';
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
            </table>
        </div>

        <div class="new-tournamet">
            <a href="/" class="new-tournamet-button">Create New Tournament</a>
        </div>
    </div>
</body>
</html> 