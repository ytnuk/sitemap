<?php

namespace WebEdit\Sitemap\Xml;

use WebEdit\Front;
use WebEdit\Sitemap;

final class Presenter extends Front\Presenter {

    /**
     * @inject
     * @var Sitemap\Control\Factory
     */
    public $controlFactory;

    protected function createComponentSitemap() {
        return $this->controlFactory->create();
    }

}
