<?php

namespace WebEdit\Sitemap;

use WebEdit\Front;
use WebEdit\Sitemap;

final class Presenter extends Front\Presenter {

    /**
     * @var Sitemap\Control\Factory
     * @inject
     */
    public $control;

    /**
     * @var Sitemap\Repository
     * @inject
     */
    public $repository;
    private $sitemap;

    public function actionView($id) {
        $this->sitemap = $this->repository->getSitemap($id);
        if (!$this->sitemap) {
            $this->error();
        }
        $this['sitemap']->setEntity($this->sitemap);
    }

    public function renderView() {
        $this['menu']->setEntity($this->sitemap->menu);
    }

    protected function createComponentSitemap() {
        return $this->control->create();
    }

}
