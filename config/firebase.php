<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Firebase Project
    |--------------------------------------------------------------------------
    | project_id  — from Firebase Console → Project Settings → General
    | credentials — path to the service account JSON downloaded from
    |               Firebase Console → Project Settings → Service Accounts
    |               → Generate new private key
    |
    | server_key  — legacy FCM server key (only needed for older projects
    |               still using the legacy HTTP API, not V1)
    */

    'project_id' => env('FIREBASE_PROJECT_ID'),

    'credentials' => [
        'file' => env('FIREBASE_CREDENTIALS', base_path('firebase-service-account.json')),
    ],

    'server_key' => env('FIREBASE_SERVER_KEY'),

];
