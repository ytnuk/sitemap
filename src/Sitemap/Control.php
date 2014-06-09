<?php

namespace WebEdit\Sitemap;

use WebEdit\Entity;
use WebEdit\Menu;

final class Control extends Entity\Control {

    private $menuRepository;
    private $groupRepository;

    public function __construct(Menu\Repository $menuRepository, Menu\Group\Repository $groupRepository, Form\Control\Factory $formControl) {
        $this->menuRepository = $menuRepository;
        $this->groupRepository = $groupRepository;
        $this->formControl = $formControl;
    }

    public function render($type = 'list') {
        if ($this->entity) {
            $this->template->menu = $this->entity->menu->menu;
        } else {
            $group = $this->groupRepository->getGroupByKey('front');
            $this->template->menu = $group->menu;
        }
        parent::render($type);
    }

    public function renderXml() {
        $this->render('xml');
    }

}
