<?php

/**
 * Testing the gallery services.
 *
 * PHP version 5
 *
 * @category  CMSimple_XH
 * @package   Testing
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2015-2016 Christoph M. Becker <http://3-magi.net>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link      http://3-magi.net/?CMSimple_XH/Fotorama_XH
 */

namespace Fotorama;

use org\bovigo\vfs\vfsStreamWrapper;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStream;

/**
 * Testing the gallery services.
 *
 * @category CMSimple_XH
 * @package  Testing
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Fotorama_XH
 */
class GalleryServiceTest extends \PHPUnit_Framework_TestCase
{
    const FOO_XML = <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<!DOCTYPE gallery SYSTEM
    "http://3-magi.net/userfiles/downloads/dtd/gallery.dtd">
<gallery/>
XML;

    /**
     * Sets up the test fixture.
     *
     * @return void
     */
    public function setUp()
    {
        global $pth;
        
        $this->root = vfsStream::setup();
        $pth = array('folder' => array(
            'content' => $this->root->url() . '/content/',
            'images' => $this->root->url() . '/images/'
        ));
        mkdir("{$pth['folder']['content']}fotorama", 0777, true);
        file_put_contents("{$pth['folder']['content']}fotorama/foo.xml", self::FOO_XML);
        touch("{$pth['folder']['content']}fotorama/bar.xml");
        mkdir("{$pth['folder']['images']}test", 0777, true);
        $img = imagecreate(100, 100);
        imagejpeg($img, "{$pth['folder']['images']}test/foo.jpg");
        imagejpeg($img, "{$pth['folder']['images']}test/bar.jpg");
    }
    
    /**
     * Tests that all galleries are found.
     *
     * @return void
     */
    public function testAllGalleriesAreFound()
    {
        $service = new GalleryService();
        $this->assertEquals(array('bar', 'foo'), $service->findAllGalleries());
    }
    
    public function testHasGallery()
    {
        $service = new GalleryService();
        $this->assertTrue($service->hasGallery('foo'));
        $this->assertFalse($service->hasGallery('baz'));
    }

    /**
     * Tests that a retrieved gallery is a SimpleXMLElement.
     *
     * @return void
     */
    public function testGalleryIsSimpleXMLElement()
    {
        $service = new GalleryService();
        $this->assertInstanceOf('SimpleXMLElement', $service->findGallery('foo'));
    }

    public function testFindsGalleryXml()
    {
        $service = new GalleryService();
        $this->assertEquals(self::FOO_XML, $service->findGalleryXml('foo'));
    }

    public function testSavesGalleryXml()
    {
        global $pth;

        $service = new GalleryService();
        $service->saveGalleryXML('bar', self::FOO_XML);
        $this->assertFileEquals(
            "{$pth['folder']['content']}fotorama/foo.xml",
            "{$pth['folder']['content']}fotorama/bar.xml"
        );
    }

    public function testFindsAllImageFolders()
    {
        $service = new GalleryService();
        $this->assertEquals(array('test'), $service->findImageFolders());
    }

    public function _testHasImageFolder()
    {
        $service = new GalleryService();
        $this->assertTrue($service->hasImageFolder('test'));
        $this->assertFalse($service->hasImageFolder('foo'));
    }

    public function testFindsAllImages()
    {
        $service = new GalleryService();
        $this->assertEquals(array('bar.jpg', 'foo.jpg'), $service->findImagesIn('test'));
    }

    public function testImageFolderName()
    {
        $service = new GalleryService();
        $this->assertEquals('vfs://root/images/test', $service->getImageFolderName('test'));
    }
}
