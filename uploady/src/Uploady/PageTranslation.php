<?php

namespace Uploady;

class PageTranslation
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function addTranslation($data)
    {
        $this->db->prepare("INSERT INTO pages_translation (language_id, page_id, translation) VALUES (:language, :page, :translation)");
        $this->db->bind(":language", $data['language']);
        $this->db->bind(":page", $data['page']);
        $this->db->bind(":translation", $data['translation']);
        $this->db->execute();
    }

    public function updateTranslation($data)
    {
        $this->db->prepare("UPDATE pages_translation SET translation = :translation WHERE language_id = :language AND page_id = :page");
        $this->db->bind(":language", $data['language']);
        $this->db->bind(":page", $data['page']);
        $this->db->bind(":translation", $data['translation']);
        $this->db->execute();
    }

    public function getTranslation($language, $page)
    {
        $this->db->prepare("SELECT * FROM pages_translation WHERE language_id = :language AND page = :page");
        $this->db->bind(":language", $language);
        $this->db->bind(":page", $page);
        $this->db->execute();
        return $this->db->single();
    }

    public function getTranslations()
    {
        $this->db->prepare("SELECT * FROM pages_translation");

        $this->db->execute();

        return $this->db->resultset();
    }

    public function deleteTranslation($language, $page)
    {
        $this->db->prepare("DELETE FROM pages_translation WHERE language_id = :language AND page_id = :page");
        $this->db->bind(":language", $language);
        $this->db->bind(":page", $page);
        $this->db->execute();
    }
}
