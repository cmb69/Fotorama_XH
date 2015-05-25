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
                XH_registerStandardPluginMenuItems(false);
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

        $o .= print_plugin_admin('off');
        switch ($admin) {
        case '':
            $o .= $this->renderPluginInfo();
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
}

?>
