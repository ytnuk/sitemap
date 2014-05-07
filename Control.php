<?php

namespace WebEdit\Sitemap;

use WebEdit;
use WebEdit\Menu;

final class Control extends WebEdit\Control {

    private $menuRepository;
    private $groupRepository;
    private $menu;

    public function __construct($sitemap, Menu\Repository $menuRepository, Menu\Group\Repository $groupRepository) {
        $this->menuRepository = $menuRepository;
        $this->groupRepository = $groupRepository;
        if ($sitemap) {
            $this->menu = $sitemap->menu->menu;
        } else {
            $group = $this->groupRepository->getGroupByKey('front');
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
        $template->menu = $this->menuRepository->getChildren($this->menu);
        $template->setFile($this->getTemplateFiles('xml'));
        $template->render();
    }

}
