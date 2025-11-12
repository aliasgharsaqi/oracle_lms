<?php

return [
    /*
    |--------------------------------------------------------------------------
    | School Information
    |--------------------------------------------------------------------------
    |
    | These details are used throughout the application, especially in
    | generated documents like result cards and certificates.
    |
    */

    'name' => env('SCHOOL_NAME', 'Oracles'),

    'address' => env('SCHOOL_ADDRESS', '123 Education Lane, Lahore, Punjab, Pakistan'),
    
    'logo_path' => public_path('images/school_logo.png'), // Path to your logo file
    
    'report_title' => 'ACADEMIC RESULT CARD',

];