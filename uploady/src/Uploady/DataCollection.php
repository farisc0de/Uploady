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
     * @return string
     *  The user IP or fallback to server variable
     **/
    public function collectIP(): string
    {
        $headers = [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
            'HTTP_CLIENT_IP',
            'REMOTE_ADDR'
        ];

        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ip = $_SERVER[$header];
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    /**
     * Function to identify the user country
     *
     * @return string
     *  The user country code or 'Unknown'
     **/
    public function idendifyCountry(): string
    {
        $ip = $this->collectIP();
        
        if ($ip === '127.0.0.1' || filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return 'Unknown';
        }

        $apis = [
            "https://api.country.is/{$ip}" => function($data) {
                return $data->country ?? null;
            },
            "https://ipapi.co/{$ip}/country/" => function($data) {
                return is_string($data) ? trim($data) : null;
            },
        ];

        foreach ($apis as $url => $parser) {
            try {
                $context = stream_context_create([
                    'http' => [
                        'timeout' => 3,
                        'ignore_errors' => true,
                    ],
                    'ssl' => [
                        'verify_peer' => true,
                        'verify_peer_name' => true,
                    ],
                ]);

                $response = @file_get_contents($url, false, $context);
                
                if ($response === false) {
                    continue;
                }

                $data = json_decode($response);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $data = $response;
                }

                $country = $parser($data);
                if ($country && strlen($country) === 2) {
                    return strtoupper($country);
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return 'Unknown';
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
    public function getOS(): string
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        if (empty($userAgent)) {
            return 'Unknown';
        }

        $osPatterns = [
            '/windows nt 10/i'                      => 'Windows 10/11',
            '/windows nt 6\.3/i'                    => 'Windows 8.1',
            '/windows nt 6\.2/i'                    => 'Windows 8',
            '/windows nt 6\.1/i'                    => 'Windows 7',
            '/windows nt 6\.0/i'                    => 'Windows Vista',
            '/windows nt 5\.2/i'                    => 'Windows Server 2003/XP x64',
            '/windows nt 5\.1/i'                    => 'Windows XP',
            '/windows nt 5\.0/i'                    => 'Windows 2000',
            '/windows phone/i'                      => 'Windows Phone',
            '/iphone/i'                             => 'iOS (iPhone)',
            '/ipad/i'                               => 'iOS (iPad)',
            '/ipod/i'                               => 'iOS (iPod)',
            '/mac os x 10[._]15/i'                  => 'macOS Catalina',
            '/mac os x 10[._]14/i'                  => 'macOS Mojave',
            '/mac os x 10[._]13/i'                  => 'macOS High Sierra',
            '/mac os x 11/i'                        => 'macOS Big Sur',
            '/mac os x 12/i'                        => 'macOS Monterey',
            '/mac os x 13/i'                        => 'macOS Ventura',
            '/mac os x 14/i'                        => 'macOS Sonoma',
            '/mac os x 15/i'                        => 'macOS Sequoia',
            '/macintosh|mac os x/i'                 => 'macOS',
            '/cros/i'                               => 'Chrome OS',
            '/android (\d+)/i'                      => 'Android',
            '/ubuntu/i'                             => 'Ubuntu',
            '/fedora/i'                             => 'Fedora',
            '/debian/i'                             => 'Debian',
            '/arch/i'                               => 'Arch Linux',
            '/centos/i'                             => 'CentOS',
            '/red hat/i'                            => 'Red Hat',
            '/linux/i'                              => 'Linux',
            '/freebsd/i'                            => 'FreeBSD',
            '/playstation/i'                        => 'PlayStation',
            '/xbox/i'                               => 'Xbox',
            '/nintendo/i'                           => 'Nintendo',
            '/bot|crawler|spider|slurp|bingbot|googlebot/i' => 'Bot/Crawler',
        ];

        foreach ($osPatterns as $pattern => $os) {
            if (preg_match($pattern, $userAgent, $matches)) {
                if ($os === 'Android' && isset($matches[1])) {
                    return "Android {$matches[1]}";
                }
                return $os;
            }
        }

        return 'Unknown';
    }
}
