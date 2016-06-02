<?php

/*
Copyright 2015-2016 Christoph M. Becker

This file is part of Fotorama_XH.

Fotorama_XH is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Fotorama_XH is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Fotorama_XH.  If not, see <http://www.gnu.org/licenses/>.
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
