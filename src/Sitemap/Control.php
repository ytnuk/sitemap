<?php

namespace WebEdit\Sitemap;

use WebEdit\Entity;
use WebEdit\Menu;

final class Control extends Entity\Control {

    private $menuRepository;
    private $groupRepository;

    public function __construct(Menu\Repository $menuRepository, Menu\Group\Repository $groupRepository, Form\Control\Factory $form) {
        $this->menuRepository = $menuRepository;
        $this->groupRepository = $groupRepository;
        $this->form = $form;
    }

    public function render() {
        $template = $this->template;
        if ($this->entity) {
            $template->menu = $this->entity->menu->menu;
        } else {
            $group = $this->groupRepository->getGroupByKey('front');
            $template->menu = $group->menu;
        }
        $template->render($this->getTemplateFiles('list'));
    }

    public function renderXml() {
        $template = $this->template;
        $group = $this->groupRepository->getGroupByKey('front');
        $template->menu = $this->menuRepository->getChildren($group->menu);
        $template->render($this->getTemplateFiles('xml'));
    }

}
