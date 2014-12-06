<?php

namespace IllustrationManager;

use Pimple\Container;

class FormatsCollection extends Container {

    public function addFormat($formatName, $callable) {
        $this->offsetSet($formatName, $callable);
    }

    public function getFormat($formatName) {
        return $this->offsetGet($formatName);
    }

}
