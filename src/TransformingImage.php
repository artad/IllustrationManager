<?php

namespace IllustrationManager;

use IllustrationManager\Format\Format;
use Imagine\Image\Box;
use Imagine\Image\Color;
use Imagine\Image\ImagineInterface;
use Imagine\Image\ImageInterface;
use Gaufrette\Filesystem;
use Imagine\Image\Point;

class TransformingImage
{

    protected $imagine;
    protected $filesystem;

    /**
     *
     * @param \Imagine\Image\ImagineInterface $imagine
     * @param \Gaufrette\Filesystem $filesystem
     */
    public function __construct(ImagineInterface $imagine, Filesystem $filesystem)
    {
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
    public function transform($pathWFilename, $savePathWFilename, $extension, Format $formatConfig = null)
    {

        $fileContent = $this->filesystem->get($pathWFilename)->getContent();
        $image = $this->imagine->load($fileContent);

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
    protected function transformImage(ImageInterface $image, Format $formatConfig)
    {

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
    protected function resize(ImageInterface $image, Format $formatConfig)
    {

        $resizeWidth = $formatConfig->getResizeWidth();
        $resizeHeight = $formatConfig->getResizeHeight();

        $imageWidth = $image->getSize()->getWidth();
        $imageHeight = $image->getSize()->getHeight();

        $resizeBox = null;

        if ($resizeWidth && $resizeHeight) {

            if (!$formatConfig->doEnglareToFormat()) {
                $resizeWidth = $imageWidth > $resizeWidth ? $resizeWidth : $imageWidth;
                $resizeHeight = $imageHeight > $resizeHeight ? $resizeHeight : $imageHeight;
            }

            if ($resizeWidth != $imageWidth || $resizeHeight != $imageHeight) {
                $resizeBox = new Box($resizeWidth, $resizeHeight);
            }
        }


        if ($resizeWidth && !$resizeHeight && ($imageWidth > $resizeWidth || $formatConfig->doEnglareToFormat())) {
            $resizeBox = $image->getSize()->widen($resizeWidth);
        }

        if (!$resizeWidth && $resizeHeight && ($imageHeight > $resizeHeight || $formatConfig->doEnglareToFormat())) {
            $resizeBox = $image->getSize()->heighten($resizeHeight);
        }

        if($resizeBox) {
            $image->resize($resizeBox);
        }
    }

    /**
     *
     * @param \Imagine\Image\ImageInterface $image
     * @param \IllustrationManager\Format\Format $formatConfig
     */
    protected function crop(ImageInterface $image, Format $formatConfig)
    {
        $image->crop(new Point($formatConfig->getCropStartPointX(), $formatConfig->getCropStartPointY()), new Box($formatConfig->getCropWidth(), $formatConfig->getCropHeight()));
    }

    /**
     *
     * @param \Imagine\Image\ImageInterface $image
     * @param \IllustrationManager\Format\Format $formatConfig
     */
    protected function rotate(ImageInterface $image, Format $formatConfig)
    {
        $bgColor = $formatConfig->getRotateBackground() ? new Color($formatConfig->getRotateBackground()) : null;
        $image->rotate($formatConfig->getRotateAngle(), $bgColor);
    }

    /**
     *
     * @param \Imagine\Image\ImageInterface $image
     * @param \IllustrationManager\Format\Format $formatConfig
     */
    protected function flip(ImageInterface $image, Format $formatConfig)
    {
        if ($formatConfig->doFlipHorizontal()) {
            $image->flipHorizontally();
        }

        if ($formatConfig->doFlipVertical()) {
            $image->flipVertically();
        }
    }

}
