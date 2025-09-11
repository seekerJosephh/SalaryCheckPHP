<?php
// includes/encryption.php

// Encryption setup
function encryptData($data, $key, $method = 'aes-256-cbc') {
    // Generate random IV (16 bytes for AES-256-CBC)
    $iv = random_bytes(16); // Requires PHP 7+
    
    // Encrypt the data
    $encrypted = openssl_encrypt($data, $method, $key, 0, $iv);
    if ($encrypted === false) {
        throw new Exception('Encryption failed');
    }
    
    // Combine IV + encrypted data, base64-encode for URL safety
    return base64_encode($iv . $encrypted);
}

// Decryption function
function decryptData($encrypted_data, $key, $method = 'aes-256-cbc') {
    // Base64-decode the data
    $enc_data = base64_decode($encrypted_data);
    if ($enc_data === false) {
        throw new Exception('Invalid encoding');
    }
    
    // Extract IV (first 16 bytes) and ciphertext
    $iv = substr($enc_data, 0, 16);
    $ciphertext = substr($enc_data, 16);
    
    // Decrypt
    $decrypted = openssl_decrypt($ciphertext, $method, $key, 0, $iv);
    if ($decrypted === false) {
        throw new Exception('Decryption failed');
    }
    
    return $decrypted;
}