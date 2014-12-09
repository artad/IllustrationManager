<?php

namespace IllustrationManager;


use Gaufrette\Adapter;
use Gaufrette\Filesystem;
use Imagine\Gd\Imagine;
use Imagine\Image\ImagineInterface;
use Predis\Client;

/**
 * Class Factory
 * @package IllustrationManager
 */
class Factory {

    /**
     * @var FormatsCollection
     */
    protected $formatsCollection;
    /**
     * @var IllustrationManagerConfig
     */
    protected $illustrationManagerConfig;
    /**
     * @var ImagineInterface
     */
    protected $imagine;
    /**
     * @var Adapter
     */
    protected $filesystemAdapter;

    /**
     * @param FormatsCollection $formatsCollection
     * @param IllustrationManagerConfig $illustrationManagerConfig
     * @param ImagineInterface $imagine
     * @param Adapter $filesystemAdapter
     */
    public function __construct(FormatsCollection $formatsCollection, IllustrationManagerConfig $illustrationManagerConfig, ImagineInterface $imagine = null, Adapter $filesystemAdapter = null) {
        $this->formatsCollection = $formatsCollection;
        $this->illustrationManagerConfig = $illustrationManagerConfig;
        $this->imagine = $imagine;
        $this->filesystemAdapter = $filesystemAdapter;
    }


    /**
     * @return Manager
     */
    public function getIllustrationManager() {

        $predisClient = $this->illustrationManagerConfig->isUseCache() ? new Client(null, array('prefix' => 'IllustrationManager')) : null;

        if(!$this->imagine) {
            $this->imagine = $this->getDefaultImagine();
        }

        if(!$this->filesystemAdapter) {
            $this->filesystemAdapter = $this->getDefaultFilesystemAdapter();
        }

        $filesystem = new Filesystem($this->filesystemAdapter);
        $namesAndPaths = new NamesAndPaths($this->illustrationManagerConfig);
        $transformingImage = new TransformingImage(
            $this->imagine,
            $filesystem
        );
        $imageGenerator = new ImageGenerator(
            $this->illustrationManagerConfig,
            $this->formatsCollection,
            $namesAndPaths,
            $transformingImage
        );
        return new Manager(
            $this->illustrationManagerConfig,
            $namesAndPaths,
            $this->formatsCollection,
            $imageGenerator,
            $filesystem,
            $predisClient
        );
    }


    /**
     * @return \Imagine\Gd\Imagine
     */
    protected function getDefaultImagine() {
        return new Imagine();
    }


    /**
     * @return Adapter\Local
     */
    protected function getDefaultFilesystemAdapter() {
        return new Adapter\Local($_SERVER["DOCUMENT_ROOT"]);
    }
} 