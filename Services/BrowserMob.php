<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Services_BrowserMob
 *
 * This class acts as interface to the BrowserMob API
 *
 * PHP versions 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * Services_BrowserMob
 *
 * @category  Services
 * @package   Services_BrowserMob
 * @author    Sebastian Nohn <sebastian@nohn.net>
 * @copyright 2011 Sebastian Nohn <sebastian@nohn.net>
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
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
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version  $Id$
 * @link     https://github.com/nohn/Services_BrowserMob
 */

class Services_BrowserMob
{
    /**
     * Sign a BrowserMob Request
     *
     * @param string $secret BrowserMob Secret
     * @param string $url    BrowserMob URL
     * @param string $params BrowserMob Parameters
     *
     * @return BrowserMob Signature
     **/
    public function sign($secret, $url, $params)
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
        
        $digest = hash_hmac('sha1', $data, $secret, true);
        return base64_encode($digest);
    }
    
    /**
     * Call BrowserMob API
     *
     * @param string $key    BrowserMob Key
     * @param string $secret BrowserMob Secret
     * @param string $url    BrowserMob URL
     * @param string $params BrowserMob Parameters
     *
     * @return BrowserMob API Response Object
     **/
    public function call($key, $secret, $url, $params)
    {
        $params['key'] = $key;
        $params['timestamp'] = time().'000';
        $params['nonce'] = uniqid();
        $params['signature'] = $this->sign($secret, $url, $params);
        $request_url = $url.'?'.http_build_query($params);
        return json_decode(file_get_contents($request_url));
    } 
} // class
?>
