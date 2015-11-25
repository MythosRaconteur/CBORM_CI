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
** 	Class UserDataBroker manages data transfer between table USER
**  and the Business Object, User.
**
** 		- Created by CBObjectGenerator v1.2
**
*********************************************************************************************/


require_once("../DataBrokers/CBDataBroker.php");
require_once("../BusinessObjectsUser.php");

class UserDataBroker extends CBDataBroker {

	/*****************************************************************************************
	**	OVERRIDDEN FUNCTIONS
	*****************************************************************************************/
	/*****************************************************************************
	**	ORMAP:
	**
	**	A dictionary mapping of relational data to object data where
	**	KEY = variable and VALUE = DB column
	**
	*****************************************************************************/
	function orMap() {
		return array (
			'_id'			=>	'id',
			'_name'			=>	'name',
			'_alias'		=>	'alias',
			'_email'		=>	'email',
			'_password'		=>	'password',
			'_created_at'	=>	'created_at',
			'_updated_at'	=>	'updated_at'
		);
	}

	/*****************************************************************************
	**	GET_TABLENAME:
	**
	**	Accessor for the table this object sits atop.
	**
	*****************************************************************************/
	function get_tableName() {
		return 'User';
	}


	/*****************************************************************************
	**	GET_PKCOLUMNNAME:
	**
	**	Accessor for the name of the primary key column in table USER.
	**
	*****************************************************************************/
	function get_pkColumnName() {
		return 'ID';
	}


	/*****************************************************************************
	**	GET_CLASSSERVED:
	**
	**	Accessor for the name of the class this broker services.
	**
	*****************************************************************************/
	function get_classServed() {
		return 'User';
	}


	/*****************************************************************************
	**	GET_WHERECLAUSE:
	**
	**	Build a SQL WHERE clause based on data contained in argument object.
	**
	******************************************************************************
	**	ARGUMENTS:
	**
	**	- anInst: An instance of class User. 
	**
	*****************************************************************************/
	function get_whereClause($anInst) {
		$wc = '';

		if (strlen($this->_searchByIdSQL($anInst)) > 0) {
			$wc .= $this->_searchByIdSQL($anInst);
		}
		else {
			if (strlen($this->_searchByIdSQL($anInst)) > 0) {
				if (strlen($wc) > 0) {
					$wc .= ' AND ';
				}

				$wc .= $this->_searchByIdSQL($anInst);
			}

			if (strlen($this->_searchByNameSQL($anInst)) > 0) {
				if (strlen($wc) > 0) {
					$wc .= ' AND ';
				}

				$wc .= $this->_searchByNameSQL($anInst);
			}

			if (strlen($this->_searchByAliasSQL($anInst)) > 0) {
				if (strlen($wc) > 0) {
					$wc .= ' AND ';
				}

				$wc .= $this->_searchByAliasSQL($anInst);
			}

			if (strlen($this->_searchByEmailSQL($anInst)) > 0) {
				if (strlen($wc) > 0) {
					$wc .= ' AND ';
				}

				$wc .= $this->_searchByEmailSQL($anInst);
			}

			if (strlen($this->_searchByPasswordSQL($anInst)) > 0) {
				if (strlen($wc) > 0) {
					$wc .= ' AND ';
				}

				$wc .= $this->_searchByPasswordSQL($anInst);
			}

			if (strlen($this->_searchByCreatedAtSQL($anInst)) > 0) {
				if (strlen($wc) > 0) {
					$wc .= ' AND ';
				}

				$wc .= $this->_searchByCreatedAtSQL($anInst);
			}

			if (strlen($this->_searchByUpdatedAtSQL($anInst)) > 0) {
				if (strlen($wc) > 0) {
					$wc .= ' AND ';
				}

				$wc .= $this->_searchByUpdatedAtSQL($anInst);
			}

		}

		return $wc;

	}
	/*****************************************************************************************
	**	PRIVATE FUNCTIONS
	*****************************************************************************************/
	function _searchByNameSQL($anInst) {
		$str = $this->_processExact('name', $anInst->get_name());

		return $str;
	}

	function _searchByAliasSQL($anInst) {
		$str = $this->_processExact('alias', $anInst->get_alias());

		return $str;
	}

	function _searchByEmailSQL($anInst) {
		$str = $this->_processExact('email', $anInst->get_email());

		return $str;
	}

	function _searchByPasswordSQL($anInst) {
		$str = $this->_processExact('password', $anInst->get_password());

		return $str;
	}

}

?>
