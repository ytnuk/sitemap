<?php

namespace WebEdit\Sitemap\Control;

use WebEdit\Sitemap;

interface Factory {

    /**
     * @return Sitemap\Control
     */
    public function create($sitemap = NULL);
}
