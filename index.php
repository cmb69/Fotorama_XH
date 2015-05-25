<?php

/**
 * The "main program".
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
 * The plugin version.
 */
define('FOTORAMA_VERSION', '@FOTORAMA_VERSION@');

 /**
  * Renders a gallery.
  *
  * @param string $name A gallery name.
  *
  * @return string (X)HTML
  */
function fotorama($name)
{
    $view = new Fotorama_GalleryView($name);
    return $view->render();
}

?>
