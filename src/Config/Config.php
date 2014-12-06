<?php

namespace IllustrationManager\Config;

class Config {

    protected $resizeWidth;
    protected $resizeHeight;
    protected $englareToFormat = false;
    protected $cropWidth;
    protected $cropHeight;
    protected $cropStartPointX;
    protected $cropStartPointY;
    protected $cropFirst = false;
    protected $rotateAngle;
    protected $rotateBackground;
    protected $flipHorizontal = false;
    protected $flipVertical = false;
    protected $quality;
    protected $hash;

    public static function factory() {
        return new self;
    }

    public function __construct() {
        
    }

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

    public function cropFirst($cropFirst = true) {
        $this->cropFirst = $cropFirst;
        return $this;
    }
    
    public function englareToFormat($englareToFormat = true) {
        $this->englareToFormat = $englareToFormat;
        return $this;
    }

    public function rotate($degrees, $background = null) {
        $this->rotateAngle = (int) $degrees;
        $this->rotateBackground = $background;
        return $this;
    }

    public function flipHorizontal() {
        $this->flipHorizontal = true;
        return $this;
        
    }

    public function flipVertical() {
        $this->flipVertical = true;
        return $this;
    }

    public function setQuality($quality) {
        if (!is_numeric($quality)) {
            throw new \InvalidArgumentException('Quality should be Int');
        }
        $this->quality = $quality;
        return $this;
    }

    public function getHash() {
        if (!$this->hash) {
            $this->hash = hash('crc32', serialize(get_object_vars($this)));
        }
        return $this->hash;
    }

    public function getResizeWidth() {
        return $this->resizeWidth;
    }

    public function getResizeHeight() {
        return $this->resizeHeight;
    }

    public function getCropWidth() {
        return $this->cropWidth;
    }

    public function getCropHeight() {
        return $this->cropHeight;
    }

    public function getCropStartPointX() {
        return $this->cropStartPointX;
    }

    public function getCropStartPointY() {
        return $this->cropStartPointY;
    }

    public function getRotateAngle() {
        return $this->rotateAngle;
    }

    public function getRotateBackground() {
        return $this->rotateBackground;
    }

    public function doFlipHorizontal() {
        return $this->flipHorizontal;
    }

    public function doFlipVertical() {
        return $this->flipVertical;
    }

    public function getQuality() {
        return $this->quality;
    }

    public function doResize() {
        return $this->resizeWidth || $this->resizeHeight;
    }

    public function doCrop() {
        return $this->cropWidth || $this->cropHeight;
    }
    
    public function doCropFirst() {
        return $this->cropFirst;
    }
    
    public function doEnglareToFormat() {
        return $this->englareToFormat;
    }
    
    public function doRotate() {
        return (bool) $this->rotateAngle;  
    }
    
    public function doFlip() {
        return $this->flipHorizontal || $this->flipVertical;
    }

}
    