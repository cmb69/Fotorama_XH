<?php

/*
Copyright 2015-2016 Christoph M. Becker

This file is part of Fotorama_XH.

Fotorama_XH is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Fotorama_XH is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Fotorama_XH.  If not, see <http://www.gnu.org/licenses/>.
*/

namespace Fotorama;

class GalleryView
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var bool
     */
    private static $jsEmitted = false;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return string (X)HTML
     */
    public function render()
    {
        global $pth, $plugin_tx;

        $service = new GalleryService();
        if (!$service->hasGallery($this->name)) {
            return XH_message(
                'fail',
                $plugin_tx['fotorama']['message_no_gallery'],
                $this->name
            );
        }
        $gallery = $service->findGallery($this->name);
        if (!self::$jsEmitted) {
            $this->emitJS();
        }
        $html = $this->renderGalleryStartTag($gallery);
        $html .= $this->renderPictures($gallery);
        $html .= '</div>';
        return $html;
    }

    protected function emitJS()
    {
        global $hjs, $pth;

        include_once $pth['folder']['plugins'] . 'jquery/jquery.inc.php';
        include_jquery();
        $hjs .= tag(
            'link rel="stylesheet" type="text/css" href="'
            . $pth['folder']['plugins'] . 'fotorama/lib/fotorama.css"'
        );
        include_jqueryplugin(
            'fotorama',
            $pth['folder']['plugins'] . 'fotorama/lib/fotorama.js'
        );
        self::$jsEmitted = true;
    }

    /**
     * @param SimpleXMLElement $gallery
     * @return string (X)HTML
     */
    protected function renderGalleryStartTag(\SimpleXMLElement $gallery)
    {
        $html = '<div class="fotorama"';
        if (isset($gallery['width'])) {
            $html .= ' data-width="' . $gallery['width'] . '"';
        }
        if (isset($gallery['ratio'])) {
            $html .= ' data-ratio="' . $gallery['ratio'] . '"';
        }
        if (isset($gallery['nav'])) {
            $html .= ' data-nav="thumbs"';
        }
        if (isset($gallery['fullscreen'])) {
            $html .= ' data-allowfullscreen="' . $gallery['fullscreen'] . '"';
        }
        if (isset($gallery['transition'])) {
            $html .= ' data-transition="' . $gallery['transition'] . '"';
        }
        $html .= '>';
        return $html;
    }

    private function renderPictures(\SimpleXMLElement $gallery)
    {
        global $pth;
    
        $html = '';
        foreach ($gallery->pic as $pic) {
            $caption = XH_hsc(isset($pic['caption']) ? $pic['caption'] : '');
            if ($isAbsoluteUrl = $this->isAbsoluteUrl($pic['path'])) {
                $filename = $pic['path'];
            } else {
                $filename = $pth['folder']['images'] . $gallery['path'] . '/'
                    . $pic['path'];
            }
            if (isset($gallery['nav'])) {
                if ($isAbsoluteUrl) {
                    $thumbnail = "{$pth['folder']['plugins']}fotorama/images/external.jpg";
                } else {
                    $thumbnail = $this->makeThumbnail($filename, 64);
                }
                $html .= "<a href=\"$filename\" data-caption=\"$caption\">";
            } else {
                $thumbnail = $filename;
            }
            $html .= tag(
                'img src="' . $thumbnail . '" data-caption="' . $caption
                . '" alt="' . $caption . '"'
            );
            if (isset($gallery['nav'])) {
                $html .= '</a>';
            }
        }
        return $html;
    }

    private function isAbsoluteUrl($url)
    {
        return strpos($url, '://') !== false;
    }

    /**
     * @param string $path
     * @param int $size Minimum size in pixels.
     * @return string
     */
    protected function makeThumbnail($path, $size)
    {
        global $pth;

        $md5 = md5($path);
        $thumb = $pth['folder']['plugins'] . 'fotorama/cache/'
            . "{$md5}_{$size}.jpg";
        if (!file_exists($thumb) || filemtime($thumb) < filemtime($path)) {
            $source = imagecreatefromjpeg($path);
            $w1 = imagesx($source);
            $h1 = imagesy($source);
            if ($w1 < $h1) {
                $w2 = $size;
                $h2 = $w2 / $w1 * $h1;
            } else {
                $h2 = $size;
                $w2 = $h2 / $h1 * $w1;
            }
            $dest = imagecreatetruecolor($w2, $h2);
            imagecopyresampled($dest, $source, 0, 0, 0, 0, $w2, $h2, $w1, $h1);
            imagejpeg($dest, $thumb);
            imagedestroy($source);
            imagedestroy($dest);
        }
        return $thumb;
    }
}
