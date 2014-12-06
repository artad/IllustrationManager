<?php

namespace IllustrationManager;

use IllustrationManager\Config\Config;
use IllustrationManager\Exception\UndefinedFormatException;

class ImageGenerator {

    protected $illustrationManagerConfig;
    protected $formatsCollection;
    protected $imagine;
    protected $transformingImage;
    protected $namesAndPathes;

    public function __construct(IllustrationManagerConfig $illustrationManagerConfig,
            FormatsCollection $formatsCollection, NamesAndPaths $namesAndPathes, TransformingImage $transformingImage) {
        $this->illustrationManagerConfig = $illustrationManagerConfig;
        $this->formatsCollection = $formatsCollection;
        $this->namesAndPathes = $namesAndPathes;
        $this->transformingImage = $transformingImage;
    }

    /**
     * 
     * @param type $pathToUploadedWFile
     * @param type $illustrationID
     * @return type
     */
    public function handleUpload($pathToUploadedWFile, $illustrationID) {
        $extension = $this->namesAndPathes->getExtensionFromFilename($pathToUploadedWFile);
        $savePathWFile = $this->namesAndPathes->getFullPathWFilenameForOriginal($illustrationID, $extension);
        $this->generate($pathToUploadedWFile, $savePathWFile, $extension, $this->illustrationManagerConfig->getConfigForOriginal());

        foreach ($this->illustrationManagerConfig->getConfigsToGenerateAfterUpload() as $config) {
            $this->makeThumbByID($illustrationID, $extension, $config);
        }
        return $savePathWFile;
    }

    /**
     * 
     * @param type $illustrationID
     * @param type $extension
     * @param \IllustrationManager\Config\Config $config
     * @return string
     */
    public function makeThumbByID($illustrationID, $extension, Config $config, $savePathWFile = null) {

        if (!$savePathWFile) {
            $configHash = $config->getHash();
            $savePathWFile = $this->namesAndPathes->getFullPathWFilename($illustrationID, $extension, $configHash);
        }

        $this->generate($this->namesAndPathes->getFullPathWFilenameForOriginal($illustrationID, $extension), $savePathWFile, $extension, $config);
        return $savePathWFile;
    }

    /**
     * 
     * @param type $pathWFilename
     * @param type $savePathWFilename
     * @param \IllustrationManager\Config\Config $config
     */
    public function generate($pathWFilename, $savePathWFilename, $extension, Config $config = null) {
        $this->transformingImage->transform($pathWFilename, $savePathWFilename, $extension, $config);
    }

}
