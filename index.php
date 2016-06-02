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

define('FOTORAMA_VERSION', '@FOTORAMA_VERSION@');

 /**
  * @param string $name
  * @return string (X)HTML
  */
function fotorama($name)
{
    $view = new Fotorama\GalleryView($name);
    return $view->render();
}

$temp = new Fotorama\Controller();
$temp->dispatch();
