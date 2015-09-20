<?php

/**
 * The gallery list commands.
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
 * The gallery list commands.
 *
 * @category CMSimple_XH
 * @package  Fotorama
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Fotorama_XH
 */
class Fotorama_GalleryListCommand extends Fotorama_Command
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
        $files = new DirectoryIterator($this->findContentFolder());
        foreach ($files as $file) {
            $filename = $file->getFilename();
            if (pathinfo($filename, PATHINFO_EXTENSION) == 'xml') {
                $basename = basename($filename, '.xml');
                $html .= '<li><a href="' . XH_hsc($url . $basename) . '">'
                    . $basename . '</a></li>';
            }
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
     *
     * @global array The paths of system files and folders.
     */
    protected function renderImageFolderSelect()
    {
        global $pth;

        return '<select name="fotorama_folder">'
            . $this->renderImageFolderSelectOptions($pth['folder']['images'], '')
            . '</select>';
    }

    /**
     * Renders the select options of an image folder.
     *
     * @param string $path   A folder path.
     * @param string $prefix A prefix.
     *
     * @return string (X)HTML
     */
    protected function renderImageFolderSelectOptions($path, $prefix)
    {
        $html = '';
        $files = new DirectoryIterator($path);
        foreach ($files as $file) {
            if (!$file->isDot() && $file->isDir()) {
                $html .= '<option>' . $prefix . $file->getFilename() . '</option>';
                $html .= $this->renderImageFolderSelectOptions(
                    $file->getPathname(), $prefix . $file->getFilename() . '/'
                );
            }
        }
        return $html;
    }
}

?>
