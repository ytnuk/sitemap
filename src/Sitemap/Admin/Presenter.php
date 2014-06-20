<?php

namespace WebEdit\Sitemap\Admin;

use WebEdit\Application;
use WebEdit\Sitemap;

final class Presenter extends Application\Admin\Presenter {

    private $sitemap;
    private $repository;
    private $control;

    public function __construct(Sitemap\Repository $repository, Sitemap\Control\Factory $control) {
        $this->repository = $repository;
        $this->control = $control;
    }

    public function renderAdd() {
        $this['menu'][] = 'sitemap.admin.add';
    }

    public function actionEdit($id) {
        $this->sitemap = $this->repository->getSitemap($id);
        if (!$this->sitemap) {
            $this->error();
        }
        $this['sitemap']->setEntity($this->sitemap);
    }

    public function renderEdit() {
        $this['menu'][] = 'sitemap.admin.edit';
    }

    protected function createComponentSitemap() {
        return $this->control->create();
    }

}
