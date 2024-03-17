<?php

namespace Uploady;

/**
 * Simple Class that handles data collection
 *
 * @package Uploady
 * @version 3.0.x
 * @author fariscode <farisksa79@protonmail.com>
 * @license MIT
 * @link https://github.com/farisc0de/Uploady
 */

class DataCollection
{
    /**
     * Function to collect the user IP
     *
     * @return string|false
     *  The user IP or false if failed
     **/
    public function collectIP()
    {
        return json_decode(file_get_contents("https://api.seeip.org/jsonip"))->ip;
    }

    /**
     * Function to identify the user country
     *
     * @return mixed
     *  The user country
     **/
    public function idendifyCountry()
    {
        $obj = json_decode(file_get_contents("https://api.country.is/{$this->collectIP()}"));
        return $obj->country;
    }

    /**
     * Function to identify the user browser
     * @param \Wolfcast\BrowserDetection $browserDetection
     *  The browser detection object
     *
     * @return mixed
     *  The user browser
     **/
    public function getBrowser($browserDetection)
    {
        return $browserDetection->getName();
    }

    /**
     * Function to identify the user operating system
     *
     * @return string
     *  The user operating system
     **/
    public function getOS()
    {

        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? "Unknown User Agent";

        $os_platform =   "";
        $os_array =   array(
            '/windows nt 10/i'      =>  'Windows 10',
            '/windows nt 6.3/i'     =>  'Windows 8.1',
            '/windows nt 6.2/i'     =>  'Windows 8',
            '/windows nt 6.1/i'     =>  'Windows 7',
            '/windows nt 6.0/i'     =>  'Windows Vista',
            '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
            '/windows nt 5.1/i'     =>  'Windows XP',
            '/windows xp/i'         =>  'Windows XP',
            '/windows nt 5.0/i'     =>  'Windows 2000',
            '/windows me/i'         =>  'Windows ME',
            '/win98/i'              =>  'Windows 98',
            '/win95/i'              =>  'Windows 95',
            '/win16/i'              =>  'Windows 3.11',
            '/macintosh|mac os x/i' =>  'Mac OS X',
            '/mac_powerpc/i'        =>  'Mac OS 9',
            '/linux/i'              =>  'Linux',
            '/ubuntu/i'             =>  'Ubuntu',
            '/iphone/i'             =>  'iPhone',
            '/ipod/i'               =>  'iPod',
            '/ipad/i'               =>  'iPad',
            '/android/i'            =>  'Android',
            '/blackberry/i'         =>  'BlackBerry',
            '/webos/i'              =>  'Mobile'
        );

        foreach ($os_array as $regex => $value) {
            if (preg_match($regex, $user_agent)) {
                $os_platform = $value;
            }
        }

        return $os_platform;
    }
}
