<?php

return [
    /**
     * The application endpoint for BanHammer to connect to in order
     * to download ban listings.
     */

    'endpoint' => env('BH_ENDPOINT', 'https://bh-api.kikfinder.com'),
    /**
     * The shared secret used to authenticate with BanHammer
     */

    'secret'   => env('BH_SHARED_SECRET', ''),
];
