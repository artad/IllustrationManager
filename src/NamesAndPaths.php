<?php

namespace IllustrationManager;

class NamesAndPaths {

    /**
     * @var
     */
    protected $hashCache;
    /**
     * @var IllustrationManagerConfig
     */
    protected $illustrationManagerConfig;

    /**
     * @param IllustrationManagerConfig $illustrationManagerConfig
     */
    public function __construct(IllustrationManagerConfig $illustrationManagerConfig) {
        $this->illustrationManagerConfig = $illustrationManagerConfig;
    }

    /**
     * @param $illustrationID
     * @param $extension
     * @param null $hash
     * @return string
     */
    public function getFullPathWFilenameForOriginal($illustrationID, $extension, $hash = null) {
        if (!$hash) {
            $hash = $this->getHashForId($illustrationID);
        }
        $filenameWExtension = $hash . '.' . $extension;
        return $this->illustrationManagerConfig->getFolderNameForOriginals() . DIRECTORY_SEPARATOR . $this->devideHashIntoPath($hash) . DIRECTORY_SEPARATOR . $filenameWExtension;
    }

    /**
     * @param $illustrationID
     * @param $extension
     * @param string $formatPrefix
     * @return string
     */
    public function getFullPathWFilename($illustrationID, $extension, $formatPrefix = '') {
        $pathByIDAndFormat = $this->getPathWFilenameById($illustrationID, $extension, $formatPrefix);
        return $this->illustrationManagerConfig->getBaseFolderName() . DIRECTORY_SEPARATOR . $formatPrefix . DIRECTORY_SEPARATOR . $pathByIDAndFormat;
    }

    /**
     * @param $id
     * @param string $extension
     * @param string $formatPrefix
     * @return string
     */
    public function getPathWFilenameById($id, $extension, $formatPrefix = '') {
        $hash = $this->getHashForId($id);
        $filename = $formatPrefix . "_" . $hash;
        return $this->devideHashIntoPath($hash) . DIRECTORY_SEPARATOR . $filename . '.' . $extension;
    }

    /**
     * @param $hash
     * @param string $formatPrefix
     * @return string
     */
    public function getFilenameByHash($hash, $formatPrefix = '') {
        return $formatPrefix . "_" . $hash;
    }

    /**
     * @param string $hash
     * @return string
     */
    public function devideHashIntoPath($hash) {
	$hash = (string) $hash;
        if(strlen($hash) < 4) {
            throw new \InvalidArgumentException('Hash string must be 4 chars minimum');
        }
        return $hash[0] . $hash[1] . DIRECTORY_SEPARATOR . $hash[2] . $hash[3];
    }

    /**
     * @param $id
     * @return string
     */
    public function getHashForId($id) {
        $this->hashCache = sha1($id);
        return $this->hashCache;
    }

    /**
     * @param $filename
     * @return string
     */
    public function getExtensionFromFilename($filename) {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }

}
