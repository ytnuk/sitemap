<?php

namespace WebEdit\Sitemap;

use WebEdit;
use WebEdit\Menu;
use WebEdit\Menu\Group;

final class Control extends WebEdit\Control {

    private $menuFacade;
    private $groupFacade;
    private $menu;

    public function __construct($sitemap, Menu\Facade $menuFacade, Group\Facade $groupFacade) {
        $this->menuFacade = $menuFacade;
        $this->groupFacade = $groupFacade;
        if ($sitemap) {
            $this->menu = $sitemap->menu->menu;
        } else {
            $group = $this->groupFacade->repository->getGroupByKey('front');
            $this->menu = $group->menu;
        }
    }

    public function render() {
        $template = $this->template;
        $template->menu = $this->menu;
        $template->setFile($this->getTemplateFiles('list'));
        $template->render();
    }

    public function renderXml() {
        $template = $this->template;
        $template->menu = $this->menuFacade->repository->getChildren($this->menu);
        $template->setFile($this->getTemplateFiles('xml'));
        $template->render();
    }

}
