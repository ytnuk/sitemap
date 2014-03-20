<?php

namespace WebEdit\Sitemap;

use WebEdit\Front;

final class Presenter extends Front\Presenter {

    /**
     * @var \WebEdit\Sitemap\Control\Factory
     * @inject
     */
    public $controlFactory;

    /**
     * @var \WebEdit\Sitemap\Repository
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
