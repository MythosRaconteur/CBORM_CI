<?php
/**
 * Basic CBBusinessObject
 *
 * This ABSTRACT class provides abstraction from which all CB business objects
 * should inherit.
 *
 * PHP versions 4 and 5
 *
 * @category   DAO
 * @author     Christopher Burns <cburns@mythosproductions.com>
 * @copyright  2006 Mythos Productions
 * @version    1.0
 */

 require_once("DataBrokers/CBDataBroker.php");

 class CBBusinessObject extends CI_Model {
 	/*******************************************************************************************
	** 	ATTRIBUTES
	*******************************************************************************************/
	var $_id;


	/*******************************************************************************************
	** 	FUNCTIONS
	*******************************************************************************************/
	/*******************************************************************************************
	**
	**	ACCESSORS
	**
	*******************************************************************************************/
	/*****************************************************************************
	** 	ID:
	**
	** 	SET and GET id.
	**
	******************************************************************************
	**	ARGUMENTS:
	**
	**	- value: The value assigned.
	**
	*****************************************************************************/
	function get_id(){
		return $this->_id;
	}

	function set_id($value){
		$this->_id = $value;
	}

	function get_collectionType() {
		// Default method for determining the collection type.
		// This is usually overridden by the CASE tool to accomodate
		// user input at the generation stage.

		$cls = get_class($this);
		return ucfirst($cls . "s");
	}

	/*****************************************************************************
	** 	DATA BROKER:
	**
	** 	READ-ONLY GET dataBroker as convenience method.
	**
	*****************************************************************************/
	function get_dataBroker(){
		return CBDataBroker::get_brokerFor($this);
	}

	/*******************************************************************************************
	**
	**	CONSTRUCTORS
	**
	*******************************************************************************************/
	function CBBusinessObject($myID = -1){
		$this->set_id($myID);
	}

	function initialize() {}

	/*******************************************************************************************
	**
	**	TESTING
	**
	*******************************************************************************************/
	function is_new(){
		return $this->get_id() == -1;
	}

	/*****************************************************************************
	**	_EQUALS
	**
	**	- Comare this instance to the argument object for equality
	**
	******************************************************************************
	**	ARGUMENTS:
	**
	**	- anObj: any object for comparison
	**
	*****************************************************************************/
	function _equals($anObj) {
		if (get_class($this) != get_class($anObj)) {
			return false;
		}

		return true;
	}

 	/******************************************************************************
 	**
 	**	DATA ACCESS
 	**
 	 *****************************************************************************/
 	 function save() {
 	 	$broker = $this->get_dataBroker();
 	 	$broker->save($this);
 	 }

 	 function delete() {
 	 	$broker = $this->get_dataBroker();
 	 	$broker->delete($this);
 	 }
 }
?>
