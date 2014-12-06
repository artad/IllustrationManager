<?php

namespace IllustrationManager\Test;

use IllustrationManager\IllustrationManagerConfig;

class IllustrationManagerConfigTest extends \PHPUnit_Framework_TestCase {

    /**
     * 
     * @return \IllustrationManager\NamesAndPaths
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
}
