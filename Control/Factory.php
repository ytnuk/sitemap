<?php

namespace WebEdit\Sitemap\Control;

use WebEdit\Sitemap\Control;

interface Factory {

    /**
     * @return Control
     */
    public function create($sitemap = NULL);
}
