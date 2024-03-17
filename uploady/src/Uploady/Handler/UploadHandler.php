<?php

namespace Uploady\Handler;

/**
 * Class to Handle adding files to the database
 *
 * @package Uploady
 * @version 3.0.x
 * @author fariscode <farisksa79@gmail.com>
 * @license MIT
 * @link https://github.com/farisc0de/Uploady
 */
class UploadHandler
{
    /**
     * Database Object
     *
     * @var \Uploady\Database
     */
    private $db;

    /**
     * UploadHandler class constructor
     **/
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Add a file to the database
     *
     * @param string $file_id
     *  The file id
     * @param string $user_id
     *  The user id
     * @param string $file_data
     *  The file data
     * @param string $user_data
     *  The user data
     * @return bool
     *  True if the file was added successfully
     */
    public function addFile($file_id, $user_id, $file_data, $user_data, $file_settings)
    {
        $this->db->prepare(
            "INSERT INTO files (
                file_id,user_id,file_data,user_data,file_settings,uploaded_at) 
                VALUES
                (:file_id,:user_id,:file_data,:user_data,:file_settings,:uploaded_at)"
        );

        $data = json_decode($file_data);

        $this->db->bind(":file_id", $file_id, \PDO::PARAM_STR);
        $this->db->bind(":user_id", $user_id, \PDO::PARAM_STR);
        $this->db->bind(":file_data", $file_data, \PDO::PARAM_STR);
        $this->db->bind(":user_data", $user_data, \PDO::PARAM_STR);
        $this->db->bind(":file_settings", $file_settings, \PDO::PARAM_STR);
        $this->db->bind(":uploaded_at", $data->uploaddate, \PDO::PARAM_STR);

        return $this->db->execute();
    }

    /**
     * Check if a file exists in the database
     *
     * @param string $file_id
     *  The file id
     * @return bool
     *  True if the file exists
     */
    public function fileExist($file_id)
    {
        $this->db->prepare("SELECT * FROM files WHERE file_id = :id");

        $this->db->bind(":id", $file_id, \PDO::PARAM_STR);

        if ($this->db->execute()) {
            return ($this->db->rowCount()) ? true : false;
        }

        return false;
    }

    /**
     * Check if a user exists in the database
     *
     * @param string $user_id
     *  The user id
     * @return bool
     *  True if the user exists
     */
    public function userExist($user_id)
    {
        $this->db->prepare("SELECT * FROM files WHERE user_id = :id");

        $this->db->bind(":id", $user_id, \PDO::PARAM_STR);

        if ($this->db->execute()) {
            if ($this->db->execute()) {
                return ($this->db->rowCount()) ? true : false;
            }
        }

        return false;
    }

    /**
     * Get a file from the database
     *
     * @param string $file_id
     *  The file id
     * @return object
     *  The file data
     */
    public function getFile($file_id)
    {
        $this->db->prepare("SELECT * FROM files WHERE file_id = :id");

        $this->db->bind(":id", $file_id, \PDO::PARAM_STR);

        if ($this->db->execute()) {
            return $this->db->single();
        }

        return [];
    }

    /**
     * Update a file in the database
     *
     * @param string $file_id
     *  The file id
     * @param string $user_id
     *  The user id
     * @param string $file_data
     *  The file data
     * @return bool
     *  True if the file was updated successfully
     */
    public function updateFile($file_id, $user_id, $file_data)
    {
        $this->db->prepare(
            "UPDATE files SET user_id = :user_id, file_data = :file_data WHERE file_id = :file_id"
        );

        $this->db->bind(":file_id", $file_id, \PDO::PARAM_STR);
        $this->db->bind(":user_id", $user_id, \PDO::PARAM_STR);
        $this->db->bind(":file_data", $file_data, \PDO::PARAM_STR);

        if ($this->db->execute()) {
            return true;
        }

        return false;
    }

    /**
     * Update a file settings in the database
     *
     * @param string $file_id
     *  The file id
     * @param string $user_id
     *  The file data
     * @param string $file_settings
     *  The file settings
     * @return bool
     *  True if the file was updated successfully
     */
    public function updateFileSettings($file_id, $user_id, $file_settings)
    {
        $this->db->prepare(
            "UPDATE files SET file_settings = :file_settings WHERE file_id = :file_id AND user_id = :user_id"
        );

        $this->db->bind(":file_id", $file_id, \PDO::PARAM_STR);
        $this->db->bind(":user_id", $user_id, \PDO::PARAM_STR);
        $this->db->bind(":file_settings", $file_settings, \PDO::PARAM_STR);

        if ($this->db->execute()) {
            return true;
        }

        return false;
    }

    /**
     * Delete a file from the database
     *
     * @param string $file_id
     *  The file id
     * @param string $user_id
     *  The user id
     * @return bool
     *  True if the file was deleted successfully
     */
    public function deleteFile($file_id, $user_id)
    {
        $this->db->prepare("DELETE FROM files WHERE file_id = :id AND user_id = :uid");

        $this->db->bind(":id", $file_id, \PDO::PARAM_STR);
        $this->db->bind(":uid", $user_id, \PDO::PARAM_STR);

        return $this->db->execute();
    }

    /**
     * Delete a file from the database as admin
     *
     * @param string $file_id
     *  The file id
     * @return bool
     *  True if the file was deleted successfully
     */
    public function deleteFileAsAdmin($file_id)
    {
        $this->db->prepare("DELETE FROM files WHERE file_id = :id");

        $this->db->bind(":id", $file_id, \PDO::PARAM_STR);

        return $this->db->execute();
    }

    /**
     * Get all files from the database
     *
     * @return array
     *  The files data
     */
    public function getFiles()
    {
        $this->db->prepare('SELECT * FROM files');

        if ($this->db->execute()) {
            return $this->db->resultset();
        }

        return [];
    }

    /**
     * Get all files from the database by user id
     *
     * @param string $user_id
     *  The user id
     * @return array
     *  The files data
     */
    public function getFilesById($user_id)
    {
        $this->db->prepare('SELECT * FROM files WHERE user_id = :uid');

        $this->db->bind(':uid', $user_id, \PDO::PARAM_STR);

        if ($this->db->execute()) {
            return $this->db->resultset();
        }

        return [];
    }

    /**
     * Get all files from the database by user id
     *
     * @param string $user_id
     *  The user id
     * @return array
     *  The files data
     */
    public function addDownload($file_id)
    {
        $this->db->prepare("UPDATE files SET downloads = COALESCE(downloads, 0) + 1 WHERE file_id = :file_id LIMIT 1;");

        $this->db->bind(":file_id", $file_id, \PDO::PARAM_STR);

        return $this->db->execute();
    }

    /**
     * Get the number of downloads from the database
     *
     * @return int
     *  The number of downloads
     * @throws \PDOException
     *  If the prepare fails
     */
    public function getDownloadsTotal()
    {
        $this->db->prepare("SELECT downloads FROM files");

        $this->db->execute();

        $total = 0;

        foreach ($this->db->resultset() as $download) {
            $total += $download->downloads;
        }

        return $total;
    }

    /**
     * Get the latest files from the database
     *
     * @return array
     *  The files data
     * @throws \PDOException
     *  If the prepare fails
     */
    public function getLatestFiles()
    {
        $this->db->prepare("SELECT * FROM files ORDER BY uploaded_at DESC LIMIT 10");

        $this->db->execute();

        return $this->db->resultset();
    }

    /**
     * Function to know how many files
     *
     * @return int
     *  Return the number of files
     */
    public function countFiles()
    {
        $this->db->prepare("SELECT * FROM files;");

        if ($this->db->execute()) {
            return $this->db->rowCount();
        }

        return 0;
    }

    /**
     * Function to know if a file is password protected
     *
     * @param string $file_id
     *  The file id
     *
     * @return bool
     *  Return true if the file is password protected
     */
    public function isFilePasswordProtected($file_id)
    {
        $this->db->prepare("SELECT file_settings FROM files WHERE file_id = :file_id");

        $this->db->bind(":file_id", $file_id, \PDO::PARAM_STR);

        if ($this->db->execute()) {
            $data = json_decode($this->db->single()->file_settings, true);

            if (isset($data['password'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns an array with the pre-defined countries names and codes
     *
     * @return array
     *  Return an array that contains the pre-defined countries
     */
    public function getCountries()
    {
        return [
            "AF" => "Afghanistan",
            "AX" => "Aland Islands",
            "AL" => "Albania",
            "DZ" => "Algeria",
            "AS" => "American Samoa",
            "AD" => "Andorra",
            "AO" => "Angola",
            "AI" => "Anguilla",
            "AQ" => "Antarctica",
            "AG" => "Antigua And Barbuda",
            "AR" => "Argentina",
            "AM" => "Armenia",
            "AW" => "Aruba",
            "AU" => "Australia",
            "AT" => "Austria",
            "AZ" => "Azerbaijan",
            "BS" => "Bahamas",
            "BH" => "Bahrain",
            "BD" => "Bangladesh",
            "BB" => "Barbados",
            "BY" => "Belarus",
            "BE" => "Belgium",
            "BZ" => "Belize",
            "BJ" => "Benin",
            "BM" => "Bermuda",
            "BT" => "Bhutan",
            "BO" => "Bolivia",
            "BA" => "Bosnia And Herzegovina",
            "BW" => "Botswana",
            "BV" => "Bouvet Island",
            "BR" => "Brazil",
            "IO" => "British Indian Ocean Territory",
            "BN" => "Brunei Darussalam",
            "BG" => "Bulgaria",
            "BF" => "Burkina Faso",
            "BI" => "Burundi",
            "KH" => "Cambodia",
            "CM" => "Cameroon",
            "CA" => "Canada",
            "CV" => "Cape Verde",
            "KY" => "Cayman Islands",
            "CF" => "Central African Republic",
            "TD" => "Chad",
            "CL" => "Chile",
            "CN" => "China",
            "CX" => "Christmas Island",
            "CC" => "Cocos (Keeling) Islands",
            "CO" => "Colombia",
            "KM" => "Comoros",
            "CG" => "Congo",
            "CD" => "Democratic Republic of Congo",
            "CK" => "Cook Islands",
            "CR" => "Costa Rica",
            "CI" => "Cote D\"Ivoire",
            "HR" => "Croatia",
            "CU" => "Cuba",
            "CY" => "Cyprus",
            "CZ" => "Czech Republic",
            "DK" => "Denmark",
            "DJ" => "Djibouti",
            "DM" => "Dominica",
            "DO" => "Dominican Republic",
            "EC" => "Ecuador",
            "EG" => "Egypt",
            "SV" => "El Salvador",
            "GQ" => "Equatorial Guinea",
            "ER" => "Eritrea",
            "EE" => "Estonia",
            "ET" => "Ethiopia",
            "FK" => "Falkland Islands (Malvinas)",
            "FO" => "Faroe Islands",
            "FJ" => "Fiji",
            "FI" => "Finland",
            "FR" => "France",
            "GF" => "French Guiana",
            "PF" => "French Polynesia",
            "TF" => "French Southern Territories",
            "GA" => "Gabon",
            "GM" => "Gambia",
            "GE" => "Georgia",
            "DE" => "Germany",
            "GH" => "Ghana",
            "GI" => "Gibraltar",
            "GR" => "Greece",
            "GL" => "Greenland",
            "GD" => "Grenada",
            "GP" => "Guadeloupe",
            "GU" => "Guam",
            "GT" => "Guatemala",
            "GG" => "Guernsey",
            "GN" => "Guinea",
            "GW" => "Guinea-Bissau",
            "GY" => "Guyana",
            "HT" => "Haiti",
            "HM" => "Heard Island & Mcdonald Islands",
            "VA" => "Holy See (Vatican City State)",
            "HN" => "Honduras",
            "HK" => "Hong Kong",
            "HU" => "Hungary",
            "IS" => "Iceland",
            "IN" => "India",
            "ID" => "Indonesia",
            "IR" => "Islamic Republic Of Iran",
            "IQ" => "Iraq",
            "IE" => "Ireland",
            "IM" => "Isle Of Man",
            "IL" => "Israel",
            "IT" => "Italy",
            "JM" => "Jamaica",
            "JP" => "Japan",
            "JE" => "Jersey",
            "JO" => "Jordan",
            "KZ" => "Kazakhstan",
            "KE" => "Kenya",
            "KI" => "Kiribati",
            "KR" => "Korea",
            "XK" => "Kosovo",
            "KW" => "Kuwait",
            "KG" => "Kyrgyzstan",
            "KP" => "North Korea",
            "LA" => "Lao People\"s Democratic Republic",
            "LV" => "Latvia",
            "LB" => "Lebanon",
            "LS" => "Lesotho",
            "LR" => "Liberia",
            "LY" => "Libyan Arab Jamahiriya",
            "LI" => "Liechtenstein",
            "LT" => "Lithuania",
            "LU" => "Luxembourg",
            "MO" => "Macao",
            "MK" => "Macedonia",
            "MG" => "Madagascar",
            "MW" => "Malawi",
            "MY" => "Malaysia",
            "MV" => "Maldives",
            "ML" => "Mali",
            "MT" => "Malta",
            "MH" => "Marshall Islands",
            "MQ" => "Martinique",
            "MR" => "Mauritania",
            "MU" => "Mauritius",
            "YT" => "Mayotte",
            "MX" => "Mexico",
            "FM" => "Federated States Of Micronesia",
            "MD" => "Moldova",
            "MC" => "Monaco",
            "MN" => "Mongolia",
            "ME" => "Montenegro",
            "MS" => "Montserrat",
            "MA" => "Morocco",
            "MZ" => "Mozambique",
            "MM" => "Myanmar",
            "NA" => "Namibia",
            "NR" => "Nauru",
            "NP" => "Nepal",
            "NL" => "Netherlands",
            "AN" => "Netherlands Antilles",
            "NC" => "New Caledonia",
            "NZ" => "New Zealand",
            "NI" => "Nicaragua",
            "NE" => "Niger",
            "NG" => "Nigeria",
            "NU" => "Niue",
            "NF" => "Norfolk Island",
            "MP" => "Northern Mariana Islands",
            "NO" => "Norway",
            "OM" => "Oman",
            "PK" => "Pakistan",
            "PW" => "Palau",
            "PS" => "Palestinian Territory, Occupied",
            "PA" => "Panama",
            "PG" => "Papua New Guinea",
            "PY" => "Paraguay",
            "PE" => "Peru",
            "PH" => "Philippines",
            "PN" => "Pitcairn",
            "PL" => "Poland",
            "PT" => "Portugal",
            "PR" => "Puerto Rico",
            "QA" => "Qatar",
            "RE" => "Reunion",
            "RO" => "Romania",
            "RU" => "Russian Federation",
            "RW" => "Rwanda",
            "BL" => "Saint Barthelemy",
            "SH" => "Saint Helena",
            "KN" => "Saint Kitts And Nevis",
            "LC" => "Saint Lucia",
            "MF" => "Saint Martin",
            "PM" => "Saint Pierre And Miquelon",
            "VC" => "Saint Vincent And Grenadines",
            "WS" => "Samoa",
            "SM" => "San Marino",
            "ST" => "Sao Tome And Principe",
            "SA" => "Saudi Arabia",
            "SN" => "Senegal",
            "RS" => "Serbia",
            "SC" => "Seychelles",
            "SL" => "Sierra Leone",
            "SG" => "Singapore",
            "SK" => "Slovakia",
            "SI" => "Slovenia",
            "SB" => "Solomon Islands",
            "SO" => "Somalia",
            "XS" => "Somaliland",
            "ZA" => "South Africa",
            "GS" => "South Georgia And Sandwich Isl.",
            "SS" => "South Sudan",
            "ES" => "Spain",
            "LK" => "Sri Lanka",
            "SD" => "Sudan",
            "SR" => "Suriname",
            "SJ" => "Svalbard And Jan Mayen",
            "SZ" => "Swaziland",
            "SE" => "Sweden",
            "CH" => "Switzerland",
            "SY" => "Syrian Arab Republic",
            "TW" => "Taiwan",
            "TJ" => "Tajikistan",
            "TZ" => "Tanzania",
            "TH" => "Thailand",
            "TL" => "Timor-Leste",
            "TG" => "Togo",
            "TK" => "Tokelau",
            "TO" => "Tonga",
            "TT" => "Trinidad And Tobago",
            "TN" => "Tunisia",
            "TR" => "Turkey",
            "TM" => "Turkmenistan",
            "TC" => "Turks And Caicos Islands",
            "TV" => "Tuvalu",
            "UG" => "Uganda",
            "UA" => "Ukraine",
            "AE" => "United Arab Emirates",
            "GB" => "United Kingdom",
            "US" => "United States",
            "UM" => "United States Outlying Islands",
            "UY" => "Uruguay",
            "UZ" => "Uzbekistan",
            "VU" => "Vanuatu",
            "VE" => "Venezuela",
            "VN" => "Viet Nam",
            "VG" => "Virgin Islands, British",
            "VI" => "Virgin Islands, U.S.",
            "WF" => "Wallis And Futuna",
            "EH" => "Western Sahara",
            "YE" => "Yemen",
            "ZM" => "Zambia",
            "ZW" => "Zimbabwe",
            "X" => "Unknown"
        ];
    }
}
