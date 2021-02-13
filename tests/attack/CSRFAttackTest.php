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

use PHPUnit\Framework\TestCase;

/**
 * A test case to actually check the CSRF protection.
 *
 * @category Testing
 * @package  Fotorama
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Fotorama_XH
 */
class CSRFAttackTest extends TestCase
{
    /**
     * The URL of the installation.
     *
     * @var string
     */
    protected $url;

    /**
     * The cURL handle.
     *
     * @var resource
     */
    protected $curlHandle;

    /**
     * The path of the cookie file.
     *
     * @var string
     */
    protected $cookieFile;

    /**
     * Sets up the test fixture.
     *
     * Log in to back-end and store cookies in a temp file.
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->url = 'http://localhost' . getenv('CMSIMPLEDIR');
        $this->cookieFile = tempnam(sys_get_temp_dir(), 'CC');

        $this->curlHandle = curl_init($this->url . '?&login=true&keycut=test');
        curl_setopt($this->curlHandle, CURLOPT_COOKIEJAR, $this->cookieFile);
        curl_setopt($this->curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_exec($this->curlHandle);
        curl_close($this->curlHandle);
    }

    /**
     * Sets the cURL options.
     *
     * @param array $fields An array of POST fields.
     *
     * @return void
     */
    protected function setCurlOptions($fields)
    {
        $options = array(
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $fields,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_COOKIEFILE => $this->cookieFile,
            //CURLOPT_COOKIEJAR => $this->cookieFile
        );
        curl_setopt_array($this->curlHandle, $options);
    }

    /**
     * Returns data for testAttack().
     *
     * @return array
     */
    public function dataForAttack()
    {
        return array(
            array(
                array(
                      'admin' => 'plugin_main',
                      'action' => 'create',
                      'fotorama_gallery' => 'foo'
                ),
                '&fotorama'
            ),
            array(
                array(
                    'admin' => 'plugin_main',
                    'action' => 'save',
                    'fotorama_gallery' => 'foo'
                ),
                '&fotorama'
            )
        );
    }

    /**
     * Tests the attacks.
     *
     * @param array  $fields      An array of POST fields.
     * @param string $queryString A query string.
     *
     * @dataProvider dataForAttack
     *
     * @return void
     */
    public function testAttack($fields, $queryString = null)
    {
        $url = $this->url . (isset($queryString) ? '?' . $queryString : '');
        $this->curlHandle = curl_init($url);
        $this->setCurlOptions($fields);
        curl_exec($this->curlHandle);
        $actual = curl_getinfo($this->curlHandle, CURLINFO_HTTP_CODE);
        curl_close($this->curlHandle);
        $this->assertEquals(403, $actual);
    }
}
