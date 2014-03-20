<?php

namespace WebEdit\Sitemap;

use WebEdit\Menu;
use WebEdit\Sitemap;

final class Facade {

    public $repository;
    private $menuFacade;

    public function __construct(Sitemap\Repository $repository, Menu\Facade $menuFacade) {
        $this->repository = $repository;
        $this->menuFacade = $menuFacade;
    }

    public function addSitemap(array $data) {
        $menu = $this->menuFacade->addMenu($data);
        $data['sitemap']['menu_id'] = $menu->id;
        $sitemap = $this->repository->insert($data['sitemap']);
        $data['menu']['link'] = ':Sitemap:Presenter:view';
        $data['menu']['link_id'] = $sitemap->id;
        $this->menuFacade->editMenu($menu, $data);
        return $sitemap;
    }

    public function editSitemap($sitemap, array $data) {
        $this->menuFacade->editMenu($sitemap->menu, $data);
    }

    public function deleteSitemap($sitemap) {
        $this->menuFacade->deleteMenu($sitemap->menu);
        $this->repository->remove($sitemap);
    }

}
