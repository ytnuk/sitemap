<?php

namespace WebEdit\Sitemap;

use WebEdit\Front;
use WebEdit\Sitemap;

final class Presenter extends Front\Presenter {

    /**
     * @var Sitemap\Control\Factory
     * @inject
     */
    public $controlFactory;

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
    }

    public function renderView() {
        $this['menu']['breadcrumb'][] = $this->sitemap->menu;
    }

    protected function createComponentSitemap() {
        return $this->controlFactory->create($this->sitemap);
    }

}
