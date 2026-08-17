<?php
/**
 * play.php - Game launcher page
 * 
 * This file handles game launch requests and displays the game in an iframe.
 */

// 1. Include the class
require_once('slots_api.class.php');

// 2. Get parameters
$symbol = isset($_GET['symbol']) ? $_GET['symbol'] : '';
$type = isset($_GET['type']) ? $_GET['type'] : 'demo';

if (empty($symbol)) {
    die('Game symbol is required');
}

// 3. Create API instance
$api = new SlotsAPI('YOUR_PARTNER_ID', 'YOUR_API_KEY');

// 4. Get player ID from session (example)
$player_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'guest_123';

// 5. Launch the game
$result = $api->launchGame(
    $symbol,
    $player_id,
    'pragmatic',
    'en',
    $type
);

if (!$result['success']) {
    die('Error launching game: ' . $result['error']);
}

// 6. Display the game in iframe
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loading game...</title>
    <style>
        * { margin: 0; padding: 0; }
        html, body {
            width: 100%;
            height: 100%;
            overflow: hidden;
            background: #1a1a2e;
        }
        .game-container {
            width: 100%;
            height: 100%;
        }
        .game-container iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
        .back-btn {
            position: fixed;
            top: 10px;
            left: 10px;
            z-index: 1000;
            background: rgba(255,255,255,0.1);
            color: #fff;
            padding: 8px 16px;
            border-radius: 20px;
            text-decoration: none;
            font-family: Arial, sans-serif;
            font-size: 14px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
            transition: all 0.3s;
        }
        .back-btn:hover {
            background: rgba(255,255,255,0.2);
        }
        <?php if ($type == 'demo'): ?>
        .demo-badge {
            position: fixed;
            top: 10px;
            right: 10px;
            z-index: 1000;
            background: rgba(255,204,0,0.9);
            color: #000;
            padding: 6px 14px;
            border-radius: 20px;
            font-family: Arial, sans-serif;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid rgba(255,204,0,0.3);
        }
        <?php endif; ?>
    </style>
</head>
<body>
    <a href="/" class="back-btn">← Back</a>
    <?php if ($type == 'demo'): ?>
    <span class="demo-badge">🎮 DEMO</span>
    <?php endif; ?>
    <div class="game-container">
        <iframe src="<?php echo htmlspecialchars($result['link']); ?>" 
                allow="autoplay; fullscreen" 
                loading="lazy">
        </iframe>
    </div>
</body>
</html>