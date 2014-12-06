<?php

namespace IllustrationManager\Test;

use IllustrationManager\NamesAndPaths;

class NamesAndPathesTest extends \PHPUnit_Framework_TestCase {


    protected function getNamesAndPaths() {
        $illustrationManagerConfigMock = $this->getMock(
            'IllustrationManager\\IllustrationManagerConfig', array('getBaseFolderName', 'getFolderNameForOriginals')
        );

        $illustrationManagerConfigMock->method('getBaseFolderName')->withAnyParameters()->will($this->returnValue('base'));
        $illustrationManagerConfigMock->method('getFolderNameForOriginals')->withAnyParameters()->will($this->returnValue('base/original'));

        return new NamesAndPaths($illustrationManagerConfigMock);
    }

    /**
     *
     * @return \IllustrationManager\NamesAndPaths
     */
    protected function getNamesAndPathsMock() {
        $illustrationManagerConfigMock = $this->getMock(
                'IllustrationManager\\IllustrationManagerConfig', array('getBaseFolderName', 'getFolderNameForOriginals')
        );

        $illustrationManagerConfigMock->method('getBaseFolderName')->withAnyParameters()->will($this->returnValue('base'));
        $illustrationManagerConfigMock->method('getFolderNameForOriginals')->withAnyParameters()->will($this->returnValue('base/original'));


        $namesAndPathsMock = $this->getMock(
                'IllustrationManager\\NamesAndPaths', array('getHashForId'), array($illustrationManagerConfigMock)
        );

        $namesAndPathsMock->expects($this->any())->method('getHashForId')->with($this->anything())->will($this->returnValue('1234567890'));

        return $namesAndPathsMock;
    }

    /**
     *
     */
    public function testGetHashForIdMock() {
        $namesAndPathes = $this->getNamesAndPathsMock();
        $this->assertEquals('1234567890', $namesAndPathes->getHashForId(1));
    }

    /**
     * @dataProvider providerHashDevide
     * @param type $hash
     * @param type $path
     */
    public function testHashDevide($hash, $path) {
        $namesAndPathes = $this->getNamesAndPathsMock();
        $this->assertEquals($path, $namesAndPathes->devideHashIntoPath($hash));
    }



    /**
     *
     * @return type
     */
    public function providerHashDevide() {
        return array(
            array('1234', '12' . DIRECTORY_SEPARATOR . '34'),
            array('abcd', 'ab' . DIRECTORY_SEPARATOR . 'cd'),
            array('qwer', 'qw' . DIRECTORY_SEPARATOR . 'er'),
      	    array( 1000,   '10' . DIRECTORY_SEPARATOR . '00'),
           //array( 100,   '10' . DIRECTORY_SEPARATOR . '0'),
           //array( 1,   '10' . DIRECTORY_SEPARATOR . '0'),

        );
    }

    /**
     *
     */
    public function testGetPathWFilenameById() {
        $pathWFilename = $this->getNamesAndPathsMock()->getPathWFilenameById(1, 'jpg', 'prefix');
        $this->assertEquals('12/34/prefix_1234567890.jpg', $pathWFilename);
    }


    /**
     * @dataProvider providerGetFilenameByHash
     * @param $hash
     * @param $formatPrefix
     * @param $filename
     */
    public function testGetFilenameByHash($hash, $formatPrefix, $filename) {
        $generatedFilename = $this->getNamesAndPathsMock()->getFilenameByHash($hash, $formatPrefix);
        $this->assertEquals($filename, $generatedFilename);
    }


    /**
     *
     * @return type
     */
    public function providerGetFilenameByHash() {
        return array(
            array('1234', '12', '12_1234'),
            array('abcd', 'ab', 'ab_abcd'),
            array('qwer', '', '_qwer'),
        );
    }



    /**
     *
     */
    public function testGetHashForId() {
        $hash = $this->getNamesAndPaths()->getHashForId(10);
        $this->assertEquals(sha1(10), $hash);
    }

        /**

     * @param type $hash
     * @param type $path
     */
    public function testGetFullPathWFilename() {
        $pathWFilename = $this->getNamesAndPathsMock()->getFullPathWFilename(1, 'jpg', 'prefix');
        $this->assertEquals('base/prefix/12/34/prefix_1234567890.jpg', $pathWFilename);
    }



    /**
     * @dataProvider GetFullPathWFilenameForOriginal
     *
     */
    public function testGetFullPathWFilenameForOriginal($illustrationID, $extension, $hash, $result) {
        $pathWFilename = $this->getNamesAndPathsMock()->getFullPathWFilenameForOriginal($illustrationID, $extension, $hash);
        $this->assertEquals($result, $pathWFilename);
    }

    /**
     * @return type
     */
    public function GetFullPathWFilenameForOriginal() {
        return array(
            array('1', 'jpg', null, 'base/original/12/34/1234567890.jpg'),
            array('1', 'png', 'qwerty', 'base/original/qw/er/qwerty.png'),
            
        );
    }
    
    /**
     * @dataProvider providerGetExtensionForFilename
     * @param type $hash
     * @param type $path
     */
    public function testGetExtensionForFilename($filename, $extension) {
        $namesAndPaths = $this->getNamesAndPathsMock();
        $this->assertEquals($namesAndPaths->getExtensionFromFilename($filename), $extension);
    }

    
    
    /**
     * 
     * @return type
     */
    public function providerGetExtensionForFilename() {
        return array(
            array('1234.jpg', 'jpg'),
            array('abcd.png', 'png'),
            array('qwer.jpg.gif', 'gif'),
            array('/a/b/1234.jpg', 'jpg'),
            array('/d/c/abcd.png', 'png'),
            array('/g/h/qwer.jpg.gif', 'gif')
        );
    }
}
