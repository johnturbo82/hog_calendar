<?php

require_once 'config.php';

class CalendarAPI 
{
    /**
     * Generate service Account Access Token
     */
    public static function get_service_account_token() {        
        // JWT Header
        $header = json_encode(['typ' => 'JWT', 'alg' => 'RS256']);
        
        // JWT Payload
        $now = time();
        $payload = json_encode([
            'iss' => GOOGLE_API_CLIENT_EMAIL,
            'scope' => 'https://www.googleapis.com/auth/calendar',
            'aud' => 'https://oauth2.googleapis.com/token',
            'exp' => $now + 3600,
            'iat' => $now
        ]);
        
        // Base64 encode
        $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        
        // Create signature
        $signature = '';
        $signingInput = $base64Header . '.' . $base64Payload;
        
        openssl_sign($signingInput, $signature, GOOGLE_API_PRIVATE_KEY, 'SHA256');
        $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        
        // JWT
        $jwt = $signingInput . '.' . $base64Signature;
        
        // Exchange JWT for access token
        $postData = http_build_query([
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt
        ]);
        
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencoded',
                'content' => $postData
            ]
        ]);
        
        $response = file_get_contents('https://oauth2.googleapis.com/token', false, $context);
        $data = json_decode($response, true);
        
        return $data['access_token'] ?? null;
    }

    /**
     * Create Calendar Event
     */
    public static function create_event($event_data) {
        $accessToken = self::get_service_account_token();
        
        if (!$accessToken) {
            return ['success' => false, 'error' => 'Could not obtain access token'];
        }

        $json_data = json_encode($event_data);
        
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $accessToken,
                ],
                'content' => $json_data,
                'ignore_errors' => true
            ]
        ]);

        $url = "https://www.googleapis.com/calendar/v3/calendars/" . CALENDAR_ID . "/events";
        
        $result = file_get_contents($url, false, $context);
        
        if ($result === false) {
            return ['success' => false, 'error' => 'Error occurred during API call'];
        }
        
        $response = json_decode($result, true);
        
        if (isset($response['error'])) {
            return [
                'success' => false, 
                'error' => $response['error']['message'],
                'code' => $response['error']['code']
            ];
        }
        
        if (isset($response['id'])) {
            return [
                'success' => true,
                'event_id' => $response['id'],
            ];
        }
        
        return ['success' => false, 'error' => 'Unexpected API response'];
    }
}
?>