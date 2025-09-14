<?php

function encryptData($data, $key, $method = 'aes-256-cbc') {
    // Generate random IV 
    $iv = random_bytes(16); 
    
    // Encrypt the data
    $encrypted = openssl_encrypt($data, $method, $key, 0, $iv);
    if ($encrypted === false) {
        throw new Exception('Encryption failed');
    }
    

    return base64_encode($iv . $encrypted);
}

// Decryption function
function decryptData($encrypted_data, $key, $method = 'aes-256-cbc') {

    $enc_data = base64_decode($encrypted_data);
    if ($enc_data === false) {
        throw new Exception('Invalid encoding');
    }
    
    $iv = substr($enc_data, 0, 16);
    $ciphertext = substr($enc_data, 16);
    
    // Decrypt
    $decrypted = openssl_decrypt($ciphertext, $method, $key, 0, $iv);
    if ($decrypted === false) {
        throw new Exception('Decryption failed');
    }
    
    return $decrypted;
}