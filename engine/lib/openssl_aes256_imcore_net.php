<?php

$key = 'abcdefghijklmnopqrstuvwxyz123456';

//echo AES_Encode('imcore.net');
//echo AES_Decode('kWyuTmUELRiREWIPpLy3ZA==');

function AES_Encode($plain_text)
{
    global $key;
    return base64_encode(openssl_encrypt($plain_text, "aes-256-cbc", $key, true, str_repeat(chr(0), 16)));
}

function AES_Decode($base64_text)
{
    global $key;
    return openssl_decrypt(base64_decode($base64_text), "aes-256-cbc", $key, true, str_repeat(chr(0), 16));
}

?>