<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CaseController extends CI_Controller {
	public function setup() {
		$this->load->model("MetaDB");
		
		$dbRes = $this->MetaDB->getDatabases();
		
		$this->load->view("CBCASEToolView", ["databases" => $dbRes]);
	}
	
	public function getTablesAJAX($fromDB) {
		$this->load->model("MetaDB");
		
		$tableRes = $this->MetaDB->getTablesInDB($fromDB);
		
		$arr = ["tables" => $tableRes];
		
		$this->load->view("partials/tablesPartialAJAX", $arr);
	}
	
	public function analyze() {
		$selectedDB = $this->input->post("database");
		$table = $this->input->post("table");
		
		$tableName = strtolower($table);
		$tableName = str_replace("_", " ", $tableName);
		$tableName = ucwords($tableName);
		$tableName = str_replace(" ", "", $tableName);
		
		$author = $this->input->post("authorName");
		$email = $this->input->post("authorEmail");
		$company = $this->input->post("authorCompany");
		
		$boName = $tableName;
		$boFileName = $boName . '.php';
		$bcName = $tableName . 's';
		$bcFileName = $bcName . '.php';
		$brokerName = $tableName . 'DataBroker';
		$brokerFileName = $brokerName . '.php';
		
		$this->load->model("MetaDB");
		$cols = $this->MetaDB->getColumnsInTable($table, $selectedDB);
		
		$orMap = [];
		$varName;
		
		foreach ($cols as $colName) {
			if (strtoupper($table) . "_ID" == $colName) {
				$varName = "_id";
			}
			else {
				$varName = "_" . strtolower($colName);
			}
			
			$orMap[$colName] = $varName;
		}
		
		$params = [
				"table" => $tableName,
				"boName" => $boName,
				"boFileName" => $boFileName,
				"bcName" => $bcName,
				"bcFileName" => $bcFileName,
				"brokerName" => $brokerName,
				"brokerFileName" => $brokerFileName,
				"orMap" => $orMap,
				"author" => $author,
				"email" => $email,
				"company" => $company
		];
		
		$this->load->view("CBCASEToolAnalyzerView", $params);
	}
	
	public function generate() {
		//	Create the array that will feed the ObjectGenerator
		$infoArr = array();
		$getArr = array();
		$setArr = array();
		$orMap = array();
		
		//	Get the POST vars and stuff them into the array
		$infoArr["table"] = $this->input->post("tableName");
		$infoArr["authorName"] = $this->input->post("authorName");
		$infoArr["authorEmail"] = $this->input->post("authorEmail");
		$infoArr["authorCompany"] = $this->input->post("authorCompany");
		$infoArr["businessObjectName"] = $this->input->post("bo");
		$infoArr["businessObjectFileName"] = $this->input->post("bofn");
		$infoArr["businessCollectionName"] = $this->input->post("bc");
		$infoArr["businessCollectionFileName"] = $this->input->post("bcfn");
		$infoArr["dataBrokerName"] = $this->input->post("db");
		$infoArr["dataBrokerFileName"] = $this->input->post("dbfn");
		$infoArr["path"] = $this->input->post("path");
		
		//	Map the columns to their user-defined variable names
		$pkColName = 'id';
		
		$infoArr['pkColumnName'] = strtoupper($pkColName);
		
		$this->load->model("MetaDB");
		$cols = $this->MetaDB->getColumnsInTable($infoArr['table']);
		
		foreach ($cols as $colName) {
			if ($pkColName == $colName) {
				$orMap[$colName] = "_id";
			}
			else {
				$orMap[$colName] = $this->input->post($colName);
				$getArr[$colName] = $this->input->post("get" . $colName);
				$setArr[$colName] = $this->input->post("set" . $colName);
			}
		}
		
		$infoArr['map'] = $orMap;
		$infoArr['gets'] = $getArr;
		$infoArr['sets'] = $setArr;
		
		$this->load->model("CBObjectGenerator");
		$this->CBObjectGenerator->generateTripletUsing($infoArr);
	}
}