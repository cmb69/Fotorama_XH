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

class GalleryServiceTest extends \PHPUnit_Framework_TestCase
{
    const FOO_XML = <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<!DOCTYPE gallery SYSTEM
    "http://3-magi.net/userfiles/downloads/dtd/gallery.dtd">
<gallery/>
XML;

    private $sut;

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
        $this->sut = new GalleryService();
    }
    
    public function testAllGalleriesAreFound()
    {
        $this->assertEquals(array('bar', 'foo'), $this->sut->findAllGalleries());
    }
    
    public function testHasGallery()
    {
        $this->assertTrue($this->sut->hasGallery('foo'));
        $this->assertFalse($this->sut->hasGallery('baz'));
    }

    public function testGalleryIsSimpleXMLElement()
    {
        $this->assertInstanceOf('SimpleXMLElement', $this->sut->findGallery('foo'));
    }

    public function testFindsGalleryXml()
    {
        $this->assertEquals(self::FOO_XML, $this->sut->findGalleryXml('foo'));
    }

    public function testSavesGalleryXml()
    {
        global $pth;

        $this->sut->saveGalleryXML('bar', self::FOO_XML);
        $this->assertFileEquals(
            "{$pth['folder']['content']}fotorama/foo.xml",
            "{$pth['folder']['content']}fotorama/bar.xml"
        );
    }

    public function testFindsAllImageFolders()
    {
        $this->assertEquals(array('test'), $this->sut->findImageFolders());
    }

    public function testHasImageFolder()
    {
        $this->assertTrue($this->sut->hasImageFolder('test'));
        $this->assertFalse($this->sut->hasImageFolder('foo'));
    }

    public function testFindsAllImages()
    {
        $this->assertEquals(array('bar.jpg', 'foo.jpg'), $this->sut->findImagesIn('test'));
    }

    public function testImageFolderName()
    {
        $this->assertEquals('vfs://root/images/test', $this->sut->getImageFolderName('test'));
    }
}
