<?php
/**
 * Abstract collection class for business objects
 *
 * Provides a collection interface to groups of business objects, as well as
 * representational breakdown of many-to-many relationships and their mapping
 * to the persistence layer.
 *
 * PHP versions 4 and 5
 *
 * @category   DAO
 * @author     Christopher Burns <chris@withoutabox.com>
 * @copyright  2006 WAB
 * @version    1.0
 */
 
 require_once("BusinessObjects/WABObject.php");
 
 class WABBusinessCollection extends WABObject {
 	/*******************************************************************************************
	** 	ATTRIBUTES
	*******************************************************************************************/
	var $_collection = array();
	var $_parentID = -1;
	var $_parentType;
	
	
	/*******************************************************************************************
	** 	FUNCTIONS
	*******************************************************************************************/
	/*******************************************************************************************
	**	MUST BE OVERRIDDEN
	*******************************************************************************************/
	function get_collectedType() {}
	function saveAssociativeData() {}
	function deleteAssociativeData() {}

	/*******************************************************************************************
	**
	**	CONSTRUCTORS
	** 
	*******************************************************************************************/
	/*****************************************************************************
	** 	NB!	This constructor is for use with the CASE Tool.  Its functionality
	**		is not guaranteed outside of that scope.
	**
	*****************************************************************************/
	function WABBusinessCollection($arr) {
		$this->_collection = $arr;
	}
	
	
	/*******************************************************************************************
	**
	**	ACCESSORS
	** 
	*******************************************************************************************/
	/*****************************************************************************
	** 	OBJECT COLLECTION (read only):
	**
	** 	GET collection as reference.
	**
	*****************************************************************************/
	function &get_objectCollection() {
		return $this->_collection;
	}

	/*****************************************************************************
	** 	PARENT ID:
	**
	** 	SET and GET parentID.
	**
	*****************************************************************************
	**	ARGUMENTS:
	**
	**	- value: The value assigned.
	**
	*****************************************************************************/	
	function get_parentID() {
		return $this->_parentID;
	}
	
	function set_parentID($value) {
		$this->_parentID = $value;
	}
	
	/*****************************************************************************
	** 	PARENT TYPE:
	**
	** 	SET and GET parentType.
	**
	*****************************************************************************
	**	ARGUMENTS:
	**
	**	- value: The value assigned.
	**
	*****************************************************************************/	
	function get_parentType() {
		return $this->_parentType;
	}
	
	function set_parentType($value) {
		$this->_parentType = $value;
	}
	
	
	/*****************************************************************************
	** 	COUNT:
	**
	** 	Return the number of elements in the collection
	**
	*****************************************************************************/	
	function count() {
		return count($this->get_objectCollection());
	}
	
	/*****************************************************************************
	** 	RESET:
	**
	** 	Clear the contents of this collection
	**
	*****************************************************************************/	
	function reset() {
		unset($this->_collection);
		$this->_collection = array();
	}
	
	/*****************************************************************************
	** 	GET ITEM AT:
	**
	** 	Return the item in the collection with the specified ID
	**
	******************************************************************************
	*	ARGUMENTS:
	*
	*	- id:	The id of the item to be found.  If item does not exist, return
	*			false
	*
	*****************************************************************************/	
	function getItemAt($id) {
		if (array_key_exists($id, $this->_collection)) {
			$col = $this->get_objectCollection();
			return $col[$id];
		}
		else {
			return false; 
		}
	}
	
	/*****************************************************************************
	** 	ADD:
	**
	** 	Add an object to the collection, keyed by the PK of the object
	**
	******************************************************************************
	*	ARGUMENTS:
	*
	*	- obj:  The object to add (by ref).  Check to be sure the object is of
	*			the correct type before adding.
	*
	*****************************************************************************/	
	function add($obj) {
		if (get_class($obj) == strtolower($this->get_collectedType())) {
			$this->_collection[$obj->get_id()] = $obj;
		}
		else {
			$this->_addError("Object to be added is of inappropriate type");			
		}
	}	
	

	/*****************************************************************************
	** 	DELETE:
	**
	** 	Remove the specified object from the collection, and delete it from the DB
	**
	******************************************************************************
	*	ARGUMENTS:
	*
	*	- obj:  A QBE object to delete
	*
	*****************************************************************************/	
	function delete($obj) {
		$col = $this->get_objectCollection();
		
		if ($this->item($obj->get_id())) {
			unset($col[$obj->get_id()]);
			$this->deleteAssociativeData($obj);
			$obj->delete();
		}
		else {
			$this->_addError("Object to be deleted does not exist in collection");
		}
	}
	
	
	/*****************************************************************************
	** 	DELETE ALL:
	**
	** 	Remove all objects from the collection, and delete them from the DB
	**
	*****************************************************************************/	
	function deleteAll() {
		$col = $this->get_objectCollection();
		
		foreach ($col as $key=>$val) {
			$this->deleteAssociativeData($val);
			$val->delete();
		}
		
		$this->reset();
	}
	
	
	/*****************************************************************************
	** 	LOAD BY EXAMPLE:
	**
	** 	Populate with all objects that match the criteria specified by the arg
	**	object
	**
	******************************************************************************
	*	ARGUMENTS:
	*
	*	- obj:  An example object
	*
	*****************************************************************************/	
	function loadByExample($obj, $reset=true) {
		if ($reset) {
			$this->reset();
		}

		$broker = $obj->get_dataBroker();
		$results = $broker->retrieve($obj);
				
		foreach ($results as $val) {
			$this->add($val);
		}
		echo "<p>In BusinessCollection->loadByExample: \$this has " . $this->count() . " items<br>";

	}
	
	
	/*****************************************************************************
	** 	DETECT:
	**
	** 	Search for the specified instance and return TRUE if found, FALSE if not
	**
	******************************************************************************
	*	ARGUMENTS:
	*
	*	- obj:  An example object
	*
	*****************************************************************************/	
	function detect($obj) {
		$result = false;
		$col = $this->get_objectCollection();
		
		foreach ($col as $key=>$val) {
			if ($obj->equals($val)) {
				$result = true;
				break;
			}
		}
		
		return $result;
	}
	

	/*****************************************************************************
	** 	SELECT:
	**
	** 	Return the first object in the collection matching obj, or FALSE if not
	*	found
	**
	******************************************************************************
	*	ARGUMENTS:
	*
	*	- obj:  An example object
	*
	*****************************************************************************/	
	function select($obj) {
		$result = false;
		$col = $this->get_objectCollection();
		
		foreach ($col as $key=>$val) {
			if ($obj->equals($val)) {
				$result = $val;
				break;
			}
		}
		
		return $result;
	}
	
	
	/*****************************************************************************
	** 	COLLECT:
	**
	** 	Return all objects in the collection matching obj in a new collection of
	*	this type
	**
	******************************************************************************
	*	ARGUMENTS:
	*
	*	- obj:  An example object
	*
	*****************************************************************************/	
	function collect($obj) {
		$cls = get_class($this);
		$result = new $cls;
		
		$col = $this->get_objectCollection();
		
		foreach ($col as $key=>$val) {
			if ($obj->equals($val)) {
				$result->add($val);
			}
		}
		
		return $result;
	}
	
	function _displayString() {
		$str = "<font face='Arial'>" . get_class($this) . ":<ul>";
		foreach ($this->get_objectCollection() as $key=>$val) {
			$str .= "<li>" . $key . " => " . $val->_displayString();
		}
		
		$str .= "</ul></font>";
		
		return $str;
	}
 }
?>
