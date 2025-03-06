<?php

trait Database {

    private function connect(){
        $string = "mysql:hostname=".DBHOST."; dbname=".DBNAME;
        $con =  new PDO($string,DBUSER,DBPASS);
        return $con;

    }

    public function query($query,$data=[]){

        $con = $this->connect();
        $stmt = $con->prepare($query);

        $check  = $stmt->execute($data);
        if($check){
            $results  = $stmt->fetchAll(PDO::FETCH_OBJ);
            if(is_array($results) && count($results)){
                return  $results;

            }
        }
        return  false;
    }

    public function get_row($query,$data=[]){

        $con = $this->connect();
        $stmt = $con->prepare($query);

        $check  = $stmt->execute($data);
        if($check){
            $results  = $stmt->fetchAll(PDO::FETCH_OBJ);
            if(is_array($results) && count($results)){
                return  $results[0];

            }
        }
        return  false;
    }

}


