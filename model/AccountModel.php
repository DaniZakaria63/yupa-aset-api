<?php

require_once PROJECT_ROOT_PATH . '/model/Database.php';

class AccountModel extends Database{

    public function find($id){
        return $this->select("SELECT * FROM account WHERE id = ?",['i',[$id]]);
    }

    public function findByKey($key){
        return $this->select("SELECT * FROM account WHERE access_key = ? LIMIT 1", ['i',[$key]]);
    }
}