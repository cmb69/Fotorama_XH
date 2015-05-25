<?php

/**
 * The plugin controllers.
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
 * The plugin controllers.
 *
 * @category CMSimple_XH
 * @package  Fotorama
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Fotorama_XH
 */
class Fotorama_Controller
{
    /**
     * Dispatches on plugin related requests.
     *
     * @return void
     */
    public function dispatch()
    {
        if (defined('XH_ADM') && XH_ADM) {
            if (function_exists('XH_registerStandardPluginMenuItems')) {
                XH_registerStandardPluginMenuItems(true);
            }
            if ($this->isAdministrationRequested()) {
                $this->handleAdministration();
            }
        }
    }

    /**
     * Returns whether the plugin administration is requested.
     *
     * @return bool
     *
     * @global string Whether the plugin administration is requested.
     */
    protected function isAdministrationRequested()
    {
        global $fotorama;

        return function_exists('XH_wantsPluginAdministration')
            && XH_wantsPluginAdministration('fotorama')
            || isset($fotorama) && $fotorama == 'true';
    }

    /**
     * Handles the plugin administration.
     *
     * @return void
     *
     * @global string The value of the <var>admin</var> G/P parameter.
     * @global string The value of the <var>action</var> G/P parameter.
     * @global string (X)HTML fragment to be inserted into the content area.
     */
    protected function handleAdministration()
    {
        global $admin, $action, $o;

        $o .= print_plugin_admin('on');
        switch ($admin) {
        case '':
            $o .= $this->renderPluginInfo();
            break;
        case 'plugin_main':
            switch ($action) {
            case 'create':
                if ($this->createGallery()) {
                    $o .= $this->renderGalleryEditor();
                } else {
                    $o .= $this->renderGalleryList();
                }
                break;
            case 'edit':
                $o .= $this->renderGalleryEditor();
                break;
            case 'save':
                if ($this->saveGallery()) {
                    $o .= $this->renderGalleryList();
                } else {
                    $o .= $this->renderGalleryEditor();
                }
                break;
            default:
                $o .= $this->renderGalleryList();
            }
            break;
        default:
            $o .= plugin_admin_common($action, $admin, 'fotorama');
        }
    }

    /**
     * Renders the plugin info.
     *
     * @return string (X)HTML
     *
     * @global array The paths of system files and folders.
     * @global array The localization of the plugins.
     */
    protected function renderPluginInfo()
    {
        global $pth, $plugin_tx;

        return '<h1>Fotorama</h1>'
            . tag(
                'img src="' . $pth['folder']['plugins'] . 'fotorama/fotorama.png"'
                . ' class="fotorama_logo" alt="'
                . $plugin_tx['fotorama']['alt_logo'] . '"'
            )
            . '<p>Version: ' . FOTORAMA_VERSION . '</p>'
            . '<p>Codeeditor_XH is powered by <a href="http://fotorama.io/">'
            . 'Fotorama</a>.</p>'
            . '<p>Copyright &copy; 2015 <a href="http://3-magi.net">'
            . 'Christoph M. Becker</a></p>'
            . '<p class="fotorama_license">This program is free software:'
            . 'you can redistribute it and/or modify'
            . ' it under the terms of the GNU General Public License as published by'
            . ' the Free Software Foundation, either version 3 of the License, or'
            . ' (at your option) any later version.</p>'
            . '<p class="fotorama_license">This program is distributed in the hope'
            . ' that it will be useful,'
            . ' but <em>without any warranty</em>; without even the implied warranty'
            . ' of <em>merchantability</em> or <em>fitness for a particular purpose'
            . '</em>.  See the GNU General Public License for more details.</p>'
            . '<p class="fotorama_license">You should have received a copy of the'
            . ' GNU General Public License'
            . ' along with this program.  If not, see'
            . ' <a href="http://www.gnu.org/licenses/">http://www.gnu.org/licenses/'
            . '</a>.</p>';
    }

    /**
     * Renders the gallery list.
     *
     * @return string
     *
     * @global string The script name.
     * @global array  The paths of system files and folders.
     * @global array  The localization of the plugins.
     */
    protected function renderGalleryList()
    {
        global $sn, $pth, $plugin_tx;

        $url = $sn . '?&fotorama&admin=plugin_main&action=edit&fotorama_gallery=';
        $html = '<h1>Fotorama &ndash; ' . $plugin_tx['fotorama']['menu_main']
            . '</h1>'
            . '<ul>';
        $files = new DirectoryIterator($pth['folder']['content'] . 'fotorama');
        foreach ($files as $file) {
            $filename = $file->getFilename();
            if (pathinfo($filename, PATHINFO_EXTENSION) == 'xml') {
                $basename = $file->getBasename('.xml');
                $html .= '<li><a href="' . XH_hsc($url . $basename) . '">'
                    . $basename . '</a></li>';
            }
        }
        $html .= '</ul>'
            . '<form action="' . $sn . '?&fotorama" method="post">'
            . tag('input type="hidden" name="admin" value="plugin_main"')
            . '<p><label>' . $plugin_tx['fotorama']['label_name'] . ' '
            . tag('input type="text" name="fotorama_gallery"')
            . '</label></p>'
            . '<p><label>' . $plugin_tx['fotorama']['label_folder'] . ' '
            . tag('input type="text" name="fotorama_folder"')
            . '</label></p>'
            . '<button class="submit" name="action" value="create">'
            . $plugin_tx['fotorama']['label_create'] . '</button>'
            . '</form>';
        return $html;
    }

    /**
     * Renders a gallery editor.
     *
     * @return string
     *
     * @global string The script name.
     * @global array  The paths of system files and folders.
     * @global array  The localization of the plugins.
     */
    protected function renderGalleryEditor()
    {
        global $sn, $pth, $plugin_tx;

        if (isset($_GET['fotorama_gallery'])) {
            $name = $this->sanitizeName($_GET['fotorama_gallery']);
        } else {
            $name = $this->sanitizeName($_POST['fotorama_gallery']);
        }
        $contents = file_get_contents(
            $pth['folder']['content'] . 'fotorama/' . $name . '.xml'
        );
        return '<h1>Fotorama &ndash; "' . $name . '"</h1>'
            . '<form action="' . $sn . '?&fotorama" method="post">'
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

    /**
     * Creates a gallery and returns whether that succeeded.
     *
     * @return bool
     *
     * @global array The paths of system files and folders.
     */
    protected function createGallery()
    {
        global $pth;

        $name = $this->sanitizeName($_POST['fotorama_gallery']);
        $path = $_POST['fotorama_folder'];
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>' . PHP_EOL
            . '<!DOCTYPE gallery SYSTEM "gallery.dtd">' . PHP_EOL
            . '<gallery path="' . $path . '">' . PHP_EOL;
        $files = new DirectoryIterator($pth['folder']['images'] . $path);
        foreach ($files as $file) {
            $filename = $file->getPathname();
            if (is_file($filename) && getimagesize($filename)) {
                $xml .= '    <pic path="' . $file->getFilename(). '"/>' . PHP_EOL;
            }
        }
        $xml .= '</gallery>' . PHP_EOL;
        return file_put_contents(
            $pth['folder']['content'] . 'fotorama/' . $name . '.xml', $xml
        );
    }

    /**
     * Saves a gallery and returns whether that succeeded.
     *
     * @return bool
     *
     * @global array The paths of system files and folders.
     */
    protected function saveGallery()
    {
        global $pth;

        $name = $this->sanitizeName($_POST['fotorama_gallery']);
        $text = $_POST['fotorama_text'];
        return file_put_contents(
            $pth['folder']['content'] . 'fotorama/' . $name . '.xml', $text
        );
    }

    /**
     * Returns the sanitized gallery name.
     *
     * @param string $name A gallery name.
     *
     * @return string
     */
    protected function sanitizeName($name)
    {
        return preg_replace('/[^a-z0-9-]/', '', $name);
    }
}

?>
