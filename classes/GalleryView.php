<?php

/**
 * The gallery views.
 *
 * PHP version 5
 *
 * @category  CMSimple_XH
 * @package   Fotorama
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2015 Christoph M. Becker <http://3-magi.net>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link      http://3-magi.net/?CMSimple_XH/Fotorama_XH
 */

namespace Fotorama;

/**
 * The gallery views.
 *
 * @category CMSimple_XH
 * @package  Fotorama
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Fotorama_XH
 */
class GalleryView
{
    /**
     * The gallery name.
     *
     * @var string
     */
    protected $name;

    /**
     * Initializes a new instance.
     *
     * @param string $name A gallery name.
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Renders the gallery.
     *
     * @return string (X)HTML
     *
     * @global array The paths of system files and folders.
     */
    public function render()
    {
        global $pth;

        $path = $pth['folder']['content'] . 'fotorama/' . $this->name . '.xml';
        $gallery = simplexml_load_file($path);
        $this->emitJS();
        $html = $this->renderGalleryStartTag($gallery);
        foreach ($gallery->pic as $pic) {
            $caption = XH_hsc(isset($pic['caption']) ? $pic['caption'] : '');
            $filename = $pth['folder']['images'] . $gallery['path'] . '/'
                . $pic['path'];
            if (isset($gallery['nav'])) {
                $thumbnail = $this->makeThumbnail($filename, 64);
                $html .= '<a href="' . $filename . '">';
            } else {
                $thumbnail = $filename;
            }
            $html .= tag(
                'img src="' . $thumbnail . '" data-caption="' . $caption
                . '" alt="' . $caption . '"'
            );
            if (isset($gallery['nav'])) {
                $html .= '</a>';
            }
        }
        $html .= '</div>';
        return $html;
    }

    /**
     * Renders the start tag of a gallery.
     *
     * @param SimpleXMLElement $gallery A gallery.
     *
     * @return string (X)HTML
     */
    protected function renderGalleryStartTag(\SimpleXMLElement $gallery)
    {
        $html = '<div class="fotorama"';
        if (isset($gallery['width'])) {
            $html .= ' data-width="' . $gallery['width'] . '"';
        }
        if (isset($gallery['ratio'])) {
            $html .= ' data-ratio="' . $gallery['ratio'] . '"';
        }
        if (isset($gallery['nav'])) {
            $html .= ' data-nav="thumbs"';
        }
        if (isset($gallery['fullscreen'])) {
            $html .= ' data-allowfullscreen="' . $gallery['fullscreen'] . '"';
        }
        if (isset($gallery['transition'])) {
            $html .= ' data-transition="' . $gallery['transition'] . '"';
        }
        $html .= '>';
        return $html;
    }

    /**
     * Emits the required JavaScript.
     *
     * @return void
     *
     * @global array  The paths of system files and folders.
     * @global string (X)HTML fragment to be inserted into the <head> element.
     */
    protected function emitJS()
    {
        global $hjs, $pth;

        include_once $pth['folder']['plugins'] . 'jquery/jquery.inc.php';
        include_jquery();
        $hjs .= tag(
            'link rel="stylesheet" type="text/css" href="'
            . $pth['folder']['plugins'] . 'fotorama/lib/fotorama.css"'
        );
        include_jqueryplugin(
            'fotorama', $pth['folder']['plugins'] . 'fotorama/lib/fotorama.js'
        );
    }

    /**
     * Creates a cached thumbnail if necessary, and returns its path.
     *
     * @param string $path A file path.
     * @param int    $size A minimum size in pixels.
     *
     * @return string
     *
     * @global array The paths of system files and folders.
     */
    protected function makeThumbnail($path, $size)
    {
        global $pth;

        $md5 = md5($path);
        $thumb = $pth['folder']['plugins'] . 'fotorama/cache/'
            . "{$md5}_{$size}.jpg";
        if (!file_exists($thumb) || filemtime($thumb) < filemtime($path)) {
            $source = imagecreatefromjpeg($path);
            $w1 = imagesx($source);
            $h1 = imagesy($source);
            if ($w1 < $h1) {
                $w2 = $size;
                $h2 = $w2 / $w1 * $h1;
            } else {
                $h2 = $size;
                $w2 = $h2 / $h1 * $w1;
            }
            $dest = imagecreatetruecolor($w2, $h2);
            imagecopyresampled($dest, $source, 0, 0, 0, 0, $w2, $h2, $w1, $h1);
            imagejpeg($dest, $thumb);
            imagedestroy($source);
            imagedestroy($dest);
        }
        return $thumb;
    }
}

?>
