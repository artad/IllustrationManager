<?php

namespace IllustrationManager;

use Gaufrette\Filesystem;
use IllustrationManager\Format\Format;
use Imagine\Image\ImagineInterface;

/**
 * Class ImageGenerator
 * @package IllustrationManager
 */
class ImageGenerator {

    /**
     * @var IllustrationManagerConfig
     */
    protected $illustrationManagerConfig;
    /**
     * @var FormatsCollection
     */
    protected $formatsCollection;

    /**
     * @var ImagineInterface
     */
    protected $imagine;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var TransformingImage
     */
    protected $transformingImage;
    /**
     * @var NamesAndPaths
     */
    protected $namesAndPaths;

    /**
     * @param IllustrationManagerConfig $illustrationManagerConfig
     * @param FormatsCollection $formatsCollection
     * @param NamesAndPaths $namesAndPaths
     * @param TransformingImage $transformingImage
     */
    public function __construct(IllustrationManagerConfig $illustrationManagerConfig,
            FormatsCollection $formatsCollection, NamesAndPaths $namesAndPaths, Filesystem $filesystem, ImagineInterface $imagine, TransformingImage $transformingImage) {
        $this->illustrationManagerConfig = $illustrationManagerConfig;
        $this->formatsCollection = $formatsCollection;
        $this->namesAndPaths = $namesAndPaths;
        $this->filesystem = $filesystem;
        $this->imagine = $imagine;
        $this->transformingImage = $transformingImage;
    }


    /**
     * @param $pathToUploadedWFile
     * @param $illustrationID
     * @return string
     */
    public function handleUpload($pathToUploadedWFile, $illustrationID) {
        $extension = $this->namesAndPaths->getExtensionFromFilename($pathToUploadedWFile);
        $savePathWFile = $this->namesAndPaths->getFullPathWFilenameForOriginal($illustrationID, $extension);
        $this->generate($pathToUploadedWFile, $savePathWFile, $extension, $this->illustrationManagerConfig->getConfigForOriginal());
        foreach ($this->illustrationManagerConfig->getConfigsToGenerateAfterUpload() as $config) {
            $this->makeThumbByID($illustrationID, $extension, $config);
        }
        return $savePathWFile;
    }

    /**
     * @param $illustrationID
     * @param $extension
     * @param Format $config
     * @param string $savePathWFile
     * @return string
     */
    public function makeThumbByID($illustrationID, $extension, Format $config, $savePathWFile = null) {
        if (!$savePathWFile) {
            $configHash = $config->getHash();
            $savePathWFile = $this->namesAndPaths->getFullPathWFilename($illustrationID, $extension, $configHash);
        }
        $this->generate($this->namesAndPaths->getFullPathWFilenameForOriginal($illustrationID, $extension), $savePathWFile, $extension, $config);
        return $savePathWFile;
    }

    /**
     * @param $pathWFilename
     * @param string $savePathWFilename
     * @param $extension
     * @param Format $config
     */
    protected  function generate($pathWFilename, $savePathWFilename, $extension, Format $config = null) {
        $fileContent = $this->filesystem->get($pathWFilename)->getContent();
        $image = $this->imagine->load($fileContent);

        if($config) {
            $this->transformingImage->transformImage($image, $config);
        }

        $imageContent = $image->get($extension);
        $this->filesystem->write($savePathWFilename, $imageContent, true);
    }
}
