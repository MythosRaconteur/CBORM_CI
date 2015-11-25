<?php

/*********************************************************************************************
**
** 	@author		:	CB <cburns@codingdojo.com>
** 	@created  	:	11.23.15
** 	@version	:	1.0.0
**	@company	:	Coding Dojo
**
**********************************************************************************************
**
** 	Class Users represents a group of business objects, and serves
**	to model associative entities for the breaking up of many-to-many relationships, the
**	capture of pertinent data therein, and the interface to the persisting of these
**	relationships.
**
** 		- Created by CBObjectGenerator v1.2
**
*********************************************************************************************/


require_once("../BusinessCollections/CBBusinessCollection.php");

class Users extends CBBusinessCollection {

	/*****************************************************************************************
	**	FUNCTIONS
	*****************************************************************************************/
	function get_collectedType() {
		return 'User';
	}

	function saveAssociativeData() {
		
	}

	function deleteAssociativeData() {
		
	}
}

?>
