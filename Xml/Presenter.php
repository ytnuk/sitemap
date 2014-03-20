<?php

namespace WebEdit\Sitemap\Xml;

use WebEdit\Front;

final class Presenter extends Front\Presenter {

    /**
     * @var \WebEdit\Sitemap\Control\Factory
     * @inject
     */
    public $controlFactory;

    protected function createComponentSitemap() {
        return $this->controlFactory->create();
    }

}
