<?php

namespace IllustrationManager\Test;

use IllustrationManager\Exception\UndefinedFormatException;
use IllustrationManager\Format\Format;
use IllustrationManager\Manager;
use IllustrationManager\NamesAndPaths;
use Imagine\Gd\Imagine;

class ManagerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $fileSystemMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $imagine;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $illustrationManagerConfigMock;

    /**
     *
     */
    protected function setUp()
    {
        $fileSystemAdapterMock = $this->getMock('Gaufrette\\Adapter\\Local', array(), array($_SERVER["DOCUMENT_ROOT"]));
        $this->fileSystemMock = $this->getMock('Gaufrette\\Filesystem', array(), array($fileSystemAdapterMock));
        $this->imagine = new Imagine();
    }


    /**
     *
     */
    protected function getFormatsCollectionMock()
    {

        $formatW900Mock = $this->getMock('IllustrationManager\\Format\\Format');
        $formatW900Mock->expects($this->any())->method('getHash')->withAnyParameters()->will($this->returnValue('abcde'));

        $formatsCollectionMock = $this->getMock('IllustrationManager\\FormatsCollection');

        $callback = function ($formatName) use ($formatW900Mock) {
            $return = null;
            switch ($formatName) {
                case 'W900':
                    $return = $formatW900Mock;
                    break;

                case 'W1200':
                    throw new UndefinedFormatException();
                    break;
                default:
                    break;
            }
            return $return;
        };

        $formatsCollectionMock->expects($this->any())->method('getFormat')->willReturnCallback($callback); //with($this->equalTo('W900'))->will($this->returnValue($formatW900Mock));
        //$formatsCollectionMock->expects($this->any())->method('getFormat')->with($this->equalTo('W1200'))->will($this->throwException(new UndefinedFormatException()));

        return $formatsCollectionMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getIllustrationManagerConfigMock($paramsArray = array())
    {
        $illustrationManagerConfigMock = $this->getMock(
            'IllustrationManager\\IllustrationManagerConfig', array_merge(array('getBaseFolderName', 'getFolderNameForOriginals'), $paramsArray)
        );
        $illustrationManagerConfigMock->expects($this->any())->method('getBaseFolderName')->withAnyParameters()->will($this->returnValue('base'));
        $illustrationManagerConfigMock->expects($this->any())->method('getFolderNameForOriginals')->withAnyParameters()->will($this->returnValue('base' . DIRECTORY_SEPARATOR . 'original'));
        return $illustrationManagerConfigMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getNamesAndPathsMock()
    {
        $namesAndPathsMock = $this->getMock(
            'IllustrationManager\\NamesAndPaths', array('getHashForId'), array($this->getIllustrationManagerConfigMock())
        );

        $namesAndPathsMock->expects($this->any())->method('getHashForId')->with($this->anything())->will($this->returnValue('1234567890'));

        return $namesAndPathsMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getTransformingImageMock()
    {
        $transformingImageMock = $this->getMock('IllustrationManager\\TransformingImage', array(), array($this->imagine, $this->fileSystemMock));
        return $transformingImageMock;
    }


    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getImageGeneratorMock(array $methods = array())
    {
        $imageGeneratorMock = $this->getMock(
            'IllustrationManager\\ImageGenerator',
            $methods,
            array(
                $this->getIllustrationManagerConfigMock(),
                $this->getFormatsCollectionMock(),
                $this->getNamesAndPathsMock(),
                $this->getTransformingImageMock(),
            ));
        //$imageGeneratorMock->method('handleUpload')->with($this->anything())->will($this->returnValue('pathToOriginal'));
        //$imageGeneratorMock->expects($this->any())->method('handleUpload')->with('pathToUploaded', 10);
        return $imageGeneratorMock;
    }

    /**
     * @param $imageGeneratorMock
     * @return Manager
     */
    protected function getManager($imageGeneratorMock, $useCache = false, $getCache = false, $setCache = false)
    {

        $illustrationManagerConfigMock = $this->getIllustrationManagerConfigMock(array('isUseCache'));
        $predisMock = null;

        if ($useCache) {
            $illustrationManagerConfigMock->expects($this->any())->method('isUseCache')->withAnyParameters()->will($this->returnValue(true));
            $predisMock = $this->getMock('\\Predis\\Client', array('get', 'set'));
        }
        if ($useCache && $getCache) {
            $predisMock->expects($this->any())->method('get')->withAnyParameters()->will($this->returnArgument(0));
        }
        if ($useCache && $setCache) {
            $predisMock->expects($this->atLeastOnce())->method('set');
        }

        /* --- */
        $manager = new Manager(
            $illustrationManagerConfigMock,
            $this->getNamesAndPathsMock(),
            $this->getFormatsCollectionMock(),
            $imageGeneratorMock,
            $this->fileSystemMock,
            $predisMock
        );
        return $manager;
    }


    /**
     * @dataProvider providerHandleUpload
     */
    public function testHandleUpload($pathToUpload, $illustrationId)
    {
        $imageGeneratorMock = $this->getImageGeneratorMock();
        $imageGeneratorMock->method('handleUpload')->with($this->anything())->will($this->returnValue('pathToOriginal'));
        $imageGeneratorMock->expects($this->once())->method('handleUpload')->with($pathToUpload, $illustrationId);
        $manager = $this->getManager($imageGeneratorMock);
        $this->assertEquals('pathToOriginal', $manager->handleUpload($pathToUpload, $illustrationId));
    }

    /**
     *
     * @return array
     */
    public function providerHandleUpload()
    {
        return array(
            array('1234', 5),
            array('abcd', 10),
            array('qwer', 15),
        );
    }


    /**
     *
     */
    public function testGetThumbFormatException()
    {
        $this->setExpectedException('IllustrationManager\\Exception\\UndefinedFormatException');
        $imageGeneratorMock = $this->getImageGeneratorMock();
        $manager = $this->getManager($imageGeneratorMock);
        $this->assertNotEmpty($manager->getThumb(10, 'jpg', 'W1200'));

    }


    /**
     *
     */
    public function testGetThumbCache()
    {
        $imageGeneratorMock = $this->getImageGeneratorMock();
        $manager = $this->getManager($imageGeneratorMock, true, true);
        $this->assertEquals('10abcde', $manager->getThumb(10, 'jpg', 'W900'));
    }


    /**
     *
     */
    public function testGetThumbFileExist()
    {
        $this->fileSystemMock->expects($this->any())->method('has')->withAnyParameters()->will($this->returnValue(true));
        $imageGeneratorMock = $this->getImageGeneratorMock();
        $manager = $this->getManager($imageGeneratorMock, true, false, true);
        $this->assertEquals('base' . DIRECTORY_SEPARATOR . 'abcde' . DIRECTORY_SEPARATOR . '12' . DIRECTORY_SEPARATOR . '34' . DIRECTORY_SEPARATOR . 'abcde_1234567890.jpg', $manager->getThumb(10, 'jpg', 'W900'));
    }

    /**
     *
     */
    public function testGetThumbFileNotExist()
    {
        $this->fileSystemMock->expects($this->any())->method('has')->withAnyParameters()->will($this->returnValue(false));
        $imageGeneratorMock = $this->getImageGeneratorMock(array('makeThumbByID'));
        $imageGeneratorMock
            ->expects($this->any())
            ->method('makeThumbByID')
            ->with(
                10,
                'jpg',
                $this->isInstanceOf('\\IllustrationManager\\Format\\Format',
                'base' . DIRECTORY_SEPARATOR . 'abcde' . DIRECTORY_SEPARATOR . '12' . DIRECTORY_SEPARATOR . '34' . DIRECTORY_SEPARATOR . 'abcde_1234567890.jpg'
                ));
        $manager = $this->getManager($imageGeneratorMock, true, false, true);
        $this->assertEquals('base' . DIRECTORY_SEPARATOR . 'abcde' . DIRECTORY_SEPARATOR . '12' . DIRECTORY_SEPARATOR . '34' . DIRECTORY_SEPARATOR . 'abcde_1234567890.jpg', $manager->getThumb(10, 'jpg', 'W900'));
    }
}
