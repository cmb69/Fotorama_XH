<?php

/**
 * The save gallery commands.
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
 * The save gallery commands.
 *
 * @category CMSimple_XH
 * @package  Fotorama
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Fotorama_XH
 */
class SaveGalleryCommand extends Command
{
    /**
     * Saves a gallery.
     *
     * @return void
     *
     * @global array  The paths of system files and folders.
     * @global array  The configuration of the plugins.
     * @global array  The localization of the plugins.
     * @global XH_CSRFProtection The CSRF protector.
     * @global string (X)HTML fragment to insert into the contents area.
     */
    public function execute()
    {
        global $pth, $plugin_cf, $plugin_tx, $_XH_csrfProtection, $o;

        $_XH_csrfProtection->check();
        $messages = '';
        $name = $this->sanitizeName($_POST['fotorama_gallery']);
        $text = $_POST['fotorama_text'];
        if ($plugin_cf['fotorama']['xml_auto_validate'] && !$this->validate($text)) {
            $messages .= XH_message(
                'warning', $plugin_tx['fotorama']['message_invalid_xml']
            );
        }
        $filename = $pth['folder']['content'] . 'fotorama/' . $name . '.xml';
        if (!file_put_contents($filename, $text)) {
            $messages .= XH_message(
                'fail', $plugin_tx['fotorama']['message_cant_save'], $filename
            );
        }
        if (!$messages) {
            $this->relocate('?&fotorama&admin=plugin_main&action=plugin_text');
        } else {
            $o .= $messages . $this->render(new GalleryEditorCommand());
        }
    }

    /**
     * Validates the given XML.
     *
     * @param string $xml An XML string.
     *
     * @return bool
     */
    protected function validate($xml)
    {
        $doc = new \DomDocument();
        return $doc->loadXML($xml) && $doc->validate();
    }
}

?>
