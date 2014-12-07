<?php

namespace IllustrationManager;

use IllustrationManager\Format\Format;

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
     * @var
     */
    protected $imagine;
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
     * @param NamesAndPaths $namesAndPathes
     * @param TransformingImage $transformingImage
     */
    public function __construct(IllustrationManagerConfig $illustrationManagerConfig,
            FormatsCollection $formatsCollection, NamesAndPaths $namesAndPathes, TransformingImage $transformingImage) {
        $this->illustrationManagerConfig = $illustrationManagerConfig;
        $this->formatsCollection = $formatsCollection;
        $this->namesAndPaths = $namesAndPathes;
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
    public function generate($pathWFilename, $savePathWFilename, $extension, Format $config = null) {
        $this->transformingImage->transform($pathWFilename, $savePathWFilename, $extension, $config);
    }

}
