<?php

namespace IllustrationManager;

use \IllustrationManager\Exception\UndefinedFormatException;
use \Gaufrette\Filesystem;
use Predis\Client;

/**
 * Class Manager
 * @package IllustrationManager
 */
class Manager
{

    /**
     * @var IllustrationManagerConfig
     */
    protected $illustrationManagerConfig;
    /**
     * @var NamesAndPaths
     */
    protected $namesAndPaths;
    /**
     * @var FormatsCollection
     */
    protected $formatsCollection;
    /**
     * @var ImageGenerator
     */
    protected $imageGenerator;
    /**
     * @var Filesystem
     */
    protected $filesystem;
    /**
     * @var \Predis\Client
     */
    protected $predis;

    /**
     * @param IllustrationManagerConfig $illustrationManagerConfig
     * @param NamesAndPaths $namesAndPaths
     * @param FormatsCollection $formatsCollection
     * @param ImageGenerator $imageGenerator
     * @param Filesystem $filesystem
     * @param \Predis\Client $predis
     */
    public function __construct(IllustrationManagerConfig $illustrationManagerConfig, NamesAndPaths $namesAndPaths,
                                FormatsCollection $formatsCollection, ImageGenerator $imageGenerator, Filesystem $filesystem,
                                Client $predis = null)
    {
        $this->illustrationManagerConfig = $illustrationManagerConfig;
        $this->namesAndPaths = $namesAndPaths;
        $this->formatsCollection = $formatsCollection;
        $this->imageGenerator = $imageGenerator;
        $this->filesystem = $filesystem;
        $this->predis = $predis;
    }

    /**
     * @param $pathToUploadedWFile
     * @param $illustrationID
     * @return string
     */
    public function handleUpload($pathToUploadedWFile, $illustrationID)
    {
        $path = $this->imageGenerator->handleUpload($pathToUploadedWFile, $illustrationID);
        return $path;
    }

    /**
     * @param $illustrationID
     * @param $extension
     * @param $formatName
     * @return string
     * @throws UndefinedFormatException
     */
    public function getThumb($illustrationID, $extension, $formatName)
    {

        $config = $this->getFormatByName($formatName);
        $configHash = $config->getHash();

        $useCache = $this->illustrationManagerConfig->isUseCache();
        if ($useCache && $this->predis) {
            if ($cached = $this->checkCache($illustrationID, $configHash)) {
                return $cached;
            }
        }

        $savePathWFile = $this->namesAndPaths->getFullPathWFilename($illustrationID, $extension, $configHash);

        if ($this->checkFileExistence($savePathWFile)) {
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
     * @param $formatName
     * @return \IllustrationManager\Format\Format
     * @throws UndefinedFormatException
     */
    protected function getFormatByName($formatName)
    {
        try {
            $config = $this->formatsCollection->getFormat($formatName);
        } catch (\InvalidArgumentException $e) {
            throw new UndefinedFormatException(sprintf('Undefined illustration format – %s', $formatName));
        }
        return $config;
    }

    /**
     * @param $illustrationID
     * @param string $configHash
     * @return string
     */
    protected  function checkCache($illustrationID, $configHash)
    {
        return $this->predis->get($illustrationID . $configHash);
    }

    /**
     * @param $illustrationID
     * @param string $configHash
     * @param string $value
     */
    protected function setCache($illustrationID, $configHash, $value)
    {
        $this->predis->set($illustrationID . $configHash, $value);
        $this->predis->expire($illustrationID . $configHash, 3600);
    }

    /**
     * @param string $pathWFile
     * @return bool
     */
    protected function checkFileExistence($pathWFile)
    {
        return $this->filesystem->has($pathWFile);
    }

}
