<?php

namespace Uploady;

/**
 * A class that has some utilities functions
 *
 * @package Uploady
 * @version 1.5.2
 * @author fariscode <farisksa79@gmail.com.com>
 * @license MIT
 * @link https://github.com/FarisCode511/Uploady
 */
class Utils
{

    /**
     * Sanitize value
     *
     * @param string $value
     *  The value of the malicious string you want to sanitize
     * @return string
     *  Return the sanitized string
     */
    public function sanitize($value)
    {
        $data = trim($value);
        $data = htmlspecialchars($data, ENT_QUOTES, "UTF-8");
        $data = filter_var($data, FILTER_SANITIZE_STRING);
        $data = strip_tags($data);
        return $data;
    }

    /**
     * Show custom alerts when needed
     *
     * @param string $message
     *  The message you want to show
     * @param string $style
     *  The style of the message using bootstrap colors
     * @param string $icon
     *  The alert icon using font awesome icons
     * @return string
     *  Return a formatted message as an HTML code
     */
    public function alert($message, $style = "primary", $icon = "info-circle")
    {
        if ($icon != null) {
            $icon = sprintf('<span class="fa fa-%s"></span>', $this->sanitize($icon));
        } else {
            $icon = "";
        }

        return sprintf(
            '<div class="alert alert-%s">%s %s</div>',
            $this->sanitize($style),
            $icon,
            $this->sanitize($message)
        );
    }

    /**
     * Show dismissible alerts when needed
     *
     * @param string $message
     *  The message you want to show
     * @param string $style
     *  The style of the message using bootstrap colors
     * @param string $icon
     *  The alert icon using font awesome icons
     * @return string
     *  Return a formatted message as an HTML code
     */
    public function dismissibleAlert($message, $style = "primary", $icon = "info-circle")
    {
        if ($icon != null) {
            $icon = sprintf('<span class="fa fa-%s"></span>', $this->sanitize($icon));
        } else {
            $icon = "";
        }

        return sprintf(
            '<div class="alert alert-%s alert-dismissible fade show">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>%s %s
             </div>',
            $this->sanitize($style),
            $icon,
            $message
        );
    }

    /**
     * Redirect a user to a page when needed
     *
     * @param string $url
     *  The URL or the page you want to redirect the user to it.
     * @return void
     */
    public function redirect($url)
    {
        header('Location: ' . $url, true, 301);
        exit;
    }

    /**
     * Show an input with a custom value when needed, like CRSF value
     *
     * @param string $name
     *  The name of the input example "CSRF"
     * @param string $value
     *  The value of the input example a CSRF token
     * @param bool $hidden
     *  Set true if you want to make the input hidden otherwise false
     * @return string
     *  Return a formatted input as an HTML code
     */
    public function input($name, $value, $hidden = true)
    {
        $h = ($hidden ? 'hidden' : "");

        $name = $this->sanitize($name);

        return sprintf(
            '<input type="text" value="%s" name="%s" id="%s" %s />',
            $this->sanitize($value),
            $name,
            $name,
            $h
        );
    }

    /**
     * Check if a link is active or not in the navbar
     *
     * @param $page
     *  Page variable exists on every page in this project
     * @param string $page_name
     *  The page name you want to check
     * @return string
     *  Return active or null
     */
    public function linkActive($page, $page_name)
    {
        if (isset($page) && $page != null) {
            return ($page == $page_name) ? "active" : "";
        } else {
            return "";
        }
    }

    /**
     * Search for a value inside an associative array
     *
     * @param array $array
     *  The array you want to search inside it
     * @param mixex $key
     *  The kay you want to check is value
     * @param mixed $val
     *  The value you want to find in the array
     * @return bool
     *  Return true if it exists otherwise false
     */
    public function findKeyValue($array, $key, $val)
    {
        foreach ($array as $item) {
            if (is_array($item) && $this->findKeyValue($item, $key, $val)) {
                return true;
            }

            if (isset($item[$key]) && $item[$key] == $val) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check, Validate and Format a URL
     *
     * @param mixed $url
     *  The url you want to check and validate
     * @return mixed
     *  Return a valid url, localhost, or an invalid message
     */
    public function validateURL($url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            $url_parase = parse_url(filter_var($url, FILTER_SANITIZE_URL));
            return $url_parase['scheme'] . "://" . $url_parase['host'] . "/";
        } elseif (filter_var($url, FILTER_VALIDATE_IP)) {
            return $url;
        } elseif ($url == "localhost") {
            return $url;
        } else {
            return "Domain does not exist";
        }
    }

    /**
     * Check if the provided email address is valid or not
     *
     * @param string $email
     *  The email address you want to check it against the function rules
     * @return bool
     *  Return true if the email address is valid otherwise return false
     */
    public function validateEmail($email)
    {

        $email = filter_var($email, FILTER_SANITIZE_EMAIL);

        $domain = strtolower(substr($email, strpos($email, '@') + 1));

        // A list of popular email providers
        $providers = [
            'gmail.com',
            'hotmail.com',
            'outlook.com',
            'msn.com',
            'outlook.sa',
            'aol.com',
            'protonmail.com'
        ];

        $inarray = in_array($domain, $providers);

        return (filter_var($email, FILTER_VALIDATE_EMAIL) && checkdnsrr($domain) != false && $inarray);
    }

    /**
     * Return the full website url
     *
     * @return string
     *  Return the full website url
     */
    public function siteUrl($file = null)
    {
        if (defined("SITE_URL") && SITE_URL != null) {
            if ($file != null) {
                return rtrim(SITE_URL, "/") . $file;
            } else {
                return rtrim(SITE_URL, "/");
            }
        }
    }

    /**
     * Create a cookie that expires in 30 days when needed
     *
     * @param string $name
     *  The cookie name
     * @param mixed $value
     *  The cookie value
     * @return bool
     *  Return true if the cookie is created
     */
    public function createCookie($name, $value)
    {
        if (!isset($_COOKIE[$name])) {
            if (setcookie($name, $value, time() + 60 * 60 * 24 * 30, "/")) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Delete a cookie when needed
     *
     * @param string $name
     *  The cookie name
     * @return bool
     *  Return true if the cookie is removed
     */
    public function deleteCookie($name)
    {
        if (isset($_COOKIE[$name])) {
            if (setcookie($name, "", time() - 3600, "/")) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Enqueue a stylesheet file when needed
     *
     * @param string $style_path
     *  The fulll path for the stylesheet file
     * @return bool
     */
    public function style($style_path, $assets = "assets")
    {
        if (filter_var($style_path, FILTER_VALIDATE_URL)) {
            $site_url = $style_path;
        } else {
            $site_url = $this->siteUrl("/{$assets}/{$style_path}");
        }
        echo "<link href=\"{$site_url}\" rel=\"stylesheet\" />" . "\n";
        return true;
    }

    /**
     * Enqueue a javascript file when needed
     *
     * @param string $script_path
     *  The fulll path for the javascript file
     * @return bool
     */
    public function script($script_path, $assets = "assets")
    {
        if (filter_var($script_path, FILTER_VALIDATE_URL)) {
            $site_url = $script_path;
        } else {
            $site_url = $this->siteUrl("/{$assets}/{$script_path}");
        }
        echo "<script src=\"{$site_url}\"></script>" . "\n";
        return true;
    }

    /**
     * Like Codeigniter sanatize a key:value pair array
     *
     * @param array $data
     * @return array
     */
    public function esc($data)
    {
        $sanitized = [];

        foreach ($data as $key => $value) {
            $sanitized[$this->sanitize($key)] = $this->sanitize($value);
        }

        return $sanitized;
    }
}
