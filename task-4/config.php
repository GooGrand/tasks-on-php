<?php
require_once 'vendor/autoload.php';
require_once 'class-db.php';

define('GOOGLE_CLIENT_ID', '651349220670-0ge7mtks1kn3n9v06srtf029tltu8pir.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', 'SacWIqdH6NsYoXG85ul3qbX_');
  
$config = [
    'callback' => 'http://localhost:81/google-sheets-api/callback.php',
    'keys'     => [
                    'id' => GOOGLE_CLIENT_ID,
                    'secret' => GOOGLE_CLIENT_SECRET
                ],
    'scope'    => 'https://www.googleapis.com/auth/spreadsheets',
    'authorize_url_parameters' => [
            'approval_prompt' => 'force', // to pass only when you need to acquire a new refresh token.
            'access_type' => 'offline'
    ]
];
  
$adapter = new Hybridauth\Provider\Google( $config );