<?php

class Settings {
    private $com_alfredwesterveld_Settings = array();

    public function  __construct() {
        $this->com_alfredwesterveld_Settings = array(
            "db" => new PDO('sqlite:database/login.sqlite3')
        );
    }

    public function get($name) {
        if (isset($this->com_alfredwesterveld_Settings[$name])) {
            return $this->com_alfredwesterveld_Settings[$name];
        }
        return NULL;
    }
}