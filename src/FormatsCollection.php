<?php

namespace IllustrationManager;

use IllustrationManager\Exception\UndefinedFormatException;
use IllustrationManager\Exception\UnexpectedFormatException;
use IllustrationManager\Format\Format;
use Pimple\Container;

/**
 * Class FormatsCollection
 * @package IllustrationManager
 */
class FormatsCollection extends Container
{

    /**
     * @param $formatName
     * @param $callable
     */
    public function addFormat($formatName, $callable)
    {
        $this->offsetSet($formatName, $callable);
    }

    /**
     * @param $formatName
     * @return Format\Format
     */
    public function getFormat($formatName)
    {
        try {
            $format = $this->offsetGet($formatName);
        } catch (\InvalidArgumentException $e) {
            throw new UndefinedFormatException(sprintf('Undefined illustration format – %s', $formatName));
        }

        if (!$format instanceof Format) {
            throw new UnexpectedFormatException('Object in container must be instance of Format class');
        }
        return $format;
    }

}
