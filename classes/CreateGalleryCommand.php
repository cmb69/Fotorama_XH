<?php

/**
 * The create gallery commands.
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
 * The create gallery commands.
 *
 * @category CMSimple_XH
 * @package  Fotorama
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Fotorama_XH
 */
class CreateGalleryCommand extends Command
{
    /**
     * Creates a gallery.
     *
     * @return void
     *
     * @global array             The paths of system files and folders.
     * @global array             The localization of the plugins.
     * @global string            (X)HTML fragment to insert into the contents area.
     * @global XH_CSRFProtection The CSRF protector.
     */
    public function execute()
    {
        global $pth, $plugin_tx, $o, $_XH_csrfProtection;

        $_XH_csrfProtection->check();
        $messages = '';
        $name = $_POST['fotorama_gallery'];
        $path = $_POST['fotorama_folder'];
        $filename = $pth['folder']['images'] . $path;
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>' . PHP_EOL
            . '<!DOCTYPE gallery SYSTEM' . PHP_EOL
            . '        "http://3-magi.net/userfiles/downloads/dtd/gallery.dtd">'
            . PHP_EOL
            . '<gallery path="' . $path . '">' . PHP_EOL;
        if (is_dir($filename)) {
            $files = new \DirectoryIterator($filename);
            foreach ($files as $file) {
                $filename = $file->getPathname();
                if (is_file($filename) && getimagesize($filename)) {
                    $xml .= '    <pic path="' . $file->getFilename(). '"/>'
                        . PHP_EOL;
                }
            }
        } else {
            $messages .= XH_message(
                'warning', $plugin_tx['fotorama']['message_no_folder'], $filename
            );
        }
        $xml .= '</gallery>' . PHP_EOL;
        if (!$this->isValidName($name)) {
            $messages .= XH_message(
                'fail', $plugin_tx['fotorama']['message_invalid_name'], $name
            );
        } else {
            $filename = $pth['folder']['content'] . 'fotorama/' . $name . '.xml';
            if (file_exists($filename)) {
                $messages .= XH_message(
                    'fail', $plugin_tx['fotorama']['message_exists'], $filename
                );
            } elseif (!file_put_contents($filename, $xml)) {
                $messages .= XH_message(
                    'fail', $plugin_tx['fotorama']['message_cant_save'], $filename
                );
            }
        }
        if (!$messages) {
            $this->relocate(
                '?&fotorama&admin=plugin_main&action=edit&fotorama_gallery=' . $name
            );
        } else {
            $o .= $messages . $this->render(new GalleryListCommand());
        }
    }

    /**
     * Returns whether a given name is a valid gallery name.
     *
     * @param string $name A gallery name.
     *
     * @return bool
     */
    protected function isValidName($name)
    {
        return preg_match('/^[a-z0-9-]+$/', $name);
    }
}

?>
