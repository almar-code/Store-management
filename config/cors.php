<?php

return [

    'paths' => [
        'api/*',
        'storage/*',
        'sanctum/csrf-cookie',
        '*'
    ],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'], // أو يمكنك وضع الرابط المحلي للمتصفح مثل ['http://localhost:52450'] حسب منفذ فلاتر ويب لديك

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true, // ضعها true لضمان قبول الكوكيز والجلسات والتوكنز عبر المتصفح
];