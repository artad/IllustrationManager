<?php

namespace IllustrationManager\Test;

use IllustrationManager\TransformingImage;
use Imagine\Image\Box;
use Imagine\Image\Point;


class TransformingImageTest extends \PHPUnit_Framework_TestCase
{

    protected function getFilesystemMock(array $methods = array()) {
        $localAdapter = $this->getMock('Gaufrette\\Adapter\\Local', array(), array($_SERVER["DOCUMENT_ROOT"]));
        return $this->getMock('Gaufrette\\Filesystem', array(), array($localAdapter));
    }


    protected function getImagineMock(array $methods = array()) {
        return $this->getMockForAbstractClass('Imagine\\Image\\ImagineInterface', $methods);
    }


    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getTransformingImageMock(array $methods = array(), $imagine, $fileSystem)
    {
        $transformingImageMock = $this->getMock('IllustrationManager\\TransformingImage', $methods, array($imagine, $fileSystem));
        return $transformingImageMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getTransformingImage($imagine, $fileSystem)
    {
        return new TransformingImage($imagine, $fileSystem);
    }


    protected function getFormatConfigMock(
        $doCrop = false,
        $doCropFirst = false,
        $doResize = false,
        $doEnglareToFormat = false,
        $resizeWidth = null,
        $resizeHeight = null
    ) {
        $formatConfigMock = $this->getMock('IllustrationManager\\Format\\Format');


        $formatConfigMock->expects($this->any())->method('doCrop')->withAnyParameters()->will($this->returnValue($doCrop));
        $formatConfigMock->expects($this->any())->method('doResize')->withAnyParameters()->will($this->returnValue($doResize));

        $formatConfigMock->expects($this->any())->method('doCropFirst')->withAnyParameters()->will($this->returnValue($doCropFirst));
        $formatConfigMock->expects($this->any())->method('getCropStartPointX')->withAnyParameters()->will($this->returnValue(10));
        $formatConfigMock->expects($this->any())->method('getCropStartPointY')->withAnyParameters()->will($this->returnValue(10));
        $formatConfigMock->expects($this->any())->method('getCropWidth')->withAnyParameters()->will($this->returnValue(20));
        $formatConfigMock->expects($this->any())->method('getCropHeight')->withAnyParameters()->will($this->returnValue(20));

        $formatConfigMock->expects($this->any())->method('getResizeWidth')->withAnyParameters()->will($this->returnValue($resizeWidth));
        $formatConfigMock->expects($this->any())->method('getResizeHeight')->withAnyParameters()->will($this->returnValue($resizeHeight));

        $formatConfigMock->expects($this->any())->method('doEnglareToFormat')->withAnyParameters()->will($this->returnValue($doEnglareToFormat));
        return $formatConfigMock;
    }


    /**
     *
     */
    public function testTransform()
    {
        $pathWFilename = 'abcd';
        $savePathWFilename = 'qwer';
        $fileSystemMock = $this->getFilesystemMock(array('get'));
        $fileMock = $this->getMock('Gaufrette\\File', array(), array(), '', false);
        $fileSystemMock->expects($this->once())->method('get')->with($pathWFilename)->will($this->returnValue($fileMock));
        $imageMock = $this->getMockBuilder('Imagine\\Image\\ImageInterface')->disableOriginalConstructor()->getMock();
        $imagineMock = $this->getImagineMock(array('load'));
        $imagineMock->expects($this->once())->method('load')->withAnyParameters()->will($this->returnValue($imageMock));

        $transformingImage = $this->getTransformingImage($imagineMock, $fileSystemMock);

        $formatConfigMock = $this->getMock('IllustrationManager\\Format\\Format');
        $transformingImage->transform($pathWFilename, $savePathWFilename, 'jpg', $formatConfigMock);
    }

    /**
     *
     */
    public function testDoCropDoCropFirst() {

        $fileSystemMock = $this->getFilesystemMock();
        $imagineMock = $this->getImagineMock();

        $imageMock = $this->getMockForAbstractClass('Imagine\\Image\\ImageInterface', array(), '', true, true, true, array('crop'));
        $imageMock->expects($this->once())->method('crop')->with($this->equalTo(new Point(10,10)), $this->equalTo(new Box(20,20)))->will($this->returnValue(true));

        $formatConfigMock = $this->getFormatConfigMock(true, true);

        $this->assertEquals(true, $formatConfigMock->doCrop());
        $this->assertEquals(true, $formatConfigMock->doCropFirst());


        $transformingImage = $this->getTransformingImage($imagineMock, $fileSystemMock);

        $reflection = new \ReflectionMethod($transformingImage, 'transformImage');
        $reflection->setAccessible(true);
        $reflection->invoke($transformingImage, $imageMock, $formatConfigMock);
    }

    /**
     *
     */
    public function testResizeNoCall() {

        $fileSystemMock = $this->getFilesystemMock();
        $imagineMock = $this->getImagineMock();

        $boxMock = $this->getMockForAbstractClass('Imagine\\Image\\BoxInterface', array(), '', true, true, true, array('getWidth','getHeight'));

        $boxMock->expects($this->any())->method('getWidth')->withAnyParameters()->will($this->returnValue(100));
        $boxMock->expects($this->any())->method('getHeight')->withAnyParameters()->will($this->returnValue(100));

        $imageMock = $this->getMockForAbstractClass('Imagine\\Image\\ImageInterface', array(), '', true, true, true, array('getSize','resize'));

        $imageMock->expects($this->any())->method('getSize')->withAnyParameters()->will($this->returnValue($boxMock));
        $imageMock->expects($this->never())->method('resize')->with($this->equalTo(new Point(10,10)), $this->equalTo(new Box(20,20)))->will($this->returnValue(true));

        $formatConfigMock = $this->getFormatConfigMock(false, false, true, false, 200, 200);

        $transformingImage = $this->getTransformingImage($imagineMock, $fileSystemMock);

        $reflection = new \ReflectionMethod($transformingImage, 'transformImage');
        $reflection->setAccessible(true);
        $reflection->invoke($transformingImage, $imageMock, $formatConfigMock);

    }


    /**
     *
     */
    public function testResizeDoEnglare() {

        $fileSystemMock = $this->getFilesystemMock();
        $imagineMock = $this->getImagineMock();

        $boxMock = $this->getMockForAbstractClass('Imagine\\Image\\BoxInterface', array(), '', true, true, true, array('getWidth','getHeight'));
        $boxMock->expects($this->any())->method('getWidth')->withAnyParameters()->will($this->returnValue(100));
        $boxMock->expects($this->any())->method('getHeight')->withAnyParameters()->will($this->returnValue(100));

        $imageMock = $this->getMockForAbstractClass('Imagine\\Image\\ImageInterface', array(), '', true, true, true, array('getSize','resize'));

        $imageMock->expects($this->any())->method('getSize')->withAnyParameters()->will($this->returnValue($boxMock));
        $imageMock->expects($this->once())->method('resize')->with($this->logicalAnd($this->attributeEqualTo('width', 200), $this->attributeEqualTo('height', 200)))->will($this->returnValue(true));

        $formatConfigMock = $this->getFormatConfigMock(false, false, true, true, 200, 200);

        $transformingImage = $this->getTransformingImage($imagineMock, $fileSystemMock);

        $reflection = new \ReflectionMethod($transformingImage, 'transformImage');
        $reflection->setAccessible(true);
        $reflection->invoke($transformingImage, $imageMock, $formatConfigMock);

    }



    /**
     *
     */
    public function testResize() {

        $fileSystemMock = $this->getFilesystemMock();
        $imagineMock = $this->getImagineMock();

        $boxMock = $this->getMockForAbstractClass('Imagine\\Image\\BoxInterface', array(), '', true, true, true, array('getWidth','getHeight'));
        $boxMock->expects($this->any())->method('getWidth')->withAnyParameters()->will($this->returnValue(400));
        $boxMock->expects($this->any())->method('getHeight')->withAnyParameters()->will($this->returnValue(400));

        $imageMock = $this->getMockForAbstractClass('Imagine\\Image\\ImageInterface', array(), '', true, true, true, array('getSize','resize'));

        $imageMock->expects($this->any())->method('getSize')->withAnyParameters()->will($this->returnValue($boxMock));
        $imageMock->expects($this->once())->method('resize')->with($this->logicalAnd($this->attributeEqualTo('width', 200), $this->attributeEqualTo('height', 200)))->will($this->returnValue(true));

        $formatConfigMock = $this->getFormatConfigMock(false, false, true, true, 200, 200);

        $transformingImage = $this->getTransformingImage($imagineMock, $fileSystemMock);

        $reflection = new \ReflectionMethod($transformingImage, 'transformImage');
        $reflection->setAccessible(true);
        $reflection->invoke($transformingImage, $imageMock, $formatConfigMock);

    }


    /**
     *
     */
    public function testResizeOnlyWidth() {

        $fileSystemMock = $this->getFilesystemMock();
        $imagineMock = $this->getImagineMock();

        $boxMock_2 = $this->getMockForAbstractClass('Imagine\\Image\\BoxInterface');
        $boxMock_2->expects($this->any())->method('getWidth');
        $boxMock_2->expects($this->any())->method('getHeight');

        $boxMock = $this->getMockForAbstractClass('Imagine\\Image\\BoxInterface', array(), '', true, true, true, array('getWidth','getHeight', 'widen'));
        $boxMock->expects($this->any())->method('getWidth')->withAnyParameters()->will($this->returnValue(400));
        $boxMock->expects($this->any())->method('getHeight')->withAnyParameters()->will($this->returnValue(400));

        $boxMock->expects($this->once())->method('widen')->withAnyParameters()->will($this->returnValue($boxMock_2));
        $boxMock->expects($this->never())->method('heighten')->withAnyParameters()->will($this->returnValue($boxMock_2));

        $imageMock = $this->getMockForAbstractClass('Imagine\\Image\\ImageInterface', array(), '', true, true, true, array('getSize','resize'));

        $imageMock->expects($this->any())->method('getSize')->withAnyParameters()->will($this->returnValue($boxMock));
        $imageMock->expects($this->once())->method('resize')->with($this->isInstanceOf('Imagine\\Image\\BoxInterface'))->will($this->returnValue(true));

        $formatConfigMock = $this->getFormatConfigMock(false, false, true, false, 300, null);

        $transformingImage = $this->getTransformingImage($imagineMock, $fileSystemMock);

        $reflection = new \ReflectionMethod($transformingImage, 'transformImage');
        $reflection->setAccessible(true);
        $reflection->invoke($transformingImage, $imageMock, $formatConfigMock);

    }


    /**
     *
     */
    public function testResizeOnlyWidthDoEnglare() {

        $fileSystemMock = $this->getFilesystemMock();
        $imagineMock = $this->getImagineMock();

        $boxMock_2 = $this->getMockForAbstractClass('Imagine\\Image\\BoxInterface');
        $boxMock_2->expects($this->any())->method('getWidth');
        $boxMock_2->expects($this->any())->method('getHeight');

        $boxMock = $this->getMockForAbstractClass('Imagine\\Image\\BoxInterface', array(), '', true, true, true, array('getWidth','getHeight', 'widen'));
        $boxMock->expects($this->any())->method('getWidth')->withAnyParameters()->will($this->returnValue(400));
        $boxMock->expects($this->any())->method('getHeight')->withAnyParameters()->will($this->returnValue(400));

        $boxMock->expects($this->once())->method('widen')->with(500)->will($this->returnValue($boxMock_2));
        $boxMock->expects($this->never())->method('heighten')->withAnyParameters()->will($this->returnValue($boxMock_2));

        $imageMock = $this->getMockForAbstractClass('Imagine\\Image\\ImageInterface', array(), '', true, true, true, array('getSize','resize'));

        $imageMock->expects($this->any())->method('getSize')->withAnyParameters()->will($this->returnValue($boxMock));
        $imageMock->expects($this->once())->method('resize')->with($this->isInstanceOf('Imagine\\Image\\BoxInterface'))->will($this->returnValue(true));

        $formatConfigMock = $this->getFormatConfigMock(false, false, true, true, 500, null);

        $transformingImage = $this->getTransformingImage($imagineMock, $fileSystemMock);

        $reflection = new \ReflectionMethod($transformingImage, 'transformImage');
        $reflection->setAccessible(true);
        $reflection->invoke($transformingImage, $imageMock, $formatConfigMock);

    }


    /**
     *
     */
    public function testResizeOnlyWidthNoEnglareNoResizeCall() {

        $fileSystemMock = $this->getFilesystemMock();
        $imagineMock = $this->getImagineMock();

        $boxMock = $this->getMockForAbstractClass('Imagine\\Image\\BoxInterface', array(), '', true, true, true, array('getWidth','getHeight', 'widen'));
        $boxMock->expects($this->any())->method('getWidth')->withAnyParameters()->will($this->returnValue(200));
        $boxMock->expects($this->any())->method('getHeight')->withAnyParameters()->will($this->returnValue(200));

        $boxMock->expects($this->never())->method('widen');
        $boxMock->expects($this->never())->method('heighten');

        $imageMock = $this->getMockForAbstractClass('Imagine\\Image\\ImageInterface', array(), '', true, true, true, array('getSize','resize'));

        $imageMock->expects($this->any())->method('getSize')->withAnyParameters()->will($this->returnValue($boxMock));
        $imageMock->expects($this->never())->method('resize')->with($this->isInstanceOf('Imagine\\Image\\BoxInterface'))->will($this->returnValue(true));

        $formatConfigMock = $this->getFormatConfigMock(false, false, true, false, 300, null);

        $transformingImage = $this->getTransformingImage($imagineMock, $fileSystemMock);

        $reflection = new \ReflectionMethod($transformingImage, 'transformImage');
        $reflection->setAccessible(true);
        $reflection->invoke($transformingImage, $imageMock, $formatConfigMock);

    }



    /**
     *
     */
    public function testResizeOnlyHeight() {

        $fileSystemMock = $this->getFilesystemMock();
        $imagineMock = $this->getImagineMock();

        $boxMock_2 = $this->getMockForAbstractClass('Imagine\\Image\\BoxInterface');

        $boxMock = $this->getMockForAbstractClass('Imagine\\Image\\BoxInterface', array(), '', true, true, true, array('getWidth','getHeight', 'widen'));
        $boxMock->expects($this->any())->method('getWidth')->withAnyParameters()->will($this->returnValue(400));
        $boxMock->expects($this->any())->method('getHeight')->withAnyParameters()->will($this->returnValue(400));

        $boxMock->expects($this->never())->method('widen')->withAnyParameters()->will($this->returnValue($boxMock_2));
        $boxMock->expects($this->once())->method('heighten')->withAnyParameters()->will($this->returnValue($boxMock_2));

        $imageMock = $this->getMockForAbstractClass('Imagine\\Image\\ImageInterface', array(), '', true, true, true, array('getSize','resize'));

        $imageMock->expects($this->any())->method('getSize')->withAnyParameters()->will($this->returnValue($boxMock));
        $imageMock->expects($this->once())->method('resize')->with($this->isInstanceOf('Imagine\\Image\\BoxInterface'))->will($this->returnValue(true));

        $formatConfigMock = $this->getFormatConfigMock(false, false, true, false, null, 300);

        $transformingImage = $this->getTransformingImage($imagineMock, $fileSystemMock);

        $reflection = new \ReflectionMethod($transformingImage, 'transformImage');
        $reflection->setAccessible(true);
        $reflection->invoke($transformingImage, $imageMock, $formatConfigMock);
    }


    /**
     *
     */
    public function testResizeOnlyHeightDoEnglare() {

        $fileSystemMock = $this->getFilesystemMock();
        $imagineMock = $this->getImagineMock();

        $boxMock_2 = $this->getMockForAbstractClass('Imagine\\Image\\BoxInterface');

        $boxMock = $this->getMockForAbstractClass('Imagine\\Image\\BoxInterface', array(), '', true, true, true, array('getWidth','getHeight', 'widen'));
        $boxMock->expects($this->any())->method('getWidth')->withAnyParameters()->will($this->returnValue(400));
        $boxMock->expects($this->any())->method('getHeight')->withAnyParameters()->will($this->returnValue(400));

        $boxMock->expects($this->never())->method('widen')->withAnyParameters()->will($this->returnValue($boxMock_2));
        $boxMock->expects($this->once())->method('heighten')->withAnyParameters()->will($this->returnValue($boxMock_2));

        $imageMock = $this->getMockForAbstractClass('Imagine\\Image\\ImageInterface', array(), '', true, true, true, array('getSize','resize'));

        $imageMock->expects($this->any())->method('getSize')->withAnyParameters()->will($this->returnValue($boxMock));
        $imageMock->expects($this->once())->method('resize')->with($this->isInstanceOf('Imagine\\Image\\BoxInterface'))->will($this->returnValue(true));

        $formatConfigMock = $this->getFormatConfigMock(false, false, true, true, null, 500);

        $transformingImage = $this->getTransformingImage($imagineMock, $fileSystemMock);

        $reflection = new \ReflectionMethod($transformingImage, 'transformImage');
        $reflection->setAccessible(true);
        $reflection->invoke($transformingImage, $imageMock, $formatConfigMock);

    }



    /**
     *
     */
    public function testResizeOnlyHeightNoEnglareNoResizeCall() {

        $fileSystemMock = $this->getFilesystemMock();
        $imagineMock = $this->getImagineMock();

        $boxMock = $this->getMockForAbstractClass('Imagine\\Image\\BoxInterface', array(), '', true, true, true, array('getWidth','getHeight', 'widen'));
        $boxMock->expects($this->any())->method('getWidth')->withAnyParameters()->will($this->returnValue(200));
        $boxMock->expects($this->any())->method('getHeight')->withAnyParameters()->will($this->returnValue(200));

        $boxMock->expects($this->never())->method('widen');
        $boxMock->expects($this->never())->method('heighten');

        $imageMock = $this->getMockForAbstractClass('Imagine\\Image\\ImageInterface', array(), '', true, true, true, array('getSize','resize'));

        $imageMock->expects($this->any())->method('getSize')->withAnyParameters()->will($this->returnValue($boxMock));
        $imageMock->expects($this->never())->method('resize');

        $formatConfigMock = $this->getFormatConfigMock(false, false, true, false, null, 300);
        $transformingImage = $this->getTransformingImage($imagineMock, $fileSystemMock);

        $reflection = new \ReflectionMethod($transformingImage, 'transformImage');
        $reflection->setAccessible(true);
        $reflection->invoke($transformingImage, $imageMock, $formatConfigMock);
    }
}
