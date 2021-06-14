<?php 
function generateToken($user){
    $header                 = json_encode(['type' => 'JWT', 'algorithm' => 'HS256']);
    $payload                = json_encode(['user' => $user,"timestamp"=>now()->format('dmyhis')]);
    $base64UrlHeader        = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
    $base64UrlPayload       = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
    $signature              = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, 'abC123!', true);
    $base64UrlSignature     = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
    $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    return $jwt;
}