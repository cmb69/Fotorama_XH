<?php

/**
 * The commands.
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
 * The commands.
 *
 * @category CMSimple_XH
 * @package  Fotorama
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Fotorama_XH
 */
abstract class Fotorama_Command
{
    /**
     * Returns the path of the content folder. If the folder does not exist, it
     * is created.
     *
     * @return string
     *
     * @global array The paths of system files and folders.
     */
    protected function findContentFolder()
    {
        global $pth;

        $folder = $pth['folder']['content'] . 'fotorama/';
        if (!file_exists($folder)) {
            mkdir($folder, 0777);
            chmod($folder, 0777);
        }
        return $folder;
    }

    /**
     * Sends a relocation header.
     *
     * @param string $url An URL to relocate to.
     *
     * @return void
     */
    protected function relocate($url)
    {
        header('Location: ' . CMSIMPLE_URL . $url);
        exit();
    }

    /**
     * Returns the output of a command.
     *
     * @param Fotorama_Command $command A command.
     *
     * @return string HTML.
     */     
    protected function render(Fotorama_Command $command)
    {
        ob_start();
        $command->execute();
        return ob_get_clean();
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
