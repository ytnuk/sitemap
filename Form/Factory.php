<?php

namespace WebEdit\Sitemap\Form;

use WebEdit\Menu;

final class Factory {

    private $menuFormFactory;

    public function __construct(Menu\Form\Factory $menuFormFactory) {
        $this->menuFormFactory = $menuFormFactory;
    }

    public function create($sitemap = NULL) {
        $menu = $sitemap ? $sitemap->menu : NULL;
        return $this->menuFormFactory->create($menu);
    }

}
