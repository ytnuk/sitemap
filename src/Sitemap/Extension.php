<?php

namespace WebEdit\Sitemap;

use WebEdit;

final class Extension extends WebEdit\Extension {

    public function loadConfiguration() {
        $builder = $this->getContainerBuilder();
        $builder->addDefinition($this->prefix('repository'))
                ->setClass('WebEdit\Sitemap\Repository');
        $builder->addDefinition($this->prefix('facade'))
                ->setClass('WebEdit\Sitemap\Facade');
        $builder->addDefinition($this->prefix('control'))
                ->setImplement('WebEdit\Sitemap\Control\Factory');
        $builder->addDefinition($this->prefix('form.control'))
                ->setImplement('WebEdit\Sitemap\Form\Control\Factory');
    }

}
