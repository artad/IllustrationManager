<?php

namespace IllustrationManager;

use IllustrationManager\Format\Format;

/**
 * Class IllustrationManagerConfig
 * @package IllustrationManager
 */
class IllustrationManagerConfig {

    /**
     * @var bool
     */
    protected $useCache = false;
    /**
     * @var
     */
    protected $configForOriginal;
    /**
     * @var string
     */
    protected $baseFolderName;
    /**
     * @var string
     */
    protected $folderNameForOriginals;
    /**
     * @var array
     */
    protected $configsToGenerateAfterUpload = array();


    /**
     *
     */
    public function __construct() {
        $this->baseFolderName = 'illustrations';
        $this->folderNameForOriginals = $this->baseFolderName.DIRECTORY_SEPARATOR.'original';
    }

    /**
     * @param bool $use
     * @return $this
     */
    public function useCache($use = true) {
        if($use) {
            $this->useCache = (bool) $use;
        }
        return $this;
    }

    /**
     * @param $baseFolderName
     */
    public function setBaseFolderName($baseFolderName) {
        $this->baseFolderName = $baseFolderName;
    }

    /**
     * @param $folderNameForOriginals
     */
    public function setFolderNameForOriginals($folderNameForOriginals) {
        $this->folderNameForOriginals = $folderNameForOriginals;
    }

    /**
     * @param Format $config
     * @return $this
     */
    public function setConfigForOriginal(Format $config) {
        $this->configForOriginal = $config;
        return $this;
    }

    /**
     * @return bool
     */
    public function isUseCache() {
        return $this->useCache;
    }

    /**
     * @return mixed
     */
    public function getConfigForOriginal() {
        return $this->configForOriginal;
    }

    /**
     * @return string
     */
    public function getBaseFolderName() {
        return $this->baseFolderName;
    }


    /**
     * @return string
     */
    public function getFolderNameForOriginals() {
        return $this->folderNameForOriginals;
    }

    /**
     * @param Format $config
     * @return $this
     */
    public function addConfigToGenerateAfterUpload(Format $config) {
        $this->configsToGenerateAfterUpload[] = $config;
        return $this;
    }

    /**
     * @return array
     */
    public function getConfigsToGenerateAfterUpload() {
        return $this->configsToGenerateAfterUpload;
    }
}
