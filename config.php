<?php

// load GCS library
require_once 'vendor/autoload.php';

use Google\Cloud\Storage\StorageClient;

// Please use your own private key (JSON file content) which was downloaded in step 3 and copy it here
// your private key JSON structure should be similar like dummy value below.
// WARNING: this is only for QUICK TESTING to verify whether private key is valid (working) or not.  
// NOTE: to create private key JSON file: https://console.cloud.google.com/apis/credentials  
$privateKeyFileContent = '{
    "type": "service_account",
    "project_id": "crefto3",
    "private_key_id": "b1de58748b10c01d1dc8114f3cdeefb4ae26c514",
    "private_key": "-----BEGIN PRIVATE KEY-----\nMIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQCoxBeQLFMbppJJ\ns6xzgomnT1cKg4/FQ2vtCNUIAtcCFFTgcHoZEJ8NLokXrL6Hqap4G/GsdzGP4sRy\nBHpU0N6SiZbuWPXSLv3L9qypLuFEeLim3AgNRI+Ub6+QEn38jJx4mv0c6GqLTMAf\nNtvdQna4vTrwx+6Cxn7nl5PZh3v8l6mJ1uXTjh6Lul8Yt1So+Xoizy1XcadJ6cvq\nZ/N7q4gUAMB6/OTPkwmUFBZ7r+8BNioStL5SYsos/uDR20yJqydZeJDoda+K3uB8\nbMR8SWYCdjcoEJFJrG9MsBMlbR8Zm4DKlSOJAbl73zVizyI+BZE8oaBdIOoY0o9J\nuaxoFCIFAgMBAAECggEAD1PqAPPpo24ERNHfBImzV2EHGoMa7HNkar2d3ZGmdf+j\nOtQ0XrDqBTA4DtCnaFpiS3DcSQn1VChoJ6Pc+XWT9XKuJN0rDGksr1jq0x3ZI1ck\njDEZQR08PiLLqvXm/+hEcNbAzN6WCKtSmrMokmauMjarkwFRmgqNrZqQeNYLjoQQ\n3SWNnDc6K1GuSzdVAfXKx3GJipxXtnGmQYtTIv+2cxM0ZcreIIg4zD+v9Fs/16VK\ngjO2J3VtiOF4/mSZupqDzYf8PsFLOX6Lzuj8H6xFvDca3yaqwFdAu+Kx0cIeITQ2\n8x2F1pH7Y0gOwql5OpcoEUt/Z+/DiqTAnFH97y0g8QKBgQDj16v2BrQO7B040C8j\nUg4rf0TUUbzOW/Nv3hnjyAWjaON2gIrkgeqeBRfeceAX3yuI604y3MmOuFTzqibR\nUafBOgvaKu5q5LmoyAQq0zwaMnFGd+fmG8+tJhRe9sibV9GLZVFNJ/rdRUOUO5ET\nYG5xuSf906kh3j4Q9e8tWQzIMQKBgQC9n2Xb0l0rAegQ91CgVULc9L/MOeRbvVt/\nS9ug1Q/LUuK7xPPIvXdVLzR3QnTmcBuXA80bFJaep0CzHAunvCtWNRw7ceJ9suAJ\nEvvA/vo/Q+csnC32hQ1SsquANTnIM+B3l0kbptPJ9JdvMF8rPOyxf7AC1jtHvluJ\n54L2JKuWFQKBgAxjDlNK3AEvrwsGrnliHakZuzk71GL3ts1vKsMqfbv7mNo5dNOl\niIbcygZq9H73wBsqh87WGBMtTFkO+BtLMC6eJETRLrMbCkj1ztwxLcRS17u4CyCE\nhI7qUhMzoYZoiNjmQjxKnyXmfR0S+/kstfRy14zCNCDGP2OWq1Ew1TQRAoGAK874\ntGmMdtQw7kKFERXBpdSxvxgc5wj/a5B2BFlVFc9nbKQbAmSrfjWytF7ZLSf3Z9NL\n/paqGatgakDvfGgfxwHsLNupzQqXceE94p/F5vnkHc1TXSHuKw32S1+Aov1BPb/o\n9wd65Kyqk9ikFBQ2RufOHUmrVm7nLRwciIt+TOUCgYBljGtZ92b6y6mkArw6oDdy\ncSES2pGw2ZbFXzs98luCvWGVwh4yXlxRd2OVVmTqHKSod5VuLLBoyv1lCKhOcWqb\nvGCpmF3vIlgUCPq0C41hRXfD0enLFWFnjAc+2i5XwB8mKCe/Agd3qVPOTyKYQfwj\nc7lDY+TyOQfg+BAxqEApLA==\n-----END PRIVATE KEY-----\n",
    "client_email": "indraco-pp@crefto3.iam.gserviceaccount.com",
    "client_id": "113999189047165205531",
    "auth_uri": "https://accounts.google.com/o/oauth2/auth",
    "token_uri": "https://oauth2.googleapis.com/token",
    "auth_provider_x509_cert_url": "https://www.googleapis.com/oauth2/v1/certs",
    "client_x509_cert_url": "https://www.googleapis.com/robot/v1/metadata/x509/indraco-pp%40crefto3.iam.gserviceaccount.com"
  }';

/*
 * NOTE: if the server is a shared hosting by third party company then private key should not be stored as a file,
 * may be better to encrypt the private key value then store the 'encrypted private key' value as string in database,
 * so every time before use the private key we can get a user-input (from UI) to get password to decrypt it.
 */

function uploadFile($bucketName, $fileContent, $cloudPath) {
    $privateKeyFileContent = $GLOBALS['privateKeyFileContent'];
    // connect to Google Cloud Storage using private key as authentication
    try {
        $storage = new StorageClient([
            'keyFile' => json_decode($privateKeyFileContent, true)
        ]);
    } catch (Exception $e) {
        // maybe invalid private key ?
        print $e;
        return false;
    }

    // set which bucket to work in
    $bucket = $storage->bucket($bucketName);

    // upload/replace file 
    $storageObject = $bucket->upload(
            $fileContent,
            ['name' => $cloudPath]
            // if $cloudPath is existed then will be overwrite without confirmation
            // NOTE: 
            // a. do not put prefix '/', '/' is a separate folder name  !!
            // b. private key MUST have 'storage.objects.delete' permission if want to replace file !
    );

    // is it succeed ?
    return $storageObject != null;
}

function getFileInfo($bucketName, $cloudPath) {
    $privateKeyFileContent = $GLOBALS['privateKeyFileContent'];
    // connect to Google Cloud Storage using private key as authentication
    try {
        $storage = new StorageClient([
            'keyFile' => json_decode($privateKeyFileContent, true)
        ]);
    } catch (Exception $e) {
        // maybe invalid private key ?
        print $e;
        return false;
    }

    // set which bucket to work in
    $bucket = $storage->bucket($bucketName);
    $object = $bucket->object($cloudPath);
    return $object->info();
}
//this (listFiles) method not used in this example but you may use according to your need 
function listFiles($bucket, $directory = null) {

    if ($directory == null) {
        // list all files
        $objects = $bucket->objects();
    } else {
        // list all files within a directory (sub-directory)
        $options = array('prefix' => $directory);
        $objects = $bucket->objects($options);
    }

    foreach ($objects as $object) {
        print $object->name() . PHP_EOL;
        // NOTE: if $object->name() ends with '/' then it is a 'folder'
    }
}