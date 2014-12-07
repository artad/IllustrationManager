<?php

namespace IllustrationManager\Format;

/**
 * Class Format
 * @package IllustrationManager\Format
 */
class Format {

    /**
     * @var
     */
    protected $resizeWidth;
    /**
     * @var
     */
    protected $resizeHeight;
    /**
     * @var bool
     */
    protected $englareToFormat = false;
    /**
     * @var
     */
    protected $cropWidth;
    /**
     * @var
     */
    protected $cropHeight;
    /**
     * @var
     */
    protected $cropStartPointX;
    /**
     * @var
     */
    protected $cropStartPointY;
    /**
     * @var bool
     */
    protected $cropFirst = false;
    /**
     * @var
     */
    protected $rotateAngle;
    /**
     * @var
     */
    protected $rotateBackground;
    /**
     * @var bool
     */
    protected $flipHorizontal = false;
    /**
     * @var bool
     */
    protected $flipVertical = false;
    /**
     * @var
     */
    protected $quality;
    /**
     * @var
     */
    protected $hash;

    /**
     * @return Format
     */
    public static function factory() {
        return new self;
    }

    /**
     *
     */
    public function __construct() {
        
    }

    /**
     * @param $width
     * @param null $height
     * @return $this
     */
    public function resize($width, $height = null) {

        if ($height < 1 && $width < 1) {
            throw new \InvalidArgumentException(sprintf(
                    'Width and Height cannot be 0 or negative, current size ' .
                    'is %sx%s', $width, $height
            ));
        }
        $this->resizeWidth = (int) $width;
        $this->resizeHeight = (int) $height;
        return $this;
    }

    /**
     * @param $width
     * @param $height
     * @param null $startPointX
     * @param null $startPointY
     * @return $this
     */
    public function crop($width, $height, $startPointX = null, $startPointY = null) {

        if ($height < 1 || $width < 1) {
            throw new \InvalidArgumentException(sprintf(
                    'Length of either side cannot be 0 or negative, current size ' .
                    'is %sx%s', $width, $height
            ));
        }

        if ($this->resizeWidth === null AND $this->resizeHeight === null) {
            $this->cropFirst = true;
        }

        $this->cropWidth = (int) $width;
        $this->cropHeight = (int) $height;
        $this->cropStartPointX = (int) $startPointX;
        $this->cropStartPointY = (int) $startPointY;
        return $this;
    }

    /**
     * @param bool $cropFirst
     * @return $this
     */
    public function cropFirst($cropFirst = true) {
        $this->cropFirst = $cropFirst;
        return $this;
    }

    /**
     * @param bool $englareToFormat
     * @return $this
     */
    public function englareToFormat($englareToFormat = true) {
        $this->englareToFormat = $englareToFormat;
        return $this;
    }

    /**
     * @param $degrees
     * @param null $background
     * @return $this
     */
    public function rotate($degrees, $background = null) {
        $this->rotateAngle = (int) $degrees;
        $this->rotateBackground = $background;
        return $this;
    }

    /**
     * @return $this
     */
    public function flipHorizontal() {
        $this->flipHorizontal = true;
        return $this;
        
    }

    /**
     * @return $this
     */
    public function flipVertical() {
        $this->flipVertical = true;
        return $this;
    }

    /**
     * @param $quality
     * @return $this
     */
    public function setQuality($quality) {
        if (!is_numeric($quality)) {
            throw new \InvalidArgumentException('Quality should be Int');
        }
        $this->quality = $quality;
        return $this;
    }

    /**
     * @return string
     */
    public function getHash() {
        if (!$this->hash) {
            $this->hash = hash('crc32', serialize(get_object_vars($this)));
        }
        return $this->hash;
    }

    /**
     * @return mixed
     */
    public function getResizeWidth() {
        return $this->resizeWidth;
    }

    /**
     * @return mixed
     */
    public function getResizeHeight() {
        return $this->resizeHeight;
    }

    /**
     * @return mixed
     */
    public function getCropWidth() {
        return $this->cropWidth;
    }

    /**
     * @return mixed
     */
    public function getCropHeight() {
        return $this->cropHeight;
    }

    /**
     * @return mixed
     */
    public function getCropStartPointX() {
        return $this->cropStartPointX;
    }

    /**
     * @return mixed
     */
    public function getCropStartPointY() {
        return $this->cropStartPointY;
    }

    /**
     * @return mixed
     */
    public function getRotateAngle() {
        return $this->rotateAngle;
    }

    /**
     * @return mixed
     */
    public function getRotateBackground() {
        return $this->rotateBackground;
    }

    /**
     * @return bool
     */
    public function doFlipHorizontal() {
        return $this->flipHorizontal;
    }

    /**
     * @return bool
     */
    public function doFlipVertical() {
        return $this->flipVertical;
    }

    /**
     * @return mixed
     */
    public function getQuality() {
        return $this->quality;
    }

    /**
     * @return bool
     */
    public function doResize() {
        return $this->resizeWidth || $this->resizeHeight;
    }

    /**
     * @return bool
     */
    public function doCrop() {
        return $this->cropWidth || $this->cropHeight;
    }

    /**
     * @return bool
     */
    public function doCropFirst() {
        return $this->cropFirst;
    }

    /**
     * @return bool
     */
    public function doEnglareToFormat() {
        return $this->englareToFormat;
    }

    /**
     * @return bool
     */
    public function doRotate() {
        return (bool) $this->rotateAngle;  
    }

    /**
     * @return bool
     */
    public function doFlip() {
        return $this->flipHorizontal || $this->flipVertical;
    }

}
    