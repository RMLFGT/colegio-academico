<?php

return [
    'presentation_data' => (bool) env('PRESENTATION_DATA', false),
    'presentation_password' => env('PRESENTATION_PASSWORD'),
    'admin_username' => env('ADMIN_USERNAME', 'admin'),
    'admin_email' => env('ADMIN_EMAIL', 'admin@colegio.test'),
    'admin_password' => env('ADMIN_PASSWORD'),
    'timezone' => env('SCHOOL_TIMEZONE', 'America/Guatemala'),
    'min_score' => (float) env('GRADE_MIN', 0),
    'max_score' => (float) env('GRADE_MAX', 100),
];
