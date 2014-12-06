<?php

namespace IllustrationManager;

use IllustrationManager\Config\Config;

class IllustrationManagerConfig {

    protected $useCache = false;
    protected $configForOriginal;
    protected $baseFolderName = 'illustrations';
    protected $folderNameForOriginals = 'illustrations/original';
    protected $configsToGenerateAfterUpload = array();
    
    
    public function useCache($use = true) {
        if($use) {
            $this->useCache = (bool) $use;
        }
        return $this;
    }

    public function setBaseFolderName($baseFolderName) {
        $this->baseFolderName = $baseFolderName;
    }
    
    public function setFolderNameForOriginals($folderNameForOriginals) {
        $this->folderNameForOriginals = $folderNameForOriginals;
    }

    public function setConfigForOriginal(Config $config) {
        $this->configForOriginal = $config;
        return $this;
    }

    public function isUseCache() {
        return $this->useCache;
    }
    
    public function getConfigForOriginal() {
        return $this->configForOriginal;
    }

    public function getBaseFolderName() {
        return $this->baseFolderName;
    }
    
    
    public function getFolderNameForOriginals() {
        return $this->folderNameForOriginals;
    }

    public function addConfigToGenerateAfterUpload(Config $config) {
        $this->configsToGenerateAfterUpload[] = $config;
        return $this;
    }
    
    public function getConfigsToGenerateAfterUpload() {
        return $this->configsToGenerateAfterUpload;
    }
}
