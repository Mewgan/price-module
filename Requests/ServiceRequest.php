<?php

namespace Jet\Modules\Price\Requests;

use JetFire\Framework\System\Request;

/**
 * Class ServiceRequest
 * @package Jet\Modules\Team\Requests
 */
class ServiceRequest extends Request
{

    /**
     * @var array
     */
    public static $messages = [
        'required' => 'Le champ ":field" doit être rempli',
    ];


    /**
     * @return array
     */
    public function rules()
    {
        return [
            'title|price|position' => 'required',
            'category.id' => 'required',
            'description' => 'optional',
        ];
    }

}