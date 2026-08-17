<?php
/**
 * Example usage of SlotsAPI class
 * 
 * This file demonstrates how to:
 * 1. Initialize the API client
 * 2. Fetch game list
 * 3. Display games
 * 4. Launch a game
 */

// 1. Include the class
require_once('slots_api.class.php');

// 2. Create an instance
$api = new SlotsAPI('YOUR_PARTNER_ID', 'YOUR_API_KEY');

// 3. Get games list
$games = $api->getGames('pragmatic');

// 4. Display game list
echo '<h2>Game List</h2>';
echo '<div style="display:flex; flex-wrap:wrap; gap:15px;">';

foreach ($games as $game) {
    echo '<div style="width:150px; text-align:center; border:1px solid #ddd; border-radius:8px; padding:10px;">';
    echo '<a href="/play.php?symbol=' . $game['symbol'] . '">';
    echo '<img src="' . $game['imageurl'] . '" alt="' . $game['name'] . '" style="width:100%; height:auto; border-radius:4px;">';
    echo '<p style="margin:5px 0 0 0; font-size:12px;">' . $game['name'] . '</p>';
    echo '</a>';
    echo '</div>';
}

echo '</div>';

// 5. Launch a game (example)
$result = $api->launchGame(
    'vs20wildparty',  // Game symbol
    'user_123',       // Player ID in your system
    'pragmatic',      // Provider
    'en',             // Language
    'demo'            // real or demo
);

if ($result['success']) {
    // Redirect to the game page
    header('Location: ' . $result['link']);
    exit;
} else {
    echo 'Error: ' . $result['error'];
}