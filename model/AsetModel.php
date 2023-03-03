<?php

require_once PROJECT_ROOT_PATH . '/model/Database.php';

class AsetModel extends Database{
    public function getAll($limit = 0){
        return $this->select("SELECT * FROM asset ORDER BY id ASC LIMIT ?",['i',[$limit]]);
    }
}