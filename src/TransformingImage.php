<?php

namespace IllustrationManager;

use IllustrationManager\Format\Format;
use Imagine\Image\Box;
use Imagine\Image\Color;
use Imagine\Image\ImagineInterface;
use Imagine\Image\ImageInterface;
use Gaufrette\Filesystem;
use Imagine\Image\Point;

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
     * @param string $pathWFilename
     * @param string $savePathWFilename
     * @param string $extension
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
     * @param \IllustrationManager\Format\Format $formatConfig
     */
    protected function resize(ImageInterface $image, Format $formatConfig) {

        $width = $formatConfig->getResizeWidth();
        $height = $formatConfig->getResizeHeight();

        if ($width && $height) {            
            if(!$formatConfig->doEnglareToFormat()) {
                $currentImageWidth = $image->getSize()->getWidth(); 
                $width = $currentImageWidth>$width ? $width : $currentImageWidth;
                $currentImageHeight = $image->getSize()->getHeight(); 
                $height = $currentImageHeight>$height ? $height : $currentImageHeight;
            }
            $image->resize(new Box($width, $height));
        }

        if ($width && !$height && ($image->getSize()->getWidth()>$width || $formatConfig->doEnglareToFormat())) {
            
            $image->resize($image->getSize()->widen($width));
        }

        if (!$width && $height  && ($image->getSize()->getHeight()>$height || $formatConfig->doEnglareToFormat())) {
            $image->resize($image->getSize()->heighten($height));
        }
    }

    /**
     * 
     * @param \Imagine\Image\ImageInterface $image
     * @param \IllustrationManager\Format\Format $formatConfig
     */
    protected function crop(ImageInterface $image, Format $formatConfig) {
        $image->crop(new Point($formatConfig->getCropStartPointX(), $formatConfig->getCropStartPointY()), new Box($formatConfig->getCropWidth(), $formatConfig->getCropHeight()));
    }

    /**
     * 
     * @param \Imagine\Image\ImageInterface $image
     * @param \IllustrationManager\Format\Format $formatConfig
     */
    protected function rotate(ImageInterface $image, Format $formatConfig) {
        $bgColor = $formatConfig->getRotateBackground() ? new Color($formatConfig->getRotateBackground()) : null;
        $image->rotate($formatConfig->getRotateAngle(), $bgColor);
    }

    /**
     * 
     * @param \Imagine\Image\ImageInterface $image
     * @param \IllustrationManager\Format\Format $formatConfig
     */
    protected function flip(ImageInterface $image, Format $formatConfig) {
        if ($formatConfig->doFlipHorizontal()) {
            $image->flipHorizontally();
        }

        if ($formatConfig->doFlipVertical()) {
            $image->flipVertically();
        }
    }

}
