<?php

namespace IllustrationManager;

use IllustrationManager\Format\Format;
use Imagine\Image\ImagineInterface;
use Imagine\Image\ImageInterface;
use Gaufrette\Filesystem;

class TransformingImage {

    protected $imagine;
    protected $filesystem;

    /**
     * 
     * @param \Imagine\Image\ImagineInterface $imagine
     * @param \Gaufrette\Filesystem $filesystem
     */
    public function __construct(ImagineInterface $imagine, Filesystem $filesystem) {
        $this->imagine = $imagine;
        $this->filesystem = $filesystem;
    }

    /**
     * 
     * @param type $pathWFilename
     * @param type $savePathWFilename
     * @param type $extension
     * @param \IllustrationManager\Format\Format $formatConfig
     */
    public function transform($pathWFilename, $savePathWFilename, $extension, Format $formatConfig = null) {

        $image = $this->imagine->load($this->filesystem->get($pathWFilename)->getContent());

        if ($formatConfig) {
            $this->transformImage($image, $formatConfig);
        }
        $imageContent = $image->get($extension);
        $this->filesystem->write($savePathWFilename, $imageContent, true);
    }

    /**
     * 
     * @param \Imagine\Image\ImageInterface $image
     * @param \IllustrationManager\Format\Format $formatConfig
     */
    protected function transformImage(ImageInterface $image, Format $formatConfig) {

        if ($formatConfig->doCrop() && $formatConfig->doCropFirst()) {
            $this->crop($image, $formatConfig);
        }

        if ($formatConfig->doResize()) {
            $this->resize($image, $formatConfig);
        }

        if ($formatConfig->doCrop() && !$formatConfig->doCropFirst()) {
            $this->crop($image, $formatConfig);
        }

        if ($formatConfig->doRotate()) {
            $this->rotate($image, $formatConfig);
        }

        if ($formatConfig->doFlip()) {
            $this->flip($image, $formatConfig);
        }
    }

    /**
     * 
     * @param \Imagine\Image\ImageInterface $image
     * @param \IllustrationManager\Config\Config $config
     */
    protected function resize(ImageInterface $image, Config $config) {

        $width = $config->getResizeWidth();
        $height = $config->getResizeHeight();

        if ($width && $height) {            
            if(!$config->doEnglareToFormat()) {
                $currentImageWidth = $image->getSize()->getWidth(); 
                $width = $currentImageWidth>$width ? $width : $currentImageWidth;
                $currentImageHeight = $image->getSize()->getHeight(); 
                $height = $currentImageHeight>$height ? $height : $currentImageHeight;
            }
            $image->resize(new \Imagine\Image\Box($width, $height));
        }

        if ($width && !$height && ($image->getSize()->getWidth()>$width || $config->doEnglareToFormat())) {
            
            $image->resize($image->getSize()->widen($width));
        }

        if (!$width && $height  && ($image->getSize()->getHeight()>$height || $config->doEnglareToFormat())) {
            $image->resize($image->getSize()->heighten($height));
        }
    }

    /**
     * 
     * @param \Imagine\Image\ImageInterface $image
     * @param \IllustrationManager\Config\Config $config
     */
    protected function crop(ImageInterface $image, Config $config) {
        $image->crop(new \Imagine\Image\Point($config->getCropStartPointX(), $config->getCropStartPointY()), new \Imagine\Image\Box($config->getCropWidth(), $config->getCropHeight()));
    }

    /**
     * 
     * @param \Imagine\Image\ImageInterface $image
     * @param \IllustrationManager\Config\Config $config
     */
    protected function rotate(ImageInterface $image, Config $config) {
        $bgColor = $config->getRotateBackground() ? new \Imagine\Image\Color($config->getRotateBackground()) : null; 
        $image->rotate($config->getRotateAngle(), $bgColor);
    }

    /**
     * 
     * @param \Imagine\Image\ImageInterface $image
     * @param \IllustrationManager\Config\Config $config
     */
    protected function flip(ImageInterface $image, Config $config) {
        if ($config->doFlipHorizontal()) {
            $image->flipHorizontally();
        }

        if ($config->doFlipVertical()) {
            $image->flipVertically();
        }
    }

}
