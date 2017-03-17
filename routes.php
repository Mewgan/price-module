<?php

return [
    '/module/service/*' => [
        'use' => 'AdminServiceController@{method}',
        'ajax' => true
    ],

    '/module/service-category/*' => [
        'use' => 'AdminServiceCategoryController@{method}',
        'ajax' => true
    ]
];