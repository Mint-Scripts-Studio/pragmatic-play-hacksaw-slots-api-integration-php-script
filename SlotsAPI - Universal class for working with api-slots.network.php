<?php
/**
 * SlotsAPI - Universal class for working with api-slots.network
 * 
 * This class provides methods to interact with the slots API,
 * including fetching game lists and launching games.
 */
class SlotsAPI {
    private $api_url = '';
    private $partner;
    private $api_key;
    private $currency = 'USD';
    
    /**
     * Constructor
     * 
     * @param string $partner - Your partner ID
     * @param string $api_key - Your API key
     * @param string $currency - Currency (USD, EUR, etc.)
     */
    public function __construct($partner, $api_key, $currency = 'USD') {
        $this->partner = $partner;
        $this->api_key = $api_key;
        $this->currency = $currency;
    }
    
    /**
     * Get list of all games
     * 
     * @param string $provider - Provider name (pragmatic, hacksaw, etc.)
     * @return array - Array of games
     */
    public function getGames($provider = 'pragmatic') {
        $url = $this->api_url . '/api/allgamelist';
        $data = array(
            'partner' => $this->partner,
            'api_key' => $this->api_key,
            'provider' => $provider
        );
        
        $result = $this->request($url, $data, 'GET');
        
        if (isset($result['slots']) && is_array($result['slots'])) {
            return $result['slots'];
        }
        
        return array();
    }
    
    /**
     * Launch a game
     * 
     * @param string $symbol - Game symbol (from getGames)
     * @param string $player_id - Player ID in your system
     * @param string $provider - Game provider
     * @param string $lang - Language (en, ru, es, etc.)
     * @param string $gametype - real or demo
     * @return array - Result with game link
     */
    public function launchGame($symbol, $player_id, $provider, $lang = 'en', $gametype = 'real') {
        $data = array(
            'symbol' => $symbol,
            'provider' => $provider,
            'currency' => $this->currency,
            'partner' => $this->partner,
            'api_key' => $this->api_key,
            'player_id' => (string)$player_id,
            'lang' => $lang,
            'gametype' => $gametype
        );
        
        $result = $this->request('/api/playGame.do', $data, 'POST');
        
        if (isset($result['status']) && $result['status'] == 'ok') {
            return array(
                'success' => true,
                'link' => isset($result['link']) ? $result['link'] : '',
                'session_id' => isset($result['meta']['session_id']) ? $result['meta']['session_id'] : null,
                'balance' => isset($result['meta']['balance']) ? $result['meta']['balance'] : null
            );
        }
        
        return array(
            'success' => false,
            'error' => isset($result['message']) ? $result['message'] : 'Unknown error'
        );
    }
    
    /**
     * Internal method for API requests
     * 
     * @param string $endpoint - API endpoint
     * @param array $data - Request data
     * @param string $method - HTTP method (GET or POST)
     * @return array - API response
     */
    private function request($endpoint, $data = array(), $method = 'POST') {
        $url = $this->api_url . $endpoint;
        
        if ($method == 'GET' && !empty($data)) {
            $url .= '?' . http_build_query($data);
        }
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Accept: application/json'
            ));
        }
        
        $response = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($error) {
            return array('status' => 'error', 'message' => 'CURL Error: ' . $error);
        }
        
        if ($httpCode != 200) {
            return array('status' => 'error', 'message' => 'HTTP Error: ' . $httpCode);
        }
        
        $result = json_decode($response, true);
        if (!$result) {
            return array('status' => 'error', 'message' => 'Invalid JSON response');
        }
        
        return $result;
    }
}