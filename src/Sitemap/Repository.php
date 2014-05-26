<?php

namespace WebEdit\Sitemap;

use WebEdit\Database;

class Repository extends Database\Repository {

    public function getSitemap($id) {
        return $this->storage()->get($id);
    }

}
