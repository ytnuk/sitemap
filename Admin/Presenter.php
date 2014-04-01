<?php

namespace WebEdit\Sitemap\Admin;

use WebEdit;
use WebEdit\Menu;

final class Presenter extends WebEdit\Admin\Presenter {

    protected $entity;

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
     * @var \WebEdit\Menu\Facade
     */
    public $menuFacade;

    public function actionAdd() {
        $this['form']['menu']['menu_id']->setItems($this->menuFacade->getChildren());
    }

    public function renderAdd() {
        $this['menu']['breadcrumb'][] = $this->translator->translate('sitemap.admin.add');
    }

    public function actionEdit($id) {
        $this->entity = $this->repository->getSitemap($id);
        if (!$this->entity) {
            $this->error();
        }
        $this['form']['menu']['menu_id']->setItems($this->menuFacade->getChildren($this->entity->menu));
        $this['form']['menu']->setDefaults($this->entity->menu);
    }

    public function renderEdit() {
        $this['menu']['breadcrumb'][] = $this->translator->translate('sitemap.admin.edit', NULL, ['sitemap' => $this->entity->menu->title]);
    }

    protected function createComponentForm() {
        $form = $this->formFactory->create($this->entity);
        $form['menu'] = new Menu\Form\Container;
        return $form;
    }

}
