<?php

namespace IllustrationManager;

use Pimple\Container;

/**
 * Class FormatsCollection
 * @package IllustrationManager
 */
class FormatsCollection extends Container {

    /**
     * @param $formatName
     * @param $callable
     */
    public function addFormat($formatName, $callable) {
        $this->offsetSet($formatName, $callable);
    }

    /**
     * @param $formatName
     * @return Format\Format
     */
    public function getFormat($formatName) {
        return $this->offsetGet($formatName);
    }

}
