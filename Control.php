<?php

namespace WebEdit\Sitemap;

use WebEdit;
use WebEdit\Menu;
use WebEdit\Menu\Group;

class Control extends WebEdit\Control {

    private $menuFacade;
    private $groupFacade;
    private $group;

    public function __construct(Menu\Model\Facade $menuFacade, Group\Model\Facade $groupFacade) {
        $this->menuFacade = $menuFacade;
        $this->groupFacade = $groupFacade;
        $this->group = $this->groupFacade->repository->getGroupByKey('front');
    }

    public function render() {
        $template = $this->template;
        $template->menu = $this->group->menu;
        $template->setFile(__DIR__ . '/Control/sitemap.latte');
        $template->render();
    }

    public function renderXml() {
        $template = $this->template;
        $template->menu = $this->menuFacade->repository->getChildren($this->group->menu);
        $template->setFile(__DIR__ . '/Control/xml.latte');
        $template->render();
    }

}
