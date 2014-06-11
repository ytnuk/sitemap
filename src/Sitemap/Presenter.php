<?php

namespace WebEdit\Sitemap;

use WebEdit\Front;
use WebEdit\Sitemap;

final class Presenter extends Front\Presenter {

    private $control;
    private $repository;
    private $sitemap;

    public function __construct(Sitemap\Repository $repository, Sitemap\Control\Factory $control) {
        $this->repository = $repository;
        $this->control = $control;
    }

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
