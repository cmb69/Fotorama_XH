<?php

/**
 * Testing the gallery services.
 *
 * PHP version 5
 *
 * @category  CMSimple_XH
 * @package   Testing
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2015 Christoph M. Becker <http://3-magi.net>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link      http://3-magi.net/?CMSimple_XH/Fotorama_XH
 */

require_once './vendor/autoload.php';

use org\bovigo\vfs\vfsStreamWrapper;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStream;
use Fotorama\GalleryService;

/**
 * Testing the gallery services.
 *
 * @category CMSimple_XH
 * @package  Testing
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Fotorama_XH
 */
class GalleryServiceTest extends PHPUnit_Framework_TestCase
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
        
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('content'));
        mkdir(vfsStream::url('content/fotorama'));
        file_put_contents(vfsStream::url('content/fotorama/foo.xml'), self::FOO_XML);
        touch(vfsStream::url('content/fotorama/bar.xml'));
        $pth = array('folder' => array('content' => vfsStream::url('content/')));
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
}

?>
