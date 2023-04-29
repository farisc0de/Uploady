<?php

namespace Farisc0de\PhpMigration;

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
}
