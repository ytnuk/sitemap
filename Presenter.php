<?php

namespace WebEdit\Sitemap;

use WebEdit\Front;

class Presenter extends Front\Presenter {

    /**
     * @var \WebEdit\Sitemap\Control\Factory
     * @inject
     */
    public $controlFactory;

    protected function createComponentSitemap() {
        return $this->controlFactory->create();
    }

}
