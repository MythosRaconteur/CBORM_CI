<?php
/**
 * Generic CB Object
 *
 * Superclass for all CB classes.  For now, does nothing more than provide basic error
 * handling.
 *
 * PHP versions 4 and 5
 *
 * @category   DAO
 * @author     Christopher Burns <cburns@mythosproductions.com>
 * @copyright  2006 Mythos Productions
 * @version    1.0
 */

 class CBObject {

 	/*******************************************************************************************
	**
	**	ERROR HANDLING
	**
	*******************************************************************************************/
	/**
	 * _addError - add an object error to the internal queue
	 * @param string - error text to add to the queue
	 * @return none
	 * @uses _errorMsg
	 */
	function _addError($msg)
	{
		if (!empty($msg)) {
			$this->_errorMsg[] = $msg;
		}
	}

	/**
	 * get_objectErrors - returns the error queue
	 * @param bool as_array - if true, the raw queue array is returned, if false, a <br>\n joined string is returned
	 * @return mixed - see as_array parameter
	 * @uses _errorMsg
	 */
	function get_objectErrors($as_array = false)
	{
		if ($as_array) {
			return $this->_errorMsg;
		}
		else {
			if (!empty($this->_errorMsg)) {
				return implode("<br>\n", $this->_errorMsg);
			}
		}
	}

	/**
	 * hasErrors - returns true if there are any messages in the internal object error queue
	 * @return bool - true if errors, false if none
	 * @uses _errorMsg
	 */
	function hasErrors()
	{
		if (empty($this->_errorMsg))
			return false;
			else return true;
	}


	/******************************************************************************
 	**
 	**	TEST FRAMEWORK
 	**
 	*****************************************************************************/
 	function alert($method, $vars) {
 		$output = "<p><font face='Arial'>In class " . strtoupper(get_class($this)) . "->" . $method . "<ul>";
 		foreach ($vars as $var=>$value) {
 			$output .= "<li>$var = " . $value->_displayString();
 		}

 		$output .= "</ul></font>";

 		echo $output;
 	}
}
?>
