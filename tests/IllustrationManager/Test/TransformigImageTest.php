<?php

namespace IllustrationManager\Test;

use IllustrationManager\TransformingImage;
use Imagine\Image\Box;
use Imagine\Image\Color;
use Imagine\Image\Point;


class TransformingImageTest extends \PHPUnit_Framework_TestCase
{

    protected function getImagineMock(array $methods = array())
    {
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
    protected function getTransformingImage($imagine)
    {
        return new TransformingImage($imagine);
    }


    protected function getFormatConfigMock(
        $doCrop = false,
        $doCropFirst = false,
        $doResize = false,
        $doEnglareToFormat = false,
        $resizeWidth = null,
        $resizeHeight = null,
        $doRotate = false,
        $rotateAngle = 15,
        $rotateBg = '#FFFFFF',
        $doFlip = false,
        $doFlipH = false,
        $doFlipV = false
    )
    {
        $formatConfigMock = $this->getMock('IllustrationManager\\Format\\Format');


        $formatConfigMock->expects($this->any())->method('doCrop')->withAnyParameters()->will($this->returnValue($doCrop));
        $formatConfigMock->expects($this->any())->method('doResize')->withAnyParameters()->will($this->returnValue($doResize));
        $formatConfigMock->expects($this->any())->method('doRotate')->withAnyParameters()->will($this->returnValue($doRotate));
        $formatConfigMock->expects($this->any())->method('getRotateAngle')->withAnyParameters()->will($this->returnValue($rotateAngle));
        $formatConfigMock->expects($this->any())->method('getRotateBackground')->withAnyParameters()->will($this->returnValue($rotateBg));

        $formatConfigMock->expects($this->any())->method('doFlip')->withAnyParameters()->will($this->returnValue($doFlip));
        $formatConfigMock->expects($this->any())->method('doFlipHorizontal')->withAnyParameters()->will($this->returnValue($doFlipH));
        $formatConfigMock->expects($this->any())->method('doFlipVertical')->withAnyParameters()->will($this->returnValue($doFlipV));


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
    public function testDoCrop()
    {

        $imagineMock = $this->getImagineMock();

        $imageMock = $this->getMockForAbstractClass('Imagine\\Image\\ImageInterface', array(), '', true, true, true, array('crop', 'getSize', 'resize'));

        $formatConfigMock = $this->getFormatConfigMock(true, false, true, false, 100, 100);


        $boxMock = $this->getMockForAbstractClass('Imagine\\Image\\BoxInterface', array(), '', true, true, true, array('getWidth', 'getHeight', 'widen'));
        $boxMock->expects($this->any())->method('getWidth')->withAnyParameters()->will($this->returnValue(200));
        $boxMock->expects($this->any())->method('getHeight')->withAnyParameters()->will($this->returnValue(200));

        $imageMock->expects($this->at(2))->method('resize');
        $imageMock->expects($this->at(3))->method('crop')->with($this->equalTo(new Point(10, 10)), $this->equalTo(new Box(20, 20)))->will($this->returnValue(true));
        $imageMock->expects($this->any())->method('getSize')->withAnyParameters()->will($this->returnValue($boxMock));

        $transformingImage = $this->getTransformingImage($imagineMock);

        $reflection = new \ReflectionMethod($transformingImage, 'transformImage');
        $reflection->setAccessible(true);
        $reflection->invoke($transformingImage, $imageMock, $formatConfigMock);
    }


    /**
     *
     */
    public function testDoCropFirst()
    {

        $imagineMock = $this->getImagineMock();

        $imageMock = $this->getMockForAbstractClass('Imagine\\Image\\ImageInterface', array(), '', true, true, true, array('crop', 'getSize', 'resize'));

        $formatConfigMock = $this->getFormatConfigMock(true, true, true, false, 100, 100);


        $boxMock = $this->getMockForAbstractClass('Imagine\\Image\\BoxInterface', array(), '', true, true, true, array('getWidth', 'getHeight', 'widen'));
        $boxMock->expects($this->any())->method('getWidth')->withAnyParameters()->will($this->returnValue(200));
        $boxMock->expects($this->any())->method('getHeight')->withAnyParameters()->will($this->returnValue(200));

        $imageMock->expects($this->at(0))->method('crop')->with($this->equalTo(new Point(10, 10)), $this->equalTo(new Box(20, 20)))->will($this->returnValue(true));
        $imageMock->expects($this->at(3))->method('resize');
        $imageMock->expects($this->any())->method('getSize')->withAnyParameters()->will($this->returnValue($boxMock));

        $transformingImage = $this->getTransformingImage($imagineMock);

        $reflection = new \ReflectionMethod($transformingImage, 'transformImage');
        $reflection->setAccessible(true);
        $reflection->invoke($transformingImage, $imageMock, $formatConfigMock);
    }



    /**
     *
     */
    public function testDoRotate() {
        $imagineMock = $this->getImagineMock();

        $imageMock = $this->getMockForAbstractClass('Imagine\\Image\\ImageInterface', array(), '', true, true, true, array('rotate'));
        $imageMock->expects($this->once())->method('rotate')->with(15, $this->equalTo(new Color('#FFFFFF')))->will($this->returnValue(true));

        $formatConfigMock = $this->getFormatConfigMock(true, true, false, false, null, null ,true, 15, '#FFFFFF');

        $this->assertEquals(true, $formatConfigMock->doRotate());

        $transformingImage = $this->getTransformingImage($imagineMock);

        $reflection = new \ReflectionMethod($transformingImage, 'transformImage');
        $reflection->setAccessible(true);
        $reflection->invoke($transformingImage, $imageMock, $formatConfigMock);
    }

    /**
     *
     */
    public function testDoFlip() {
        $imagineMock = $this->getImagineMock();

        $imageMock = $this->getMockForAbstractClass('Imagine\\Image\\ImageInterface', array(), '', true, true, true, array('rotate'));

        $formatConfigMock = $this->getFormatConfigMock(true, true, false, false, null, null ,true, 15, '#FFFFFF', true, true, true);

        $imageMock->expects($this->once())->method('flipHorizontally');
        $imageMock->expects($this->once())->method('flipVertically');

        $transformingImage = $this->getTransformingImage($imagineMock);

        $reflection = new \ReflectionMethod($transformingImage, 'transformImage');
        $reflection->setAccessible(true);
        $reflection->invoke($transformingImage, $imageMock, $formatConfigMock);
    }

    /**
     *
     */
    public function testDoFlipH() {
        $imagineMock = $this->getImagineMock();

        $imageMock = $this->getMockForAbstractClass('Imagine\\Image\\ImageInterface', array(), '', true, true, true, array('rotate'));

        $formatConfigMock = $this->getFormatConfigMock(true, true, false, false, null, null ,true, 15, '#FFFFFF', true, true);

        $imageMock->expects($this->once())->method('flipHorizontally');
        $imageMock->expects($this->never())->method('flipVertically');

        $transformingImage = $this->getTransformingImage($imagineMock);

        $reflection = new \ReflectionMethod($transformingImage, 'transformImage');
        $reflection->setAccessible(true);
        $reflection->invoke($transformingImage, $imageMock, $formatConfigMock);
    }


    /**
     *
     */
    public function testDoFlipV() {
        $imagineMock = $this->getImagineMock();
        $imageMock = $this->getMockForAbstractClass('Imagine\\Image\\ImageInterface', array(), '', true, true, true, array('rotate'));
        $formatConfigMock = $this->getFormatConfigMock(true, true, false, false, null, null ,true, 15, '#FFFFFF', true, false, true);
        $imageMock->expects($this->never())->method('flipHorizontally');
        $imageMock->expects($this->once())->method('flipVertically');
        $transformingImage = $this->getTransformingImage($imagineMock);
        $reflection = new \ReflectionMethod($transformingImage, 'transformImage');
        $reflection->setAccessible(true);
        $reflection->invoke($transformingImage, $imageMock, $formatConfigMock);
    }



    /**
     * @dataProvider providerTestResizeAll
     */
    public function testResize($imageWidth, $imageHeight, $configWidth, $configHeight, $doEnglare = false)
    {
        $imagineMock = $this->getImagineMock();
        $boxMock = $this->getMockForAbstractClass('Imagine\\Image\\BoxInterface', array(), '', true, true, true, array('getWidth', 'getHeight', 'widen'));
        $boxMock->expects($this->any())->method('getWidth')->withAnyParameters()->will($this->returnValue($imageWidth));
        $boxMock->expects($this->any())->method('getHeight')->withAnyParameters()->will($this->returnValue($imageHeight));

        $imageMock = $this->getMockForAbstractClass('Imagine\\Image\\ImageInterface', array(), '', true, true, true, array('getSize', 'resize'));

        //Width&Heght
        if ($configWidth && $configHeight && $imageWidth > $configWidth) {

            $boxMock->expects($this->never())->method('widen');
            $boxMock->expects($this->never())->method('heighten');
            $imageMock->expects($this->once())->method('resize')->with($this->attributeEqualTo('width', $configWidth));

        } elseif ($configWidth && $configHeight && $imageHeight > $configHeight) {

            $boxMock->expects($this->never())->method('widen');
            $boxMock->expects($this->never())->method('heighten');
            $imageMock->expects($this->once())->method('resize')->with($this->attributeEqualTo('height', $configHeight))->will($this->returnValue(true));

        } elseif ($configWidth && $configHeight && ($imageWidth <= $configWidth && $imageHeight <= $configHeight) && !$doEnglare) {

            $boxMock->expects($this->never())->method('widen');
            $boxMock->expects($this->never())->method('heighten');
            $imageMock->expects($this->never())->method('resize');

        } elseif ($configWidth && $configHeight && ($imageWidth <= $configWidth) && $doEnglare) {

            $boxMock->expects($this->never())->method('widen');
            $boxMock->expects($this->never())->method('heighten');
            $imageMock->expects($this->once())->method('resize')->with($this->attributeEqualTo('width', $configWidth))->will($this->returnValue(true));

        } elseif ($configWidth && $configHeight && ($imageHeight <= $configHeight) && $doEnglare) {

            $boxMock->expects($this->never())->method('widen');
            $boxMock->expects($this->never())->method('heighten');
            $imageMock->expects($this->once())->method('resize')->with($this->attributeEqualTo('height', $configHeight))->will($this->returnValue(true));

        } elseif ($configWidth && !$configHeight && $configWidth < $imageWidth) {

            $boxMock_2 = $this->getMockForAbstractClass('Imagine\\Image\\BoxInterface');
            $boxMock_2->expects($this->any())->method('getWidth');
            $boxMock_2->expects($this->any())->method('getHeight');

            $boxMock->expects($this->once())->method('widen')->withAnyParameters()->will($this->returnValue($boxMock_2));
            $boxMock->expects($this->never())->method('heighten')->withAnyParameters()->will($this->returnValue($boxMock_2));

            $imageMock->expects($this->any())->method('getSize')->withAnyParameters()->will($this->returnValue($boxMock));
            $imageMock->expects($this->once())->method('resize')->with($this->isInstanceOf('Imagine\\Image\\BoxInterface'))->will($this->returnValue(true));

        } elseif ($configWidth && !$configHeight && $configWidth > $imageWidth && !$doEnglare) {

            $boxMock_2 = $this->getMockForAbstractClass('Imagine\\Image\\BoxInterface');
            $boxMock_2->expects($this->any())->method('getWidth');
            $boxMock_2->expects($this->any())->method('getHeight');

            $boxMock->expects($this->never())->method('widen');
            $boxMock->expects($this->never())->method('heighten');

            $imageMock->expects($this->any())->method('getSize')->withAnyParameters()->will($this->returnValue($boxMock));
            $imageMock->expects($this->never())->method('resize');

        } elseif ($configWidth && !$configHeight && $configWidth > $imageWidth && $doEnglare) {

            $boxMock_2 = $this->getMockForAbstractClass('Imagine\\Image\\BoxInterface');
            $boxMock_2->expects($this->any())->method('getWidth');
            $boxMock_2->expects($this->any())->method('getHeight');

            $boxMock->expects($this->once())->method('widen')->with($configWidth)->will($this->returnValue($boxMock_2));
            $boxMock->expects($this->never())->method('heighten');

            $imageMock->expects($this->any())->method('getSize')->withAnyParameters()->will($this->returnValue($boxMock));
            $imageMock->expects($this->once())->method('resize')->with($this->isInstanceOf('Imagine\\Image\\BoxInterface'))->will($this->returnValue(true));

        } elseif (!$configWidth && $configHeight && $configHeight < $imageHeight) {

            $boxMock_2 = $this->getMockForAbstractClass('Imagine\\Image\\BoxInterface');
            $boxMock_2->expects($this->any())->method('getWidth');
            $boxMock_2->expects($this->any())->method('getHeight');

            $boxMock->expects($this->never())->method('widen');
            $boxMock->expects($this->once())->method('heighten')->with($configHeight)->will($this->returnValue($boxMock_2));

            $imageMock->expects($this->any())->method('getSize')->withAnyParameters()->will($this->returnValue($boxMock));
            $imageMock->expects($this->once())->method('resize')->with($this->isInstanceOf('Imagine\\Image\\BoxInterface'))->will($this->returnValue(true));

        } elseif (!$configWidth && $configHeight && $configHeight > $imageHeight && !$doEnglare) {

            $boxMock_2 = $this->getMockForAbstractClass('Imagine\\Image\\BoxInterface');
            $boxMock_2->expects($this->any())->method('getWidth');
            $boxMock_2->expects($this->any())->method('getHeight');

            $boxMock->expects($this->never())->method('widen');
            $boxMock->expects($this->never())->method('heighten');

            $imageMock->expects($this->any())->method('getSize')->withAnyParameters()->will($this->returnValue($boxMock));
            $imageMock->expects($this->never())->method('resize');

        } elseif (!$configWidth && $configHeight && $configHeight > $imageHeight && $doEnglare) {

            $boxMock_2 = $this->getMockForAbstractClass('Imagine\\Image\\BoxInterface');
            $boxMock_2->expects($this->any())->method('getWidth');
            $boxMock_2->expects($this->any())->method('getHeight');

            $boxMock->expects($this->never())->method('widen');
            $boxMock->expects($this->once())->method('heighten')->with($configHeight)->will($this->returnValue($boxMock_2));

            $imageMock->expects($this->any())->method('getSize')->withAnyParameters()->will($this->returnValue($boxMock));
            $imageMock->expects($this->once())->method('resize')->with($this->isInstanceOf('Imagine\\Image\\BoxInterface'))->will($this->returnValue(true));
        }


        $imageMock->expects($this->any())->method('getSize')->withAnyParameters()->will($this->returnValue($boxMock));

        $formatConfigMock = $this->getFormatConfigMock(false, false, true, $doEnglare, $configWidth, $configHeight);
        $transformingImage = $this->getTransformingImage($imagineMock);

        $reflection = new \ReflectionMethod($transformingImage, 'transformImage');
        $reflection->setAccessible(true);
        $reflection->invoke($transformingImage, $imageMock, $formatConfigMock);
    }


    public function providerTestResizeAll()
    {
        return array(
            array(400, 400, 200, 200, false),
            array(400, 400, 500, 500, false),
            array(400, 400, 500, 500, true),
            array(400, 400, 200, null, false),
            array(400, 400, 500, null, false),
            array(400, 400, 500, null, true),
            array(400, 400, null, 200, false),
            array(400, 400, null, 500, false),
            array(400, 400, null, 500, true),
            array(400, 400, 200, 500, false),
            array(400, 400, 500, 200, false),
        );
    }

}
