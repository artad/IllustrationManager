<?php

namespace IllustrationManager;


use Gaufrette\Adapter;
use Imagine\Image\ImagineInterface;

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


        $predisClient = $this->illustrationManagerConfig->isUseCache() ? new \Predis\Client(null, array('prefix' => 'IllustrationManager')) : null;

        if(!$this->imagine) {
            $this->imagine = $this->getDefaultImagine();
        }

        if(!$this->filesystemAdapter) {
            $this->filesystemAdapter = $this->getDefaultFilesystemAdapter();
        }

        $filesystem = new \Gaufrette\Filesystem($this->filesystemAdapter);
        $namesAndPaths = new \IllustrationManager\NamesAndPaths($this->illustrationManagerConfig);
        $transformingImage = new \IllustrationManager\TransformingImage(
            $this->imagine,
            $filesystem
        );
        $imageGenerator = new \IllustrationManager\ImageGenerator(
            $this->illustrationManagerConfig,
            $this->formatsCollection,
            $namesAndPaths,
            $transformingImage
        );
        return new \IllustrationManager\Manager(
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
        return new \Imagine\Gd\Imagine();
    }


    /**
     * @return Adapter\Local
     */
    protected function getDefaultFilesystemAdapter() {
        return new \Gaufrette\Adapter\Local($_SERVER["DOCUMENT_ROOT"]);
    }
} 