<?php
class Connection {

    private $connection;
    private $host;
    private $user;
    private $password;

    /**
     * @param type $host
     * @param type $user
     * @param type $password
     * @param type $db
     */
    public function __construct($host, $user, $password, $db) {
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
        $this->db = $db;
        $this->connection = new mysqli($this->host, $this->user, $this->password, $this->db);
        $this->connection->set_charset("utf8");

        if ($this->connection->connect_errno) {
            throw new \Exception('Connect to DB failed!');
        }
    }

    /**
     * 
     * @param type $sql
     * @return Object
     */
    public function query($sql) {
        return $this->connection->query($sql);
    }
    
    public function real_escape_string($string) {
        return $this->connection->real_escape_string($string);
    }

}
