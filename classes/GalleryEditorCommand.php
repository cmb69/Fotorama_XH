<?php

/**
 * The gallery editor commands.
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
 * The gallery editor commands.
 *
 * @category CMSimple_XH
 * @package  Fotorama
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Fotorama_XH
 */
class GalleryEditorCommand extends Command
{
    /**
     * Renders a gallery editor.
     *
     * @return string
     *
     * @global string The script name.
     * @global array  The localization of the plugins.
     * @global XH_CSRFProtection The CSRF protector.
     */
    public function execute()
    {
        global $sn, $plugin_tx, $_XH_csrfProtection;

        if (isset($_GET['fotorama_gallery'])) {
            $name = $this->sanitizeName(stsl($_GET['fotorama_gallery']));
        } else {
            $name = $this->sanitizeName(stsl($_POST['fotorama_gallery']));
        }
        $service = new GalleryService();
        $contents = $service->findGalleryXML($name);
        echo '<h1>Fotorama &ndash; "' . $name . '"</h1>'
            . '<form action="' . $sn . '?&amp;fotorama" method="post">'
            . $_XH_csrfProtection->tokenInput()
            . tag('input type="hidden" name="admin" value="plugin_main"')
            . tag(
                'input type="hidden" name="fotorama_gallery" value="' . $name . '"'
            )
            . '<textarea rows="25" cols="80" class="xh_file_edit"'
            . ' name="fotorama_text">' . XH_hsc($contents) . '</textarea>'
            . '<button name="action" value="save">'
            . $plugin_tx['fotorama']['label_save'] . '</button>'
            . '</form>';
    }
}

?>
