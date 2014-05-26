<?php

namespace WebEdit\Sitemap\Form\Control;

use WebEdit\Sitemap\Form;

interface Factory {

    /**
     * @return Form\Control
     */
    public function create();
}
