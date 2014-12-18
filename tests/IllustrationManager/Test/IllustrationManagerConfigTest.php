<?php

namespace IllustrationManager\Test;

use IllustrationManager\Format\Format;
use IllustrationManager\IllustrationManagerConfig;

class IllustrationManagerConfigTest extends \PHPUnit_Framework_TestCase {

    /**
     * 
     * @return \IllustrationManager\IllustrationManagerConfig
     */
    protected function getConfig() {
        $illustrationManagerConfig = new IllustrationManagerConfig();
        return $illustrationManagerConfig;
    }

    /**
     * @dataProvider providerUseCache
     */
    public function testUseCache($use, $isUse) {
        $illustrationManagerConfig = $this->getConfig();
        $illustrationManagerConfig->useCache($use);        
        $this->assertEquals($isUse, $illustrationManagerConfig->isUseCache());
    }



    /**
     *
     * @return type
     */
    public function providerUseCache() {
        return array(
            array(true, true),
            array(false, false),
        );
    }


    public function testBaseFolderName() {
        $illustrationManagerConfig = $this->getConfig();
        $illustrationManagerConfig->setBaseFolderName('ABC');
        $this->assertEquals('ABC', $illustrationManagerConfig->getBaseFolderName());
    }

    public function testFolderNameForOriginal() {
        $illustrationManagerConfig = $this->getConfig();
        $illustrationManagerConfig->setFolderNameForOriginals('ABC');
        $this->assertEquals('ABC', $illustrationManagerConfig->getFolderNameForOriginals());
    }


    public function testFormatForOriginal() {
        $illustrationManagerConfig = $this->getConfig();
        $format = new Format();
        $format->resize(100,115);
        $illustrationManagerConfig->setConfigForOriginal($format);
        $this->assertSame($format, $illustrationManagerConfig->getConfigForOriginal());
    }

    public function testConfigsAfterUpload() {
        $illustrationManagerConfig = $this->getConfig();

        $format = new Format();
        $format->resize(100,115);
        $format_ = new Format();
        $format_->resize(1000,1150);

        $illustrationManagerConfig->addConfigToGenerateAfterUpload($format);
        $illustrationManagerConfig->addConfigToGenerateAfterUpload($format_);

        $this->assertContains($format, $illustrationManagerConfig->getConfigsToGenerateAfterUpload());
        $this->assertContains($format_, $illustrationManagerConfig->getConfigsToGenerateAfterUpload());
    }
}
