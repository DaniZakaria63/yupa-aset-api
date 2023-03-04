<?php

require_once PROJECT_ROOT_PATH . '/model/Database.php';

class AsetModel extends Database{
    public function getAll($limit = 0){
        return $this->select("SELECT * FROM asset ORDER BY id ASC LIMIT ?",['i',[$limit]]);
    }

    public function findOne($key){
        return $this->select("SELECT * FROM asset WHERE file_name_hash = ? LIMIT 1", ['s',[$key]]);
    }

    public function insertOne($value){
        return $this->insert("INSERT INTO asset(
            account_id, file_name, file_name_ori, file_name_hash, extension, directory, file_size, output_type, output_ext, output_size, output_desc, date_created, date_updated)
            VALUES
            (?,?,?,?,?,?,?,?,?,?,?,?,?)
            ",$value);
    }
}