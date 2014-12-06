<?php

namespace IllustrationManager;

use \IllustrationManager\Exception\UndefinedFormatException;
use \Gaufrette\Filesystem;

class Manager {

    protected $illustrationManagerConfig;
    protected $namesAndPathes;
    protected $formatsCollection;
    protected $imageGenerator;
    protected $filesystem;
    protected $predis;

    /**
     * 
     * @param \IllustrationManager\NamesAndPaths $namesAndPathes
     * @param \IllustrationManager\ImageGenerator $imageGenerator
     * @param \Gaufrette\Filesystem $filesystem
     */
    public function __construct(IllustrationManagerConfig $illustrationManagerConfig, NamesAndPaths $namesAndPathes,
            FormatsCollection $formatsCollection, ImageGenerator $imageGenerator, Filesystem $filesystem,
            \Predis\Client $predis = null) {
        $this->illustrationManagerConfig = $illustrationManagerConfig;
        $this->namesAndPathes = $namesAndPathes;
        $this->formatsCollection = $formatsCollection;
        $this->imageGenerator = $imageGenerator;
        $this->filesystem = $filesystem;
        $this->predis = $predis;
    }

    /**
     * 
     * @param string $pathToUploadedWFile
     * @param string|integer $illustrationID
     * @return string
     */
    public function handleUpload($pathToUploadedWFile, $illustrationID) {
        $path = $this->imageGenerator->handleUpload($pathToUploadedWFile, $illustrationID);
        return $path;
    }

    /**
     * 
     * @param type $illustrationID
     * @param type $extension
     * @param type $formatName
     * @return string
     */
    public function getThumb($illustrationID, $extension, $formatName) {

        $config = $this->getFormatConfigByName($formatName);
        $configHash = $config->getHash();
        
        $useCache = $this->illustrationManagerConfig->isUseCache();
        if ($useCache && $this->predis) {
            if ($cached = $this->checkCache($illustrationID, $configHash)) {
                return $cached;
            }
        }

        $savePathWFile = $this->namesAndPathes->getFullPathWFilename($illustrationID, $extension, $configHash);

        if ($this->checkFileExistance($savePathWFile)) {
            if ($useCache && $this->predis) {
                $this->setCache($illustrationID, $configHash, $savePathWFile);
            }
            return $savePathWFile;
        }

        $this->imageGenerator->makeThumbByID($illustrationID, $extension, $config, $savePathWFile);

        if ($useCache && $this->predis) {
            $this->setCache($illustrationID, $configHash, $savePathWFile);
        }
        return $savePathWFile;
    }

    /**
     * 
     * @param string $formatName
     * @return  Config
     * @throws UndefinedFormatException
     */
    protected function getFormatConfigByName($formatName) {
        try {
            $config = $this->formatsCollection->getFormat($formatName);
            return $config;
        } catch (\InvalidArgumentException $e) {
            throw new UndefinedFormatException(sprintf('Undefined illustration format – %s', $formatName));
        }
    }

    /**
     * 
     * @param type $illustrationID
     * @param type $formatName
     * @return null
     */
    protected function checkCache($illustrationID, $configHash) {
        return $this->predis->get($illustrationID . $configHash);
    }

    /**
     * 
     * @param type $illustrationID
     * @param type $configHash
     * @return null
     */
    protected function setCache($illustrationID, $configHash, $value) {
        $this->predis->set($illustrationID . $configHash, $value);
        $this->predis->expire($illustrationID . $configHash, 3600);
    }

    /**
     * 
     * @param string $pathWFile
     * @return bool
     */
    protected function checkFileExistance($pathWFile) {
        return $this->filesystem->has($pathWFile);
    }

}
