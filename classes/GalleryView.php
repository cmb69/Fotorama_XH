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

/**
 * The gallery views.
 *
 * @category CMSimple_XH
 * @package  Fotorama
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Fotorama_XH
 */
class Fotorama_GalleryView
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
     * @global array  The paths of system files and folders.
     */
    public function render()
    {
        global $pth;

        $path = $pth['folder']['content'] . 'fotorama/' . $this->name . '.xml';
        $gallery = simplexml_load_file($path);
        $this->emitJS();
        $html = '<div class="fotorama">';
        foreach ($gallery->pic as $pic) {
            $html .= tag(
                'img src="' . $pth['folder']['images'] . $gallery['path'] . '/'
                . $pic['path'] . '"'
            );
        }
        $html .= '</div>';
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
}

?>
