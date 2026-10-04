<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Initial Super Admin
    |--------------------------------------------------------------------------
    |
    | Created by `php artisan db:seed`. Outside production the password falls
    | back to "password"; in production ADMIN_PASSWORD must be set.
    |
    */

    'admin' => [
        'name' => env('ADMIN_NAME', 'Admin User'),
        'email' => env('ADMIN_EMAIL', 'admin@propnest.test'),
        'password' => env('ADMIN_PASSWORD'),
    ],

];
