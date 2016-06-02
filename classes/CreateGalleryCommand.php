<?php

/**
 * The create gallery commands.
 *
 * PHP version 5
 *
 * @category  CMSimple_XH
 * @package   Fotorama
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2015-2016 Christoph M. Becker <http://3-magi.net>
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
     * @global array             The localization of the plugins.
     * @global string            (X)HTML fragment to insert into the contents area.
     * @global XH_CSRFProtection The CSRF protector.
     */
    public function execute()
    {
        global $plugin_tx, $o, $_XH_csrfProtection;

        $_XH_csrfProtection->check();
        $messages = '';
        $name = stsl($_POST['fotorama_gallery']);
        $path = stsl($_POST['fotorama_folder']);
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>' . PHP_EOL
            . '<!DOCTYPE gallery SYSTEM' . PHP_EOL
            . '        "http://3-magi.net/userfiles/downloads/dtd/gallery.dtd">'
            . PHP_EOL
            . '<gallery path="' . $path . '">' . PHP_EOL;
        $service = new GalleryService();
        if (!$service->hasImageFolder($path)) {
            foreach ($service->findImagesIn($path) as $image) {
                $xml .= '    <pic path="' . $image . '"/>' . PHP_EOL;
            }
        } else {
            $messages .= XH_message(
                'warning',
                $plugin_tx['fotorama']['message_no_folder'],
                $service->getImageFoldername($path)
            );
        }
        $xml .= '</gallery>' . PHP_EOL;
        if (!$this->isValidName($name)) {
            $messages .= XH_message(
                'fail',
                $plugin_tx['fotorama']['message_invalid_name'],
                $name
            );
        } else {
            if ($service->hasGallery($name)) {
                $messages .= XH_message(
                    'fail',
                    $plugin_tx['fotorama']['message_exists'],
                    $service->getGalleryFilename($name)
                );
            } elseif (!$service->saveGalleryXML($name, $xml)) {
                $messages .= XH_message(
                    'fail',
                    $plugin_tx['fotorama']['message_cant_save'],
                    $service->getGalleryFilename($name)
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
