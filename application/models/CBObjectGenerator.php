<?php

class CBObjectGenerator extends CI_Model {
	/*******************************************************************************************
	** 	ATTRIBUTES
	*******************************************************************************************/
	var $_company;
	var $_author;
	var $_email;
	var $_dataArray = array();
	var $errorMsg;

	/*******************************************************************************************
	** 
	**	ENTRY POINTS
	**
	********************************************************************************************
	******************************************************************************
	** 	GENERATE TRIPLET FOR:
	**
	** 	Create a BusinessObject, a BusinessCollection, and a DataBroker for the
	**	table(s) specified.  This uses the default schema for object and variable
	**	naming.
	**
	******************************************************************************
	**	ARGUMENTS:
	**
	**	- table:	Can be either a string or an array containing the name(s) of
	**				tables to process
	**	- path:		The location to store the output files
	**
	*****************************************************************************/
	function generateTripletFor($table,$path) {
		if (!$this->_dataArray) {
			// Build the data array for the generation algorithms to parse
			$this->_buildDefaultDataArray($table,$path);
		}
				
		return $this->_generate();
	}
	
	/*****************************************************************************
	** 	GENERATE TRIPLET USING:
	**
	** 	Create a BusinessObject, a BusinessCollection, and a DataBroker using the
	**	info specified
	**
	******************************************************************************
	**	ARGUMENTS:
	**
	**	- dataArr: 	An array containing all pertinent values.  Necessary values
	**				have defaults ascribed in case of missing data in array
	**
	*****************************************************************************/
	function generateTripletUsing($dataArr) {
		$this->_dataArray = $dataArr;
		return $this->_generate();
	}


	/*******************************************************************************************
	** 
	**	GENERATION ALGORITHMS
	**
	********************************************************************************************
	/*****************************************************************************
 	** 	GENERATE_BUSINESSOBJECT:
	**
 	** 	generate_businessObject()
  	**
	******************************************************************************
 	** 	OBJECTIVE:
	**
	** 	Generate a BusinessObject with private variables, accessors for each,
	**	and any other generic functionality.
 	**
 	*****************************************************************************/
	function generate_businessObject(){
		//echo "<p>Creating file " . $this->_dataArray["path"] . $this->_dataArray["businessObjectFileName"];
		if (!file_exists($this->_dataArray["path"])) {
			mkdir($this->_dataArray["path"], 0755);
		}
		$fp = fopen($this->_dataArray["path"] . $this->_dataArray["businessObjectFileName"],"w");
		
		if (!$fp) {
			$this->errorMsg = "<br><font color='red'><b>Output file <b>" . $this->_dataArray["businessObjectFileName"] . "</b> cannot be opened/created.</b></font>";
			return false;
		}
		else {
			$today = $today = date("m.d.y");
			
			$output = "<?php\n\n";	
			$output .= "/*********************************************************************************************\n";
			$output .= "**\n";
			$output .= "** 	@author		:	" . $this->_dataArray["authorName"] . " <" . $this->_dataArray["authorEmail"] . ">\n";
			$output .= "** 	@created  	:	" . $today . "\n";
			$output .= "** 	@version	:	1.0.0\n";
			$output .= "**	@company	:	" . $this->_dataArray["authorCompany"] . "\n";
			$output .= "**\n";
			$output .= "**********************************************************************************************\n";
			$output .= "**\n";
			$output .= "** 	Class " . $this->_dataArray["businessObjectName"] . " provides a viewport onto the database table " . strtoupper($this->_dataArray["table"]) . "\n";
			$output .= "**\n";
			$output .= "** 		- Created by CBObjectGenerator v1.2\n";
			$output .= "**\n";
			$output .= "*********************************************************************************************/\n\n\n";
			
			$output .= "require_once(\"../BusinessObjects/CBBusinessObject.php\");\n\n";

			$output .= "class " . $this->_dataArray["businessObjectName"] . " extends CBBusinessObject {\n\n";
			$output .= $this->_printHeaderComment("ATTRIBUTES");
	
			// get the columns for the table to process
			$this->load->model("MetaDB");
			$cols = $this->MetaDB->getColumnsInTable($this->_dataArray["table"]);
							
			// make a private variable for each column
			foreach ($cols as $col) {
				$var = $this->_dataArray["map"][$col];
				$output .= "	var \$" . strtolower($var) . ";\n";
			}
			
			// Reset the row pointer to the beginning
// 			MSSQL_DATA_SEEK($cols, 0);
			
			$output .= "\n\n";
			$output .= $this->_printHeaderComment("ACCESSORS");
			
			// iterate over DB columns and generate a get and set method
			foreach ($cols as $col) {
				$ucCol = strtoupper($col);
				$lcCol = strtolower($col);
				$gets = $this->_dataArray["gets"];
				$sets = $this->_dataArray["sets"];
				
				if ($gets[$col] && $sets[$col]) {
					$output .= "	/*****************************************************************************\n";
					$output .= "	** 	" . $ucCol . ":\n";
					$output .= "	**\n";
					$output .= "	** 	SET and GET $lcCol \n";
					$output .= "	**\n";
					$output .= "	******************************************************************************\n";
					$output .= "	**	ARGUMENTS:\n";
					$output .= "	**\n";
					$output .= "	**	- value: The value assigned.\n";
					$output .= "	**\n";
					$output .= "	*****************************************************************************/\n";
				}
				else if ($gets[$col]) {
					$output .= "	/*****************************************************************************\n";
					$output .= "	** 	" . $ucCol . " (read-only):\n";
					$output .= "	**\n";
					$output .= "	**	GET $lcCol \n";
					$output .= "	**\n";
					$output .= "	*****************************************************************************/\n";
				}
				else if ($sets[$col]) {
					$output .= "	/*****************************************************************************\n";
					$output .= "	** 	" . $ucCol . " (write-only):\n";
					$output .= "	**\n";
					$output .= "	**	SET $lcCol \n";
					$output .= "	**\n";
					$output .= "	******************************************************************************\n";
					$output .= "	**	ARGUMENTS:\n";
					$output .= "	**\n";
					$output .= "	**	- value: The value assigned.\n";
					$output .= "	**\n";
					$output .= "	*****************************************************************************/\n";
				}
								
				if ($gets[$col]) {
					$output .= "	function get_" . $lcCol . "() {\n";
					$output .= "		return $" . "this->_" . $lcCol . ";\n";
					$output .= "	}\n\n";
				}
				
				if ($sets[$col]) {
					$output .= "	function set_" . $lcCol . "($" . "value) {\n";
					$output .= "		$" . "this->_" . $lcCol . " = $" . "value;\n";
					$output .= "	}\n\n";
				}
			}
			
			// now generate other methods
			// GET_COLLECTIONTYPE
			$output .= "\n";
			$output .= $this->_printHeaderComment("GET_COLLECTIONTYPE");
			$output .= "	function get_collectionType() {\n";
			$output .= "		return '" . $this->_dataArray["businessCollectionName"] . "';\n";
			$output .= "	}\n\n";
			
			// INITIALIZE the object
			$output .= "\n";
			$output .= $this->_printHeaderComment("INITIALIZE");
			$output .= "	function initialize() {\n";
			
			// Reset the row pointer to the beginning
// 			MSSQL_DATA_SEEK($cols, 0);
			
			// check to see if we are initializing a foreign key, if so, set to -1
			foreach ($cols as $col) {
				$ucCol = strtoupper($col);
				$var = $this->_dataArray["map"][$col];
				if (stristr($col,"_id") === FALSE) {
					$output .= "		\$this->" . strtolower($var) . " = NULL;\n";
				}
				else {
					$output .= "		\$this->" . strtolower($var) . " = -1;\n";
				}
			}
			
			$output .= "	}\n\n\n";
			
			$output .= $this->_printHeaderComment("PRIVATE METHODS");
			$output .= "	/*****************************************************************************\n";
			$output .= "	**	_DISPLAY STRING\n";
			$output .= "	**\n";
			$output .= "	**	- Return a user-friendly version of this instance\n";
			$output .= "	**\n";
			$output .= "	*****************************************************************************/\n";

			$output .= "	function _displayString() {\n";
 			$output .= "		\n";
 			$output .= "	}\n\n\n";
 			
 			$output .= "	/*****************************************************************************\n";
			$output .= "	**	_EQUALS\n";
			$output .= "	**\n";
			$output .= "	**	- Comare this instance to the argument object for equality\n";
			$output .= "	**\n";
			$output .= "	******************************************************************************\n";
			$output .= "	**	ARGUMENTS:\n";
			$output .= "	**\n";
			$output .= "	**	- anObj: any object for comparison\n";
			$output .= "	**\n";
			$output .= "	*****************************************************************************/\n";

			$output .= "	function _equals(\$anObj) {\n";
 			$output .= "		if (!parent::_equals(\$anObj)) {\n";
 			$output .= "			return false;\n";
 			$output .= "		}\n";
 			$output .= "		else {\n";
 			$output .= "			// add equality code here\n";
 			$output .= "		}\n";
 			$output .= "	}\n";
			
			$output .= "}\n\n";
			$output .= "?>";
			$output .= "\n";
		}
	
		fputs($fp,$output);
		
		fclose($fp);
		
		return true;		
		//echo "<br><font color='blue'>Successfully created Business Object <b>" . $this->_dataArray["businessObjectName"] . "</b> in file <b>" . $this->_dataArray["businessObjectFileName"] . "</b></font>";
	}
	
	
	/*****************************************************************************
 	** 	GENERATE_BUSINESSCOLLECTION:
	**
 	** 	generate_businessCollection()
  	**
	******************************************************************************
 	** 	OBJECTIVE:
	**
	** 	Generate a BusinessCollection based on information provided in $_dataArray.
 	**
 	*****************************************************************************/
	function generate_businessCollection() {
		//echo "<p>Creating file " . $this->_dataArray["path"] . $this->_dataArray["businessCollectionFileName"];
		$fp = fopen($this->_dataArray["path"] . $this->_dataArray["businessCollectionFileName"],"w");
		
		if (!$fp) {
			$this->errorMsg = "<br><font color='red'><b>Output file <b>" . $this->_dataArray["businessCollectionFileName"] . "</b> cannot be opened/created.</b></font>";
			return false;
		}
		else {
			$today = $today = date("m.d.y");
			
			$output = "<?php\n\n";	
			$output .= "/*********************************************************************************************\n";
			$output .= "**\n";
			$output .= "** 	@author		:	" . $this->_dataArray["authorName"] . " <" . $this->_dataArray["authorEmail"] . ">\n";
			$output .= "** 	@created  	:	" . $today . "\n";
			$output .= "** 	@version	:	1.0.0\n";
			$output .= "**	@company	:	" . $this->_dataArray["authorCompany"] . "\n";
			$output .= "**\n";
			$output .= "**********************************************************************************************\n";
			$output .= "**\n";
			$output .= "** 	Class " . $this->_dataArray["businessCollectionName"] . " represents a group of business objects, and serves\n";
			$output .= "**	to model associative entities for the breaking up of many-to-many relationships, the\n";
			$output .= "**	capture of pertinent data therein, and the interface to the persisting of these\n";
			$output .= "**	relationships.\n";
			$output .= "**\n";
			$output .= "** 		- Created by CBObjectGenerator v1.2\n";
			$output .= "**\n";
			$output .= "*********************************************************************************************/\n\n\n";
			
			$output .= "require_once(\"../BusinessCollections/CBBusinessCollection.php\");\n\n";

			$output .= "class " . $this->_dataArray["businessCollectionName"] . " extends CBBusinessCollection {\n\n";
			$output .= $this->_printHeaderComment("FUNCTIONS");
			$output .= "	function get_collectedType() {\n";
			$output .= "		return '" . $this->_dataArray["businessObjectName"] . "';\n";
			$output .= "	}\n\n";
			
			$output .= "	function saveAssociativeData() {\n";
			$output .= "		\n";
			$output .= "	}\n\n";
			
			$output .= "	function deleteAssociativeData() {\n";
			$output .= "		\n";
			$output .= "	}\n";
			
			$output .= "}\n\n";
			$output .= "?>";
			$output .= "\n";

			fputs($fp,$output);

			fclose($fp);
			
			return true;
			
			//echo "<br><font color='blue'>Successfully created Business Collection <b>" . $this->_dataArray["businessCollectionName"] . "</b> in file <b>" . $this->_dataArray["businessCollectionFileName"] . "</b></font>";
		}
	}
	
	
	/*****************************************************************************
 	** 	GENERATE_DATABROKER:
	**
 	** 	generate_dataBroker()
  	**
	******************************************************************************
 	** 	OBJECTIVE:
	**
	** 	Generate the Data Broker for the specified business object.
 	**
 	*****************************************************************************/
 	function generate_dataBroker(){
		//echo "<p>Creating file " . $this->_dataArray["path"] . $this->_dataArray["dataBrokerFileName"];
		$fp = fopen($this->_dataArray["path"] . $this->_dataArray["dataBrokerFileName"],"w");
		
		if (!$fp) {
			$this->errorMsg = "<br><font color='red'><b>Output file <b>" . $this->_dataArray["dataBrokerFileName"] . "</b> cannot be opened/created.</b></font>";
			return false;
		}
		else {			
			$today = $today = date("m.d.y");
			
			$output = "<?php\n\n";	
			$output .= "/*********************************************************************************************\n";
			$output .= "**\n";
			$output .= "** 	@author		:	" . $this->_dataArray["authorName"] . " <" . $this->_dataArray["authorEmail"] . ">\n";
			$output .= "** 	@created  	:	" . $today . "\n";
			$output .= "** 	@version	:	1.0.0\n";
			$output .= "**	@company	:	" . $this->_dataArray["authorCompany"] . "\n";
			$output .= "**\n";
			$output .= "**********************************************************************************************\n";
			$output .= "**\n";
			$output .= "** 	Class " . $this->_dataArray["dataBrokerName"] . " manages data transfer between table " . strtoupper($this->_dataArray["table"]) . "\n";
			$output .= "**  and the Business Object, " . $this->_dataArray["businessObjectName"] . ".\n";
			$output .= "**\n";
			$output .= "** 		- Created by CBObjectGenerator v1.2\n";
			$output .= "**\n";
			$output .= "*********************************************************************************************/\n\n\n";

			$output .= "require_once(\"../DataBrokers/CBDataBroker.php\");\n";
			$output .= "require_once(\"../BusinessObjects" . $this->_dataArray["businessObjectFileName"] . "\");\n\n";
			
			$output .= "class " . $this->_dataArray["dataBrokerName"] . " extends CBDataBroker {\n\n";

			$output .= $this->_printHeaderComment("OVERRIDDEN FUNCTIONS");

			$output .= "	/*****************************************************************************\n";
			$output .= "	**	ORMAP:\n";
			$output .= "	**\n";
			$output .= "	**	A dictionary mapping of relational data to object data where\n";
			$output .= "	**	KEY = variable and VALUE = DB column\n";
			$output .= "	**\n";
			$output .= "	*****************************************************************************/\n";

			$output .= "	function orMap() {\n";
			$output .= "		return array (\n";
			
			// Calculate the number of tabs to insert for the orMap
			$tabs = array();
			foreach ($this->_dataArray["map"] as $var) {
				$tabs[] = strlen($var);
			}
			
			// find longest var name (add 2 for the quotes)
			$x = max($tabs) + 2;
			
			// determine the number of tabs we need based on longest var name
			$tabCount = floor($x / 4) + 1;
			
			foreach ($this->_dataArray["map"] as $col=>$var) {
				$output .= "			'$var'";
				
				$varLen = strlen($var) + 2;
				$varTabs = floor($varLen / 4);
				
				while ($varTabs < $tabCount) {
					$output .= "\t";
					$varTabs++;
				}
				$output .= "=>	'$col',\n";
			}
			
			// Pull off that trailing comma
			$output = substr($output, 0, -2);
			
			$output .= "\n";
			$output .= "		);\n";
			$output .= "	}\n\n";
			
			
			$output .= "	/*****************************************************************************\n";
			$output .= "	**	GET_TABLENAME:\n";
			$output .= "	**\n";
			$output .= "	**	Accessor for the table this object sits atop.\n";
			$output .= "	**\n";
			$output .= "	*****************************************************************************/\n";

			$output .= "	function get_tableName() {\n";
			$output .= "		return '" . $this->_dataArray["table"] . "';\n";
			$output .= "	}\n\n\n";

			$output .= "	/*****************************************************************************\n";
			$output .= "	**	GET_PKCOLUMNNAME:\n";
			$output .= "	**\n";
			$output .= "	**	Accessor for the name of the primary key column in table " . strtoupper($this->_dataArray["table"]) . ".\n";
			$output .= "	**\n";
			$output .= "	*****************************************************************************/\n";
		
			$output .= "	function get_pkColumnName() {\n";
			$output .= "		return '" . $this->_dataArray["pkColumnName"] . "';\n";
			$output .= "	}\n\n\n";

			$output .= "	/*****************************************************************************\n";
			$output .= "	**	GET_CLASSSERVED:\n";
			$output .= "	**\n";
			$output .= "	**	Accessor for the name of the class this broker services.\n";
			$output .= "	**\n";
			$output .= "	*****************************************************************************/\n";
			
			$output .= "	function get_classServed() {\n";
			$output .= "		return '" . $this->_dataArray["businessObjectName"] . "';\n";
			$output .= "	}\n\n\n";

			$output .= "	/*****************************************************************************\n";
			$output .= "	**	GET_WHERECLAUSE:\n";
			$output .= "	**\n";
			$output .= "	**	Build a SQL WHERE clause based on data contained in argument object.\n";
			$output .= "	**\n";
			$output .= "	******************************************************************************\n";
			$output .= "	**	ARGUMENTS:\n";
			$output .= "	**\n";
			$output .= "	**	- anInst: An instance of class " . $this->_dataArray["businessObjectName"] . ". \n";
			$output .= "	**\n";
			$output .= "	*****************************************************************************/\n";
		
			$output .= "	function get_whereClause(\$anInst) {\n";
			$output .= "		\$wc = '';\n\n";
			$output .= "		if (strlen(\$this->_searchByIdSQL(\$anInst)) > 0) {\n";
            $output .= "			\$wc .= \$this->_searchByIdSQL(\$anInst);\n";
			$output .= "		}\n";
			$output .= "		else {\n";
			
			$this->load->model("MetaDB");
			$cols = $this->MetaDB->getColumnsInTable($this->_dataArray["table"]);
			
			$searchMethodNames = array();

			foreach($cols as $col) {
				if ($col != $this->_dataArray["pkColumnName"]) {
					$methodName = strtolower($col);
					$methodName = str_replace("_", " ", $methodName);
					$methodName = ucwords($methodName);
					$methodName = str_replace(" ", "", $methodName);
					
					$searchName = "_searchBy" . $methodName . "SQL";
					$searchMethodNames[$col] = $searchName;
					
					$output .= "			if (strlen(\$this->$searchName(\$anInst)) > 0) {\n";
					$output .= "				if (strlen(\$wc) > 0) {\n";
					$output .= "					\$wc .= ' AND ';\n";
	                $output .= "				}\n\n";
	                $output .= "				\$wc .= \$this->$searchName(\$anInst);\n";
	            	$output .= "			}\n\n";
				}
			}
						
			$output .= "		}\n\n";
			$output .= "		return \$wc;\n\n";
			$output .= "	}\n";
			
			$output .= $this->_printHeaderComment("PRIVATE FUNCTIONS");
			
			foreach ($searchMethodNames as $col=>$method) {
				if ($this->_dataArray["gets"][$col]) {
					$output .= "	function " . $method . "(\$anInst) {\n";
					$output .= "		\$str = \$this->_processExact('" . $col . "', \$anInst->get" . $this->_dataArray["map"][$col] . "());\n\n";
					$output .= "		return \$str;\n";
					$output .= "	}\n\n";
				}
			}			

			$output .= "}\n\n";
			$output .= "?>\n";

			fputs($fp,$output);

			fclose($fp);
			
			return true;
			
			//echo "<br><font color='blue'>Successfully created Data Broker <b>" . $this->_dataArray["dataBrokerName"] . "</b> in file <b>" . $this->_dataArray["dataBrokerFileName"] . "</b></font>\n";
		}
	}
	
	
	/*******************************************************************************************
	** 
	**	PRIVATE METHODS
	**
	********************************************************************************************
	/*****************************************************************************
 	** 	_BUILD DEFAULT DATA ARRAY:
	**
 	** 	_buildDefaultDataArray($table,$path)
  	**
	******************************************************************************
 	** 	OBJECTIVE:
	**
	** 	Create an array of needed information that will fuel the generators.
	**	Needed info being:
	**		- TABLE: The DB table to process
	**		- PATH: The location to store the output files
	**		- AUTHOR NAME: The developer creating these files
	**		- AUTHOR EMAIL: Email of the developer creating these files
	**		- AUTHOR COMPANY: The company for which these files are created
	**		- BUSINESS OBJECT NAME: The name of the BO to create
	**		- BUSINESS OBJECT FILE NAME: The name of the physical file the BO
	**		  will reside in
	**		- BUSINESS COLLECTION NAME: The name of the collection class that
	**		  holds the BOs
	**		- BUSINESS COLLECTION FILE NAME: The name of the physical file the
	**		  BusinessCollection will reside in
 	**		- DATA BROKER NAME: The name of the DataBroker to create
 	**		- DATA BROKER FILE NAME: The name of the physical file the DataBroker
 	**		  will reside in
 	**		- MAP array: An array keyed by DB column names with values of PHP
 	**		  variable names
 	**		- GETARR array: An array keyed by DB column names with values of
 	**		  getter method names
 	**		- SETARR array: An array keyed by DB column names with values of
 	**		  setter method names
 	**
 	******************************************************************************
 	**	ARGUMENTS
 	**
 	**	- table: Name of the table from which to generate objects.
 	**	- path:  Directory in which to store the resultant output files.
 	**			 e.g. /root/objets/obj.class.php . Don't forget to set
 	**			 write permissions on the directory.
 	**
 	*****************************************************************************/
	function _buildDefaultDataArray($table,$path) {
		unset($this->_dataArray);
		
		$this->_dataArray = array();
		$setArr = array();
		$getArr = array();
		$map = array();
		
		$this->_dataArray["path"] = $path;
		$this->_dataArray["table"] = $table;
		$this->_dataArray["authorName"] = "Christopher Burns";
		$this->_dataArray["authorEmail"] = "cburns@mythosproductions.com";
		$this->_dataArray["authorCompany"] = "Mythos Productions";
		
		$baseBOName = ucfirst(strtolower($table));
		$this->_dataArray["businessObjectName"] = $baseBOName;
		$this->_dataArray["businessObjectFileName"] = $baseBOName . ".php";
		$this->_dataArray["businessCollectionName"] = $baseBOName . "s";
		$this->_dataArray["businessCollectionFileName"] = $baseBOName . "s.php";
		$this->_dataArray["dataBrokerName"] = $baseBOName . "DataBroker";
		$this->_dataArray["dataBrokerFileName"] = $baseBOName . "DataBroker.php";
		
 		$this->load->model("MetaDB");
		$cols = $this->MetaDB->getColumnsInTable($this->_dataArray["table"]);
 
		foreach($cols as $col) {
			$colName = strtoupper($col);
			$map[$colName] = "_" . $col;
			$getArr[$colName] = "get_" . $col;
			$setArr[$colName] = "set_" . $col; 
		}
 
 		$this->_dataArray["map"] = $map;
		$this->_dataArray["gets"] = $getArr;
		$this->_dataArray["sets"] = $setArr; 		
	}
	
	
	/*****************************************************************************
 	** 	_GENERATE:
	**
 	** 	_generate()
  	**
	******************************************************************************
 	** 	OBJECTIVE:
	**
	** 	Begin the triplet generation process.
 	**
 	*****************************************************************************/
 	function _generate() {
 		$this->errorMsg = "";
 		
 		if ($this->generate_businessObject() && $this->generate_businessCollection() &&  $this->generate_dataBroker()) {
 			return true;
 		}
 		
 		return false;
 	}
 	
 	function _printHeaderComment($val) {
 		$output = "";
 		$output .= "	/*****************************************************************************************\n";
		$output .= "	**	" . strtoupper($val) . "\n";
		$output .= "	*****************************************************************************************/\n";
		
		return $output;
 	}
}