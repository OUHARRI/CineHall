<?php

class Login
{
    /**
     * @throws JsonException
     */
    public static function JWT($admin = false): void
    {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
        header("Access-Control-Allow-Headers:  Content-Type, Authorization, X-Auth-Token, Origin ");
        header("Access-Control-Allow-Methods: *");
        http_response_code(200);

        // On vérifie si on reçoit un token
        if (isset($_SERVER['Authorization'])) {
            $token = trim($_SERVER['Authorization']);
        } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $token = trim($_SERVER['HTTP_AUTHORIZATION']);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            if (isset($requestHeaders['Authorization'])) {
                $token = trim($requestHeaders['Authorization']);
            }
        }

        // On vérifie si la chaine commence par "Bearer "
        if (!isset($token) || !preg_match('/Bearer\s(\S+)/', $token, $matches)) {
            http_response_code(200);
            echo json_encode([
                'success' => false,
                'message' => 'Token introuvable'
            ], JSON_THROW_ON_ERROR);
            exit;
        }

        // On extrait le token
        $token = str_replace('Bearer ', '', $token);

        $jwt = new JWT();

        // On vérifie la validité
        if (!$jwt->isValid($token)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Token invalide'
            ], JSON_THROW_ON_ERROR);
            exit;
        }

        // On vérifie la signature
        if (!$jwt->check($token, SECRET)) {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'Le token est invalide'
            ], JSON_THROW_ON_ERROR);
            exit;
        }

        // On vérifie l'expiration
        if ($jwt->isExpired($token)) {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'Le token a expiré'
            ], JSON_THROW_ON_ERROR);
            exit;
        }

        if ($admin) {
            $flag = true;
            $payload = $jwt->getPayload($token);
            foreach ($payload['roles'] as $role) {
                if ($role === 'ROLE_ADMIN') {
                    $flag = false;
                }
            }
            if ($flag) {
                http_response_code(403);
                echo json_encode([
                    'success' => false,
                    'message' => "vous n'avez pas l'accès a cette page !"
                ], JSON_THROW_ON_ERROR);
                exit;
            }
        }
    }
}