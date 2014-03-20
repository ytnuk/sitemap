<?php

namespace WebEdit\Sitemap\Admin;

use WebEdit;

final class Presenter extends WebEdit\Admin\Presenter {

    private $sitemap;

    /**
     * @inject
     * @var \WebEdit\Sitemap\Repository
     */
    public $repository;

    /**
     * @inject
     * @var \WebEdit\Sitemap\Facade
     */
    public $facade;

    /**
     * @inject
     * @var \WebEdit\Sitemap\Form\Factory
     */
    public $formFactory;

    public function actionAdd() {
        $this['form']->onSuccess[] = [$this, 'handleAdd'];
    }

    public function handleAdd($form) {
        $sitemap = $this->facade->addSitemap($form->getValues());
        $this->redirect('Presenter:edit', ['id' => $sitemap->id]);
    }

    public function renderAdd() {
        $this['menu']['breadcrumb'][] = $this->translator->translate('sitemap.admin.add');
    }

    public function actionEdit($id) {
        $this->sitemap = $this->repository->getSitemap($id);
        if (!$this->sitemap) {
            $this->error();
        }
        $this['form']['menu']->setDefaults($this->sitemap->menu);
        $this['form']->onSuccess[] = [$this, 'handleEdit'];
    }

    public function handleEdit($form) {
        if ($form->submitted->name == 'delete') {
            $this->facade->deleteSitemap($this->sitemap);
            $this->redirect('Presenter:view');
        } else {
            $this->facade->editSitemap($this->sitemap, $form->getValues());
            $this->redirect('this');
        }
    }

    public function renderEdit() {
        $this['menu']['breadcrumb'][] = $this->translator->translate('sitemap.admin.edit', NULL, ['sitemap' => $this->sitemap->menu->title]);
    }

    protected function createComponentForm() {
        return $this->formFactory->create($this->sitemap);
    }

}
