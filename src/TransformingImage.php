<?php

namespace IllustrationManager;

use IllustrationManager\Config\Config;
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
     * @param \IllustrationManager\Config\Config $config
     */
    public function transform($pathWFilename, $savePathWFilename, $extension, Config $config = null) {

        $image = $this->imagine->load($this->filesystem->get($pathWFilename)->getContent());

        if ($config) {
            $this->transformImage($image, $config);
        }
        $imageContent = $image->get($extension);
        $this->filesystem->write($savePathWFilename, $imageContent, true);
    }

    /**
     * 
     * @param \Imagine\Image\ImageInterface $image
     * @param \IllustrationManager\Config\Config $config
     */
    protected function transformImage(ImageInterface $image, Config $config) {

        if ($config->doCrop() && $config->doCropFirst()) {
            $this->crop($image, $config);
        }

        if ($config->doResize()) {
            $this->resize($image, $config);
        }

        if ($config->doCrop() && !$config->doCropFirst()) {
            $this->crop($image, $config);
        }

        if ($config->doRotate()) {
            $this->rotate($image, $config);
        }

        if ($config->doFlip()) {
            $this->flip($image, $config);
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
