<?php

namespace Jet\Modules\Price\Controllers;

use Jet\FrontBlock\Controllers\MainController;
use Jet\Models\Content;
use Jet\Models\Website;
use Jet\Modules\Price\Models\Service;
use Jet\Modules\Price\Models\ServiceCategory;

class FrontServiceController extends MainController
{

    /**
     * @param Website $website
     * @param Content $content
     * @return null
     */
    public function show(Website $website, $content)
    {
        $data = $content->getData();

        if (!empty($data)) {
            if (empty($this->websites)) {
                $this->websites[] = $website;
                $this->getThemeWebsites($website);
            }
            $params = [
                'websites' => $this->websites,
                'options' => $this->getWebsiteData($website),
                'categories' => (isset($data['categories']) && is_array($data['categories'])) ? $data['categories'] : [],
                'service_in_category' => (isset($data['service_in_category']) && ((string)$data['service_in_category'] == 'true' || $data['service_in_category'] == true)) ? true : false
            ];

            $services = (isset($data['service']) && ((string)$data['service'] == 'false' || $data['service'] == false)) ? [] : Service::repo()->listAll($params);
            $service_categories = (isset($data['category']) && ((string)$data['category'] == 'false' || $data['category'] == false)) ? [] : ServiceCategory::repo()->listAll($params);
            return $this->_renderContent($content->getTemplate(), 'src/Modules/Price/Views/', compact('services', 'service_categories'));
        }
        return null;
    }

}