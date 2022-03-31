<?php

/*
 * Created by Fabián Doñaque
 * Company Fabs Robotics
 * www.fabsrobotics.com
 * 12/12/19
*/

class Response {
	public $ok = 0;
	public $clientError = 1;
	public $serverError = 2;
	public $unAuthorized = 3;
	
    public function __construct($code,$data){
        $this->code = $code;
        $this->data = $data;
    }
}

class FabsPDO {
	private $pdo;
	private $salt;
	public $encryptor;

	public function __construct($host,$dbname,$user,$password,$salt) {
		$dsn = "mysql:host=".$host.";dbname=".$dbname.";charset=utf8mb4";
    	$options = [
              PDO::ATTR_EMULATE_PREPARES   => false, // turn off emulation mode for "real" prepared statements
              PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, //turn on errors in the form of exceptions
              PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //make the default fetch be an associative array
            ];
        $this->pdo = new PDO($dsn, $user, $password, $options);
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
            return new Response(Response.ok,'Ok');

        } catch(Exception $e){
            error_log($e);
            return new Response(Response.serverError,'Database operation failed.');
        }
    }

    public function returnId($query,$params = array()){
        try{
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            $id = $this->pdo->lastInsertId();
            return new Response(Response.ok,$id);

        } catch(Exception $e){
            error_log($e);
            return new Response(Response.serverError,'Database operation failed.');
        }
    }

    public function returnRowCount($query,$params = array()){
        try{
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            return new Response(Response.ok,$stmt->rowCount());

        } catch(Exception $e){
            error_log($e);
            return new Response(Response.serverError,'Database operation failed.');
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
            return new Response(Response.ok,$result);

        } catch(Exception $e){
            error_log($e);
            return new Response(Response.serverError, 'Database operation failed.');
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
