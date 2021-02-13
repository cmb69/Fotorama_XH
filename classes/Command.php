<?php

/*
Copyright 2015-2021 Christoph M. Becker

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

abstract class Command
{
    /**
     * @param string $url
     */
    protected function relocate($url)
    {
        header('Location: ' . CMSIMPLE_URL . $url);
        exit();
    }

    /**
     * @return string HTML.
     */
    protected function render(Command $command)
    {
        ob_start();
        $command->execute();
        return ob_get_clean();
    }

    /**
     * @param string $name
     * @return string
     */
    protected function sanitizeName($name)
    {
        return preg_replace('/[^a-z0-9-]/', '', $name);
    }
}
