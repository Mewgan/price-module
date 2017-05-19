<?php

return [

    'app' => [
        'Price' => [
            'order' => 2,
            'hook' => [
                'left_sidebar' => true
            ],
            'routes' => [
                [
                    'title' => 'Tarif',
                    'name'=> 'module:price',
                ]
            ]
        ],
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