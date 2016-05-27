<?php

/**
 * The gallery list commands.
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
 * The gallery list commands.
 *
 * @category CMSimple_XH
 * @package  Fotorama
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Fotorama_XH
 */
class GalleryListCommand extends Command
{
    /**
     * Renders the gallery list.
     *
     * @return void
     *
     * @global string            The script name.
     * @global array             The localization of the plugins.
     * @global XH_CSRFProtection The CSRF protector.
     */
    public function execute()
    {
        global $sn, $plugin_tx, $_XH_csrfProtection;

        $url = $sn . '?&fotorama&admin=plugin_main&action=edit&fotorama_gallery=';
        $html = '<h1>Fotorama &ndash; ' . $plugin_tx['fotorama']['menu_main']
            . '</h1>'
            . '<ul>';
        $service = new GalleryService();
        foreach ($service->findAllGalleries() as $gallery) {
            $html .= '<li><a href="' . XH_hsc($url . $gallery) . '">'
                . $gallery . '</a></li>';
            
        }
        $html .= '</ul>'
            . '<form action="' . $sn . '?&amp;fotorama" method="post">'
            . $_XH_csrfProtection->tokenInput()
            . tag('input type="hidden" name="admin" value="plugin_main"')
            . '<fieldset><legend>' . $plugin_tx['fotorama']['label_create_gallery']
            . '</legend>'
            . '<p><label>' . $plugin_tx['fotorama']['label_name'] . ' '
            . tag('input type="text" name="fotorama_gallery"')
            . '</label></p>'
            . '<p><label>' . $plugin_tx['fotorama']['label_folder'] . ' '
            . $this->renderImageFolderSelect()
            . '</label></p>'
            . '<p><button class="submit" name="action" value="create">'
            . $plugin_tx['fotorama']['label_create'] . '</button></p>'
            . '</fieldset>'
            . '</form>';
        echo $html;
    }

    /**
     * Renders an image folder select element.
     *
     * @return string (X)HTML
     */
    protected function renderImageFolderSelect()
    {
        return '<select name="fotorama_folder">'
            . $this->renderImageFolderSelectOptions()
            . '</select>';
    }

    /**
     * Renders the select options of an image folder.
     *
     * @return string (X)HTML
     */
    protected function renderImageFolderSelectOptions()
    {
        $html = '';
        $service = new GalleryService();
        foreach ($service->findImageFolders() as $folder) {
            $html .= '<option>' . $folder . '</option>';
        }
        return $html;
    }
}

?>
