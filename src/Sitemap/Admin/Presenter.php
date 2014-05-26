<?php

namespace WebEdit\Sitemap\Admin;

use WebEdit;
use WebEdit\Sitemap;

final class Presenter extends WebEdit\Admin\Presenter {

    private $sitemap;

    /**
     * @inject
     * @var Sitemap\Repository
     */
    public $repository;

    /**
     * @inject
     * @var Sitemap\Control\Factory
     */
    public $control;

    public function renderAdd() {
        $this['menu']['breadcrumb'][] = 'sitemap.admin.add';
    }

    public function actionEdit($id) {
        $this->sitemap = $this->repository->getSitemap($id);
        if (!$this->sitemap) {
            $this->error();
        }
        $this['sitemap']->setEntity($this->sitemap);
    }

    public function renderEdit() {
        $this['menu']['breadcrumb'][] = 'sitemap.admin.edit';
    }

    protected function createComponentSitemap() {
        return $this->control->create();
    }

}
