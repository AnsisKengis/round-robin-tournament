<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tournament Generator</title>
    <link rel="stylesheet" href="/main.css">
</head>
<body>
    <div class="container">
        <h1>Tournament Generator</h1>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="message"><?php echo htmlspecialchars($_SESSION['message']); ?></div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="error"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form method="POST" action="/tournament/create">
            <div class="form-group">
                <label for="tournament_name">Tournament Name:</label>
                <input type="text" id="tournament_name" name="tournament_name" required>
            </div>

            <div class="form-group">
                <label for="team_count">Number of Teams:</label>
                <select id="team_count" name="team_count" required>
                    <?php for ($i = 2; $i <= 12; $i++): ?>
                        <option value="<?php echo $i; ?>">
                            <?php echo $i; ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>

            <button type="submit">Generate Tournament</button>
        </form>
    </div>
</body>
</html>