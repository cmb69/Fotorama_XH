<?php

/**
 * The gallery services.
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
 * The gallery services.
 *
 * @category CMSimple_XH
 * @package  Fotorama
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Fotorama_XH
 */
class GalleryService
{
    /**
     * Finds all galleries.
     *
     * @return array
     */
    public function findAllGalleries()
    {
        $result = array();
        $files = new \DirectoryIterator($this->findContentFolder());
        foreach ($files as $file) {
            $filename = $file->getFilename();
            if (pathinfo($filename, PATHINFO_EXTENSION) == 'xml') {
                $result[] = basename($filename, '.xml');
            }
        }
        natcasesort($result);
        return array_values($result);
    }

    /**
     * Returns whether a certain gallery exists.
     *
     * @param string $name A name.
     *
     * @return bool
     */
    public function hasGallery($name)
    {
        return file_exists($this->getGalleryFilename($name));
    }
    
    /**
     * Finds a gallery by name.
     *
     * @param string $name A name.
     *
     * @return \SimpleXMLElement
     */
    public function findGallery($name)
    {
        return simplexml_load_file($this->getGalleryFilename($name));
    }

    /**
     * Finds a gallery's XML by name.
     *
     * @param string $name A name.
     *
     * @return string
     */
    public function findGalleryXML($name)
    {
        return file_get_contents($this->getGalleryFilename($name));
    }
    
    /**
     * Saves XML in a gallery file, and returns whether that succeeded.
     *
     * @param string $name A name.
     * @param string $xml  An XML string.
     *
     * @return bool
     */
    public function saveGalleryXML($name, $xml)
    {
        global $pth;

        $filename = $pth['folder']['content'] . 'fotorama/' . $name . '.xml';
        return file_put_contents($filename, $xml) !== false;
    }

    /**
     * Returns the filename of a gallery file.
     *
     * @param string $name A name.
     *
     * @return string
     */
    public function getGalleryFilename($name)
    {
        global $pth;

        return $pth['folder']['content'] . 'fotorama/' . $name . '.xml';
    }

    /**
     * Returns all image folders, alphabetically sorted.
     *
     * @return string[]
     */
    public function findImageFolders()
    {
        global $pth;

        $folders = $this->findImageFoldersIn($pth['folder']['images'], '');
        natcasesort($folders);
        return array_values($folders);
    }

    /**
     * Returns whether a certain image folder exists.
     *
     * @param string $path A path inside the image folder.
     *
     * @return bool
     */
    public function hasImageFolder($path)
    {
        return is_dir($path);
    }

    /**
     * Returns all image folders under a given folder.
     *
     * @param string $path   A path to search in.
     * @param string $prefix A prefix for the folder names.
     *
     * @return string[]
     */
    protected function findImageFoldersIn($path, $prefix)
    {
        $folders = array();
        $files = new \DirectoryIterator($path);
        foreach ($files as $file) {
            if (!$file->isDot() && $file->isDir()) {
                $folders[] = $prefix . $file->getFilename();
                $folders = array_merge(
                    $folders,
                    $this->findImageFoldersIn(
                        $file->getPathname(),
                        $prefix . $file->getFilename() . '/'
                    )
                );
            }
        }
        return $folders;
    }

    /**
     * Returns all images in a folder, alphabetically sorted.
     *
     * @param string $path A path inside the image folder to search in.
     *
     * @return string[]
     */
    public function findImagesIn($path)
    {
        global $pth;

        $images = array();
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $files = new \DirectoryIterator("{$pth['folder']['images']}$path");
        foreach ($files as $file) {
            $filename = $file->getPathname();
            if (is_file($filename) && $finfo->file($filename) == 'image/jpeg') {
                $images[] = $file->getFilename();
            }
        }
        natcasesort($images);
        return array_values($images);
    }

    /**
     * Returns the foldername.
     *
     * @param string $path A path inside the image folder.
     *
     * @return string
     */
    public function getImageFoldername($path)
    {
        global $pth;

        return "{$pth['folder']['images']}$path";
    }

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
}
