<?php

/*
 * Created by Fabián Doñaque
 * Company Fabs Robotics
 * www.fabsrobotics.com
 * 12/12/19
*/

class Response {
    public function __construct($code,$data){
        $this->code = $code;
        $this->data = $data;
    }
}

class FabsPDO {
	private $pdo;
	private $salt;
	public $encryptor;

	public function __construct($dbname,$user,$password,$salt) {
		$dsn = "mysql:host=localhost;dbname=donaque_".$dbname.";charset=utf8mb4";
    	$options = [
              PDO::ATTR_EMULATE_PREPARES   => false, // turn off emulation mode for "real" prepared statements
              PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, //turn on errors in the form of exceptions
              PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //make the default fetch be an associative array
            ];
        $this->pdo = new PDO($dsn, "donaque_".$user, $password, $options);
        $this->salt = $salt;
        $this->encryptor  = "AES_ENCRYPT(?,'$salt')";
	}

	// Procedure methods
	public function decrypt($value){
        $salt = $this->salt;
        return "AES_DECRYPT($value,'$salt')";
    }

    public function returnNothing($query,$params = array()){
        try{
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            return new Response(1,'Ok');

        } catch(Exception $e){
            error_log($e);
            return new Response(0,'Ha fallado la base de datos');
        }
    }

    public function returnId($query,$params = array()){
        try{
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            $id = $this->pdo->lastInsertId();
            return new Response(1,$id);

        } catch(Exception $e){
            error_log($e);
            return new Response(0,'Ha fallado la base de datos');
        }
    }

    public function returnRowCount($query,$params = array()){
        try{
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            return new Response(1,$stmt->rowCount());

        } catch(Exception $e){
            error_log($e);
            return new Response(0,'Ha fallado la base de datos');
        }
    }

    public function returnArray($query,$params = array()){
        try{
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);

            $result = array();
            while($row = $stmt->fetch()) {
                array_push($result,$row);
            }
            return new Response(1,$result);

        } catch(Exception $e){
            error_log($e);
            return new Response(0, 'Ha fallado la base de datos');
        }
    }

    // DB Methods
    // Data Definitions
    public function dbCreate($query,$params = array()){
        return $this->returnNothing($query,$params);
    }
    public function dbAlter($query,$params = array()){
        return $this->returnNothing($query,$params);
    }
    public function dbDrop($query,$params = array()){
        return $this->returnNothing($query,$params);
    }
    // Data Manipulations
    public function dbInsert($query,$params = array()){
        return $this->returnId($query,$params);
    }
    public function dbSelect($query,$params = array()){
        return $this->returnArray($query,$params);
    }
    public function dbUpdate($query,$params = array()){
        return $this->returnRowCount($query,$params);
    }
    public function dbDelete($query,$params = array()){
        return $this->returnRowCount($query,$params);
    }
}
