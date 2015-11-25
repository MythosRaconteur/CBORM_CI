<?php

	class MetaDB extends CI_Model {
		function getDatabases() {
			$arr = $this->db->query("SHOW DATABASES")->result_array();
			$rtnArr = [];
			
			foreach ($arr as $assocArr) {
				$rtnArr[] = $assocArr['Database'];
			}
			
			return $rtnArr;
		}
		
		function getTablesInDB($aDB) {
			$arr = $this->db->query("SHOW TABLES IN $aDB")->result_array();
			
			$rtnArr = [];
			
			foreach ($arr as $assocArr) {
				$rtnArr[] = $assocArr['Tables_in_' . strtolower($aDB)];
			}
			
			return $rtnArr;
		}
		
		function getColumnsInTable($aTable, $aDB = null) {
			if ($aDB) {
				$this->db->close();
				$this->db->database = $aDB;
				
				$dsn = "mysql://root:root@localhost/$aDB";
				$this->load->database($dsn);
			}
			
			$arr = $this->db->query("SHOW COLUMNS IN $aTable")->result_array();
			
			$rtnArr = [];
			
			foreach ($arr as $assocArr) {
				$rtnArr[] = $assocArr["Field"];
			}
			
			return $rtnArr;
		}
	}