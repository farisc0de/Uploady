<?php

namespace Uploady;

/**
 * A class that has some utilities functions
 *
 * @package Uploady
 * @version 3.0.x
 * @author fariscode <farisksa79@gmail.com>
 * @license MIT
 * @link https://github.com/farisc0de/Uploady
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
        if (!is_null($value)) {
            $data = trim($value);
            $data = htmlspecialchars($data, ENT_QUOTES, "UTF-8");
            $data = strip_tags($data);
            return $data;
        }
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
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>%s %s
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
        }

        return "";
    }

    /**
     * Search for a value inside an associative array
     *
     * @param array $array
     *  The array you want to search inside it
     * @param mixed $key
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
            }

            return rtrim(SITE_URL, "/");
        }

        return "";
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
            return setcookie($name, $value, time() + 60 * 60 * 24 * 30, "/", "", true, true);
        }

        return true;
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
            return setcookie($name, "", time() - 3600, "/", "", true, true);
        }

        return true;
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
        $site_url = filter_var($style_path, FILTER_VALIDATE_URL) ?
            $style_path :
            $this->siteUrl("/{$assets}/{$style_path}");

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
    public function script($script_path, $assets = "assets", $async = false)
    {
        $site_url = filter_var($script_path, FILTER_VALIDATE_URL) ?
            $script_path :
            $this->siteUrl("/{$assets}/{$script_path}");

        $async = ($async == true) ? "async" : "";

        echo "<script {$async} src=\"{$site_url}\"></script>" . "\n";
        return true;
    }

    /**
     * Sanatize an associative array, a sequential array or a string
     *
     * Usage:
     *  $sanitizer->useSanitize($_POST["username"]);
     *
     * @param mixed $data
     *  The value of the malicious string you want to sanitize
     * @return mixed
     *  Return a sanitized string, array, or associative array
     */
    public function esc($data)
    {
        if (is_string($data)) {
            if ($this->isEmpty($data)) {
                return false;
            }

            return $this->sanitize($data);
        }


        if (is_array($data)) {
            $santizied = [];

            if ($this->isEmpty($data)) {
                return false;
            }

            if ($this->isAssociative($data) == false) {
                foreach ($data as $value) {
                    $santizied[] = $this->sanitize($value);
                }
            }

            if ($this->isAssociative($data) == true) {
                foreach ($data as $key => $value) {
                    $santizied[$this->sanitize($key)] = $this->sanitize($value);
                }
            }

            return $santizied;
        }

        return false;
    }

    /**
     * Check if the provided array is an associative or a sequential array
     *
     * @param array $array
     *  The array you want to check it's type
     * @return boolean
     *  Return true if provided array is an associative or false otherwise
     */
    public function isAssociative($array)
    {
        return array_keys($array) !== range(0, count($array) - 1);
    }

    /**
     * Check if the provided variable is empty
     *
     * @param mixed $data
     *  The variable you want to check if it's empty or not
     * @return boolean
     *  Return true if the variable does not contain data or false otherwise
     */
    public function isEmpty($data)
    {
        $bool = false;

        if (is_array($data)) {
            $bool = array() === $data;
        }

        if (is_string($data)) {
            $bool = ($data == "");
        }

        return $bool;
    }

    /**
     * Escape SQL Queries
     *
     * @param string $value
     *  The sql query you want to escape
     * @return string
     *  Return the escaped SQL query
     */
    public function escape($value)
    {
        $data = str_replace(
            array("\\", "\0", "\n", "\r", "\x1a", "'", '"'),
            array("\\\\", "\\0", "\\n", "\\r", "\Z", "\'", '\"'),
            $value
        );
        return $data;
    }

    /**
     * Function to return the full list of languages
     *
     * @return array
     *  Return an array of languages
     */
    public function getLanguages()
    {
        return [
            "aa" => "Afar",
            "ab" => "Abkhazian",
            "ae" => "Avestan",
            "af" => "Afrikaans",
            "ak" => "Akan",
            "am" => "Amharic",
            "an" => "Aragonese",
            "ar" => "Arabic",
            "as" => "Assamese",
            "av" => "Avaric",
            "ay" => "Aymara",
            "az" => "Azerbaijani",
            "ba" => "Bashkir",
            "be" => "Belarusian",
            "bg" => "Bulgarian",
            "bh" => "Bihari languages",
            "bi" => "Bislama",
            "bm" => "Bambara",
            "bn" => "Bengali",
            "bo" => "Tibetan",
            "br" => "Breton",
            "bs" => "Bosnian",
            "ca" => "Catalan",
            "ce" => "Chechen",
            "ch" => "Chamorro",
            "co" => "Corsican",
            "cr" => "Cree",
            "cs" => "Czech",
            "cu" => "Church Slavic",
            "cv" => "Chuvash",
            "cy" => "Welsh",
            "da" => "Danish",
            "de" => "German",
            "dv" => "Maldivian",
            "dz" => "Dzongkha",
            "ee" => "Ewe",
            "el" => "Greek",
            "en" => "English",
            "eo" => "Esperanto",
            "es" => "Spanish",
            "et" => "Estonian",
            "eu" => "Basque",
            "fa" => "Persian",
            "ff" => "Fulah",
            "fi" => "Finnish",
            "fj" => "Fijian",
            "fo" => "Faroese",
            "fr" => "French",
            "fy" => "Western Frisian",
            "ga" => "Irish",
            "gd" => "Gaelic",
            "gl" => "Galician",
            "gn" => "Guarani",
            "gu" => "Gujarati",
            "gv" => "Manx",
            "ha" => "Hausa",
            "he" => "Hebrew",
            "hi" => "Hindi",
            "ho" => "Hiri Motu",
            "hr" => "Croatian",
            "ht" => "Haitian",
            "hu" => "Hungarian",
            "hy" => "Armenian",
            "hz" => "Herero",
            "ia" => "Interlingua",
            "id" => "Indonesian",
            "ie" => "Interlingue",
            "ig" => "Igbo",
            "ii" => "Sichuan Yi",
            "ik" => "Inupiaq",
            "io" => "Ido",
            "is" => "Icelandic",
            "it" => "Italian",
            "iu" => "Inuktitut",
            "ja" => "Japanese",
            "jv" => "Javanese",
            "ka" => "Georgian",
            "kg" => "Kongo",
            "ki" => "Kikuyu",
            "kj" => "Kuanyama",
            "kk" => "Kazakh",
            "kl" => "Kalaallisut",
            "km" => "Central Khmer",
            "kn" => "Kannada",
            "ko" => "Korean",
            "kr" => "Kanuri",
            "ks" => "Kashmiri",
            "ku" => "Kurdish",
            "kv" => "Komi",
            "kw" => "Cornish",
            "ky" => "Kirghiz",
            "la" => "Latin",
            "lb" => "Luxembourgish",
            "lg" => "Ganda",
            "li" => "Limburgan",
            "ln" => "Lingala",
            "lo" => "Lao",
            "lt" => "Lithuanian",
            "lu" => "Luba-Katanga",
            "lv" => "Latvian",
            "mg" => "Malagasy",
            "mh" => "Marshallese",
            "mi" => "Maori",
            "mk" => "Macedonian",
            "ml" => "Malayalam",
            "mn" => "Mongolian",
            "mr" => "Marathi",
            "ms" => "Malay",
            "mt" => "Maltese",
            "my" => "Burmese",
            "na" => "Nauru",
            "nb" => "Norwegian",
            "nd" => "North Ndebele",
            "ne" => "Nepali",
            "ng" => "Ndonga",
            "nl" => "Dutch",
            "nn" => "Norwegian",
            "no" => "Norwegian",
            "nr" => "South Ndebele",
            "nv" => "Navajo",
            "ny" => "Chichewa",
            "oc" => "Occitan",
            "oj" => "Ojibwa",
            "om" => "Oromo",
            "or" => "Oriya",
            "os" => "Ossetic",
            "pa" => "Panjabi",
            "pi" => "Pali",
            "pl" => "Polish",
            "ps" => "Pushto",
            "pt" => "Portuguese",
            "qu" => "Quechua",
            "rm" => "Romansh",
            "rn" => "Rundi",
            "ro" => "Romanian",
            "ru" => "Russian",
            "rw" => "Kinyarwanda",
            "sa" => "Sanskrit",
            "sc" => "Sardinian",
            "sd" => "Sindhi",
            "se" => "Northern Sami",
            "sg" => "Sango",
            "si" => "Sinhala",
            "sk" => "Slovak",
            "sl" => "Slovenian",
            "sm" => "Samoan",
            "sn" => "Shona",
            "so" => "Somali",
            "sq" => "Albanian",
            "sr" => "Serbian",
            "ss" => "Swati",
            "st" => "Sotho, Southern",
            "su" => "Sundanese",
            "sv" => "Swedish",
            "sw" => "Swahili",
            "ta" => "Tamil",
            "te" => "Telugu",
            "tg" => "Tajik",
            "th" => "Thai",
            "ti" => "Tigrinya",
            "tk" => "Turkmen",
            "tl" => "Tagalog",
            "tn" => "Tswana",
            "to" => "Tonga",
            "tr" => "Turkish",
            "ts" => "Tsonga",
            "tt" => "Tatar",
            "tw" => "Twi",
            "ty" => "Tahitian",
            "ug" => "Uighur",
            "uk" => "Ukrainian",
            "ur" => "Urdu",
            "uz" => "Uzbek",
            "ve" => "Venda",
            "vi" => "Vietnamese",
            "vo" => "Volapük",
            "wa" => "Walloon",
            "wo" => "Wolof",
            "xh" => "Xhosa",
            "yi" => "Yiddish",
            "yo" => "Yoruba",
            "za" => "Zhuang",
            "zh" => "Chinese",
            "zu" => "Zulu"
        ];
    }

    /**
     * Load alerts when needed
     *
     * @param string $msg
     *  The message you want to show
     * @param string $module
     *  The module name you want to show
     * @return void
     */
    public function loadAlerts($msg, $module = "user")
    {
        $status = "success";
        $icon = "check-circle";

        switch ($msg) {
            case "{$module}_created":
                $msg = "{$module} has been created";
                break;
            case "{$module}_updated":
                $msg = "{$module} has been updated";
                break;
            case "{$module}_deleted":
                $msg = "{$module} has been deleted";
                break;
            case "{$module}_enabled":
                $msg = "{$module} has been enabled";
                break;
            case "{$module}_disabled":
                $msg = "{$module} has been disabled";
                break;
            case "csrf":
                $msg = "CSRF token is invalid.";
                $status = "danger";
                $icon = "times-circle";
                break;
            case "forbidden":
                $msg = "Sorry, but you can't delete this {$module}!";
                $status = "danger";
                $icon = "times-circle";
                break;
            case "error":
                $msg = "An unexpected error has occurred";
                $status = "danger";
                $icon = "times-circle";
                break;
        }

        echo $this->alert($msg, $status, $icon);
    }

    public function module_exist($module)
    {
        return file_exists(APP_PATH . "modules/$module/main.php");
    }
}
