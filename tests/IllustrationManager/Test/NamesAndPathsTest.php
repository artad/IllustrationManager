<?php

namespace IllustrationManager\Test;

use IllustrationManager\NamesAndPaths;

class NamesAndPathsTest extends \PHPUnit_Framework_TestCase {


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
        $illustrationManagerConfigMock->method('getFolderNameForOriginals')->withAnyParameters()->will($this->returnValue('base'.DIRECTORY_SEPARATOR.'original'));


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
        $namesAndPaths = $this->getNamesAndPathsMock();
        $this->assertEquals($path, $namesAndPaths->devideHashIntoPath($hash));
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
            array( 1000,  '10' . DIRECTORY_SEPARATOR . '00'),
        );
    }

    /**
     * @dataProvider providerHashDevideException
     * @param string $hash
     * @param string $path
     */
    public function testHashDevideException($hash, $path) {
        $this->setExpectedException('InvalidArgumentException');
        $namesAndPaths = $this->getNamesAndPathsMock();
        $this->assertEquals($path, $namesAndPaths->devideHashIntoPath($hash));
    }


    /**
     *
     * @return type
     */
    public function providerHashDevideException() {
        return array(
            array( 100,   '10' . DIRECTORY_SEPARATOR . '0'),
            array( 1,   '10' . DIRECTORY_SEPARATOR . '0'),
        );
    }

    /**
     *
     */
    public function testGetPathWFilenameById() {
        $pathWFilename = $this->getNamesAndPathsMock()->getPathWFilenameById(1, 'jpg', 'prefix');
        $this->assertEquals('12'.DIRECTORY_SEPARATOR.'34'.DIRECTORY_SEPARATOR.'prefix_1234567890.jpg', $pathWFilename);
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
        $this->assertEquals('base'.DIRECTORY_SEPARATOR.'prefix'.DIRECTORY_SEPARATOR.'12'.DIRECTORY_SEPARATOR.'34'.DIRECTORY_SEPARATOR.'prefix_1234567890.jpg', $pathWFilename);
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
            array('1', 'jpg', null, 'base'.DIRECTORY_SEPARATOR.'original'.DIRECTORY_SEPARATOR.'12'.DIRECTORY_SEPARATOR.'34'.DIRECTORY_SEPARATOR.'1234567890.jpg'),
            array('1', 'png', 'qwerty', 'base'.DIRECTORY_SEPARATOR.'original'.DIRECTORY_SEPARATOR.'qw'.DIRECTORY_SEPARATOR.'er'.DIRECTORY_SEPARATOR.'qwerty.png'),
            
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
            array(''.DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b'.DIRECTORY_SEPARATOR.'1234.jpg', 'jpg'),
            array(''.DIRECTORY_SEPARATOR.'d'.DIRECTORY_SEPARATOR.'c'.DIRECTORY_SEPARATOR.'abcd.png', 'png'),
            array(''.DIRECTORY_SEPARATOR.'g'.DIRECTORY_SEPARATOR.'h'.DIRECTORY_SEPARATOR.'qwer.jpg.gif', 'gif')
        );
    }
}
