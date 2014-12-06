<?php

namespace IllustrationManager;

class NamesAndPaths {

    protected $hashCache;
    protected $illustrationManagerConfig;

    public function __construct(IllustrationManagerConfig $illustrationManagerConfig) {
        $this->illustrationManagerConfig = $illustrationManagerConfig;
    }

    /**
     * 
     * @param type $illustrationID
     * @param type $extension
     * @param type $hash
     * @return type
     */
    public function getFullPathWFilenameForOriginal($illustrationID, $extension, $hash = null) {
        if (!$hash) {
            $hash = $this->getHashForId($illustrationID);
        }
        $filenameWExtension = $hash . '.' . $extension;
        return $this->illustrationManagerConfig->getFolderNameForOriginals() . DIRECTORY_SEPARATOR . $this->devideHashIntoPath($hash) . DIRECTORY_SEPARATOR . $filenameWExtension;
    }

    /**
     * 
     * @param type $id
     * @param type $extension
     * @param type $formatPrefix
     * @return type
     */
    public function getFullPathWFilename($illustrationID, $extension, $formatPrefix = '') {
        $pathByIDAndFormat = $this->getPathWFilenameById($illustrationID, $extension, $formatPrefix);
        return $this->illustrationManagerConfig->getBaseFolderName() . DIRECTORY_SEPARATOR . $formatPrefix . DIRECTORY_SEPARATOR . $pathByIDAndFormat;
    }

    /**
     * 
     * @param type $id
     * @param type $extension
     * @param type $formatPrefix
     * @return type
     */
    public function getPathWFilenameById($id, $extension, $formatPrefix = '') {
        $hash = $this->getHashForId($id);
        $filename = $formatPrefix . "_" . $hash;
        return $this->devideHashIntoPath($hash) . DIRECTORY_SEPARATOR . $filename . '.' . $extension;
    }

    /**
     * 
     * @param type $hash
     * @param type $formatPrefix
     * @return type
     */
    public function getFilenameByHash($hash, $formatPrefix = '') {
        return $formatPrefix . "_" . $hash;
    }

    public function devideHashIntoPath($hash) {
	$hash = (string) $hash;
        return $hash[0] . $hash[1] . DIRECTORY_SEPARATOR . $hash[2] . $hash[3];
    }

    public function getHashForId($id) {
        $this->hashCache = sha1($id);
        return $this->hashCache;
    }

    public function getExtensionFromFilename($filename) {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }

}
