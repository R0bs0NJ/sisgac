<?php


class conexaoModel {

    private $mysqli;

    public function __construct() {

        $this->mysqli = new mysqli('localhost', 'root', '', 'bd_ac');

        if ($this->mysqli->connect_errno) {
            die("Failed to connect to MySQL: (" . $this->mysqli->connect_error . ") " . $this->mysqli->connect_error);
        }

        if (!$this->mysqli->set_charset("utf8")) {
            printf("Error loading character set utf8: %s\n", $this->mysqli->error);
        }
    }

    public function getConexao() {
        return $this->mysqli;
    }

}
