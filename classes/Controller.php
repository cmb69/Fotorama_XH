<?php

/**
 * The plugin controllers.
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
 * The plugin controllers.
 *
 * @category CMSimple_XH
 * @package  Fotorama
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Fotorama_XH
 */
class Controller
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
            $o .= $this->render(new PluginInfoCommand());
            break;
        case 'plugin_main':
            $this->handleMainAction();
            break;
        default:
            $o .= plugin_admin_common($action, $admin, 'fotorama');
        }
    }

    /**
     * Handles plugin_main actions.
     *
     * @return void
     */
    protected function handleMainAction()
    {
        global $action, $o;
        
        switch ($action) {
        case 'create':
            $this->createGallery();
            break;
        case 'edit':
            $o .= $this->render(new GalleryEditorCommand());
            break;
        case 'save':
            $this->saveGallery();
            break;
        default:
            $o .= $this->render(new GalleryListCommand());
        }
    }

    /**
     * Returns the output of a command.
     *
     * @param Command $command A command.
     *
     * @return string HTML.
     */     
    protected function render(Command $command)
    {
        ob_start();
        $command->execute();
        return ob_get_clean();
    }

    /**
     * Creates a gallery.
     *
     * @return void
     */
    protected function createGallery()
    {
        (new CreateGalleryCommand())->execute();
    }

    /**
     * Saves a gallery.
     *
     * @return void
     */
    protected function saveGallery()
    {
        $command = new SaveGalleryCommand();
        $command->execute();
    }
}

?>
