<?php

namespace WebEdit\Sitemap\Xml;

use WebEdit\Application;
use WebEdit\Sitemap;

final class Presenter extends Application\Front\Presenter {

    private $control;

    public function __construct(Sitemap\Control\Factory $control) {
        $this->control = $control;
    }

    protected function createComponentSitemap() {
        return $this->control->create();
    }

}
