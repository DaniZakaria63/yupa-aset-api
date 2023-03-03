<?php

class Database{
    protected $connection = null;

    public function __construct()
    {
        try{
            if(!function_exists('mysqli_init') && !extension_loaded('mysqli')){
                throw new Exception("Module mysqli tidak ada");
            }
            $this->connection = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_ASET_TRX);

            if(mysqli_connect_errno()) throw new Exception("Tidak bisa menyambungkan database trx, #1");

        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    public function select($query = '', $params = []){
        try{
            $stmt = $this->executeStatement($query, $params);
            $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            return $result;
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
        return false;
    }

    public function executeStatement($query = '', $params = []){
        
        try{
            $stmt = $this->connection->prepare( $query );

            if($stmt === false) throw new Exception('Tidak dapat melakukan eksekusi statement, #2');

            if($params) $stmt->bind_param($params[0], $params[1]);

            $stmt->execute();

            return $stmt;

        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }
}

?>