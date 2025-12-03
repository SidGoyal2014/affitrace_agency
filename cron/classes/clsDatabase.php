<?php

class database{

    // declare and set our variables to a default (empty) state
    var $con=null;
    var $recordset=null;
    var $result=null;
    var $database_name=null;
    var $dbError=null;

    // constructor - makes the connection, sets the database
    function __CONSTRUCT($hostname,$username,$password,$database_name){

        $this->con= mysqli_connect($hostname,$username,$password, $database_name)
            or $this->dbError ='Connection error: '.mysqli_connect_errno(); 

    }

    
	function insert($table, $fields){
	
		$keys = array();
		$vals = array();

		$keys = array_keys($fields);
		$vals = array_values($fields);

		$sql = 'insert into '.$table.' set ';

		$arrIndex = 0;
		while($arrIndex < count($keys)){
		
			$sql .= $keys[$arrIndex] .'="'. $this->realEscapeString($vals[$arrIndex]).'",';

			$arrIndex++;

		}//while

		$sql = substr($sql,0,-1);

		//echo "<br>------>>>".$sql."<br>";die;

		mysqli_query($this->con, $sql) or $this->dbError = '<br><B>Insert Query Failed</B><br><br>'.$sql.'<br><br>'.mysqli_error($this->con).'<br>';
		return $this->lastInsertId();

	}//insert



	function update($table, $fields, $whereClause){

		$keys = array();
		$vals = array();

		$keys = array_keys($fields);
		$vals = array_values($fields);

		$sql = 'update '.$table.' set ';

		$arrIndex = 0;
		while($arrIndex < count($keys)){

			$sql .= $keys[$arrIndex] .'="'. $this->realEscapeString($vals[$arrIndex]).'",';

			$arrIndex++;

		}//while

		$sql = substr($sql,0,-1);

		$sql .= $whereClause;
//echo $sql; die;
		mysqli_query($this->con, $sql) or $this->dbError = '<br><B>Update Query Failed</B><br><br>'.$sql.'<br><br>'.mysqli_error($this->con).'<br>';

		return true;

	}//update


	function deleteQuery($sql){
	
		mysqli_query($this->con, $sql);
		
	}//deleteQuery

	function query($sql){

		$recordset = mysqli_query($this->con, $sql) or $this->dbError = mysqli_error($this->con);
		return $recordset;

	}//selectQuery

	function traverseResultSet($recordset){

		$rec = mysqli_fetch_array($recordset);
		return $rec;
	
	}//traverseResultSet


	function lastInsertId(){
	
		return mysqli_insert_id($this->con);

	}//lastInsertId

	function realEscapeString($field){
	
		return mysqli_real_escape_string($this->con, $field);

	}//realEscapeString


	function numRows($recordset){
	
		return @mysqli_num_rows($recordset);

	}//numRows

	function affectedRows(){
		return $this->con->affected_rows;
	}//affectedRows


	function getFieldValue($table, $field, $where = ''){

		$whereClause = '';

		if($where != ''){
			$whereClause = ' where '.$where;
		}

		$sql = 'select '.$field.' from '.$table.$whereClause;
		$recordset = $this->query($sql);
		$rec = $this->traverseResultSet($recordset);
		return $rec[$field];

	}//getFieldValue


}// End of class



// instantiate the object, pass connection parameters, database name


$dbObject = new database('localhost','root','','affitrace');

//$dbObject= new database('localhost','eurek8d7_common','common#123?','eurek8d7_affitrace_adsfinemedia');
?>