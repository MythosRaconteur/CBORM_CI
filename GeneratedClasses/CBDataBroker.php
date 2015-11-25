<?php
/**
 * Basic WAB Data Broker
 *
 * This essentially ABSTRACT class provides abstraction from which all WAB
 * data broker objects should inherit.  It also serves as a broker locator
 * by virtue of the class-side method #get_brokerFor
 *
 * PHP versions 4 and 5
 *
 * @category   DAO
 * @author     Christopher Burns <chris@withoutabox.com>
 * @copyright  2006 WAB
 * @version    1.0
 */
 
 require_once("./Include/singleton.php");
 require_once("./FFDBI.php");
 require_once("./BusinessObjects/WABObject.php");
 
 class WABDataBroker extends WABObject {
 
  	/*******************************************************************************************
	** 	ATTRIBUTES
	*******************************************************************************************/
	var $_exact;
	
	
	/*******************************************************************************************
	** 	FUNCTIONS
	*******************************************************************************************/
	/*******************************************************************************************
	**
	**	ACCESSORS
	** 
	*******************************************************************************************/
	/*****************************************************************************
	** 	EXACT:
	**
	** 	SET and GET exact.
	**
	******************************************************************************
	**	ARGUMENTS:
	**
	**	- value: The value assigned.
	**
	*****************************************************************************/	
	function get_exact(){
		return $this->_exact;
	}
	
	function set_exact($value){
		$this->_exact = $value;
	}
	
	
 	function get_brokerFor($anInst) {
 		static $brokerDictionary = array();
 		
 		$cls = ucfirst(get_class($anInst));
 		
 		// #is_a deprecated in v4 in favor of #instanceof.
 		// Should be - !($brokerDictionary[$cls] instanceof $cls) 
 		if (!array_key_exists($cls, $brokerDictionary) || !is_a($brokerDictionary[$cls], $cls)) {
			$broker = $cls . "DataBroker";
			$brokerDictionary[$cls] = &new $broker;
 		} 

		return $brokerDictionary[$cls];
 	}

	/*************************************************************
	** MUST BE OVERRIDDEN
	*************************************************************/
//	abstract function get_tableName(){}	
//	abstract function get_pkColumnName() {}	
//	abstract function get_classServed() {}
//	abstract function findUniqueSQL() {}
//	abstract function get_whereClause() {}
//  abstract function orMap() {}
 	 	
 	/***************************************************************************************
 	 *	FACTORY METHODS 
 	 **************************************************************************************/
 	function make_object() {
 		$cls = $this->get_classServed();
 		$inst = new $cls;
 		return $inst;
 	}

 	function get_object($id) {
 		$obj = $this->make_object();
 		$obj->set_id($id);
 		$result = $this->retrieve($obj);
 		
 		if (count($result) > 0) {
 			return $result[$id];
 		}
 	}
 	
 	function retrieve($obj) {
 		$sql = $this->selectSQL($obj);
 		
 		print "<p>SQL statement = <br>$sql";
 		
 		$dbResult = MSSQL_QUERY($sql);

 		$result = array();
 		
 		while ($row = MSSQL_FETCH_ARRAY($dbResult)) {
 			$obj = $this->make_object();
 			$this->_load_fromArray($obj, $row);
 			$result[$obj->get_id()] = $obj;
 		}

 		return $result;
 	}
 	
 	/*************************************************************************************
 	 *	DATA ACCESS METHODS
 	 ************************************************************************************/
 	function save($anInst) {
 		if ($anInst->is_new()) {
 			$this->_db_exec($this->insertSQL($anInst));
 		}
 		else {
 			$this->_db_exec($this->updateSQL($anInst));
 		}
 	}
 	
 	function delete($anInst) {
 		$this->_db_exec($this->deleteSQL($anInst));
 	}
 	
 	/*************************************************************************************
 	 *	SQL STATEMENTS
 	 ************************************************************************************/
 	function selectSQL($anInst, $exact=false) {
 		$this->set_exact($exact);
 		
 		$sql = 'SELECT * FROM ' . $this->get_tableName();
        $wc = $this->get_whereClause($anInst);
        
        if (strlen($wc) > 0) {
            $sql .= ' WHERE ' . $wc;
        }
        
        return $sql;        
 	}
 	
 	function insertSQL($anInst) {
 		$sql = 'INSERT INTO ' . $this->get_tableName() . '(';
 		
 		$colMap = $this->orMap();
 		
 		foreach(array_keys($colMap) as $key) {
 			if ($key == '_id') {
	  			$val = $this->_gen_newID();
	  		}
 			else {
 				$str = 'return $anInst->get' . $key . '();';
	 			$val = eval($str);	
 			}
 			
	 		$cols .= $colMap[$key] . ',';
	 		
	 		if ($key == '_create_date') {
	 			$vals .= 'sysdate' . ',';
	 		}
	 		else {
	 			$vals .= $this->_prepareText($val) . ',';
	 		}
 		}
 		
 		// Tear off the trailing comma
 		$cols = substr($cols, 0, -1);
 		$vals = substr($vals, 0, -1);
 		
 		$sql .= $cols . ') VALUES(' . $vals . ')'; 		
 		 		
 		return $sql;
 	}
 	
 	function updateSQL($anInst) {
 		$sql = 'UPDATE ' . $this->get_tableName() . ' SET ';
 		
 		$colMap = $this->orMap();
 		
 		foreach(array_keys($colMap) as $key) {
 			$sql .= $colMap[$key] . '=';
 			$str = 'return $anInst->get' . $key . '();';
 			$val = eval($str);
 			$sql .= $this->_prepareText($val) . ',';
 		}
 		
 		// Tear off the trailing comma
 		$sql = substr($sql, 0, -1);
 		
 		$sql .= ' WHERE ' . $this->get_pkColumnName() . '=' . $anInst->get_id();
 		
 		return $sql;
 	}
 	
 	function deleteSQL($anInst) {
 		$sql = 'DELETE FROM  ' . $this->get_tableName();
		$sql .= ' WHERE ' . $this->get_pkColumnName() . '=' . $anInst->get_id();
		
		return $sql;
 	}
 
 	
 	/*************************************************************************************
 	 *	PRIVATE METHODS
 	 ************************************************************************************/
 	function _db_exec($aString) {
 		$db = new dataBase();
 		$dbh = $db->connect(); 		
 		$stmt = ociparse($dbh, $aString);
 		ociexecute($stmt);
		OCIFreeStatement($stmt);		
 	}
 	
 	function _gen_newID() {
 		$db = new dataBase();
 		$result = $db->select('SELECT ' . $this->get_pkColumnName() . '.nextval FROM DUAL');
 		return $result[0]['NEXTVAL'];
 	}
 	
 	function _load_fromArray(&$anInst, $anArray) {
 		$colMap = $this->orMap();
 		$ucArray = array_change_key_case($anArray, CASE_UPPER);
 		
 		foreach(array_keys($colMap) as $key) {
 			$resKey = strtoupper($colMap[$key]);
 			$resVal = $ucArray[$resKey];
 			$str = '$anInst->set' . $key . '("' . addslashes($resVal) . '");';

 			eval($str);
 		} 		
 	}
 	
 	function _processExact($col, $var, $asNull=false, $withWildCards=false) {
 		$output = "";
 		
 		if (is_array($var)) {
 			$vals = $var;
 			$output .= "(";
 			$this->_buildWhereClause($col, $vals, $asNull, $withWildCards, &$output);
 			$output .= ")";
 		}
 		else {
 			$vals[0] = $var;
 			$this->_buildWhereClause($col, $vals, $asNull, $withWildCards, &$output);
 		}
 		
 		return $output;
 	}
 	
 	function _buildWhereClause($col, $anArray, $asNull, $withWildCards, $output) { 		
 		foreach ($anArray as $value) { 		
	 		if ($this->get_exact()) {
	        	$output .= $col . ' = ' . $this->_prepareText($value);
	 		}
	        else {
	            if (strlen(trim($value)) > 0) {
	                if ($withWildCards) {
	                    // Check to see if only a wild card was entered if so then also allow nulls.
	                    if (trim($value) == "*") {
	                        $output .= "(" . $col . " LIKE '" . str_replace("*", "%", trim($value)) . "' OR " . $col . " IS NULL)";
	                    }
	                    else {
	                        $output .= $col . " LIKE '" . str_replace("*", "%", trim($value)) . "' ";
	                	}
	                }
	                else {
	                    $output .= $col . " LIKE '" . trim($value) . "%' ";
	                }
	            }
	            else {
	                if (isset($value) and !is_null($value)) {
	                    $output .= "(" . $col . " LIKE '" . trim($value) . "%' OR " . $col . " IS NULL)";
	                }
	                else {
	                    if ($asNull) {
	                    	$output .= "(" . $col . " IS NULL)";
	                    }
	                }
	           	}
	        }
	        
	        $output .= " OR "; 
        }
        
        $output = substr($output, 0, -4);
 	}
 	
 	function _prepareText($str) {
 		if (strlen(trim($str)) == 0) {
            $output = 'NULL';
 		}
        else {
            $newVal = str_replace("'", "''", trim($str));
            $output = "'" . $newVal . "'";
        }
        
        return $output;
 	}
 	
 	function _searchByIdSQL($obj) {
 		$str = "";
 		
 		if ($obj->get_id() > 0) {
 			$str = $this->get_pkColumnName() . "=" . $obj->get_id();
 		}

		return $str;
 	}
}
?>
