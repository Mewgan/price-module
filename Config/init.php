<?php

return [

    'app' => [
        'blocks' => [
            'PriceModule' => [
                'path' => 'src/Modules/Price/',
                'namespace' => '\\Jet\\Modules\\Price',
                'view_dir' => 'src/Modules/Price/Views/',
                'prefix' => 'admin',
            ],
        ],
        'fixtures' => [
            'src/Modules/Price/Fixtures/'
        ]
    ]
];