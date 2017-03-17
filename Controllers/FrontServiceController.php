<?php

namespace Jet\Modules\Price\Controllers;

use Jet\FrontBlock\Controllers\MainController;

class FrontServiceController extends MainController
{

    /**
     * @param $content
     * @return null
     */
    public function show($content){
        $data = $content->getData();
        if(!empty($data)) {
            return (empty($data['content']))
                ? null
                : $data['content'];
        }
        return null;
    }
    
}