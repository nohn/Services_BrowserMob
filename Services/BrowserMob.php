<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Services_BrowserMob
 *
 * Copyright (c) 2010-2011, Sebastian Nohn <sebastian@nohn.net>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Sebastian Nohn nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * This class acts as interface to the BrowserMob API
 *
 * @category  Services
 * @package   Services_BrowserMob
 * @author    Sebastian Nohn <sebastian@nohn.net>
 * @copyright 2011 Sebastian Nohn <sebastian@nohn.net>
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   $Id$
 * @link      https://github.com/nohn/Services_BrowserMob
 * @see       Services_BrowserMob
 * @since     File available since Release 1.0.0
 */

/**
 * Services_BrowserMob
 *
 * Services_BrowserMob
 *
 * @category Services
 * @package  Services_BrowserMob
 * @author   Sebastian Nohn <sebastian@nohn.net>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  $Id$
 * @link     https://github.com/nohn/Services_BrowserMob
 */

class Services_BrowserMob
{
    private $_key;
    private $_secret;

    /**
     * Set BrowserMob Key
     *
     * @param string $key BrowserMob Key
     *
     * @return void
     */
    public function setKey($key)
    {
        $this->_key = $key;
    }

    /**
     * Get BrowserMob Key
     *
     * @return string BrowserMob Key
     */
    public function getKey()
    {
        return $this->_key;
    }

    /**
     * Set BrowserMob Secret
     *
     * @param string $secret BrowserMob Secret
     *
     * @return void
     */
    public function setSecret($secret)
    {
        $this->_secret = $secret;
    }

    /**
     * Get BrowserMob Secret
     *
     * @return string BrowserMob Secret
     */
    public function getSecret()
    {
        return $this->_secret;
    }

    /**
     * Sign a BrowserMob Request
     *
     * @param string $url    BrowserMob URL
     * @param string $params BrowserMob Parameters
     *
     * @return BrowserMob Signature
     **/
    public function sign($url, $params)
    {
        ksort($params);
        $urlencoded_params = array();
        foreach ($params as $key => $value) {
            $urlencoded_params[urlencode($key)] = urlencode(utf8_encode($value));
        }
        $parsed_url = parse_url($url);
        $data = "GET\n";
        $data .= $parsed_url['host']."\n";
        $data .= $parsed_url['path']."\n";
        $data .= http_build_query($urlencoded_params);
        
        $digest = hash_hmac('sha1', $data, $this->getSecret(), true);
        return base64_encode($digest);
    }
    
    /**
     * Call BrowserMob API
     *
     * @param string $url    BrowserMob URL
     * @param string $params BrowserMob Parameters
     *
     * @return BrowserMob API Response Object
     **/
    public function call($url, $params)
    {
        $params['key'] = $this->getKey();
        $params['timestamp'] = time().'000';
        $params['nonce'] = uniqid();
        $params['signature'] = $this->sign($url, $params);
        $request_url = $url.'?'.http_build_query($params);
        return json_decode(file_get_contents($request_url));
    } 

} // class
?>
