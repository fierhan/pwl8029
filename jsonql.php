<?php

	class jsonql{
		
		private $db; #this will be an associative array
		private $status = true; #true = functions can be done else don't
		
		private function printErr($f, $e){
			echo "<B>
				Error found it at ". $f .": ". $e ."</B></BR>";
			$this->status = false;
		}
			#db
		public function createDB($name, $d = ""){
			$fn = "createDB";
			if($d == "" || file_exists($d)){
			if(!isset($this->db)){
			$this->db = array(
				"INFORMATION_DB" => array(
					"DB_NAME" => $name,
					"DB_LOCATION" => $d),
				"COLUMNS_TYPE" => array(),
				"COLUMNS_NAME" => array(),
				"ROWS" => array());
				$this->created = true;
				return true;
			} else {
			    self::printErr($fn, "DB is already set");
			    return false;
			}
		} else {
			self::printErr($fn, "Direction does not exists");
			return false;
		}
		}
		
		#location without file extension
		public function loadDB($location){
			$fn = "loadDB";
			if(is_file($location .".json")){
			    if(!isset($this->db)){
				if($f = fopen($location .".json", "r")){
					$js = fread($f, filesize($location .".json"));
					fclose($f);
					$this->db = json_decode($js, true);
					$this->created = true;
					return true;
				} else {
					self::printErr($fn, "Error loading file ". $location);
					return false;
				}
			    } else {
			        self::printErr($fn, "DB is already set");
			        return false;
			    }
			} else {
				self::printErr($fn, $location ." is NOT a file.");
				return false;
			}
		}
		
		public function saveDB(){
			$fn = "saveDB";
			if(is_array($this->db) && $this->status){
				$db = json_encode($this->db);
				$f = fopen(
					$this->db["INFORMATION_DB"]["DB_LOCATION"] . 
					$this->db["INFORMATION_DB"]["DB_NAME"] .".json", "w");
				fwrite($f, json_encode($this->db));
				fclose($f);
				return true;
			} else {
				self::printErr($fn, "Error saving DB");
				return false;
			}
		}
			
			#about rows/columns
		private function checkType($row){
			$correct = true;
			for($i = 0; $i < count($row); $i++){
				switch($this->db["COLUMNS_TYPE"][$i]){
					case "i":
						if(!is_numeric($row[$i])){
							$correct = false;
						}
					break;
					
					default:
						if(!is_string($row[$i])){
							$correct = false;
						}
				}
			}
			return $correct;
		}
		
		public function addColumn($columnName, $columnType){
			$this->db["COLUMNS_TYPE"][] = $columnType;
			$this->db["COLUMNS_NAME"][] = $columnName;
		}
		
		
		#row must be an array
		public function addRow($row){
			$fn = "addRow";
			if(is_array($row) && count($row) == count($this->db["COLUMNS_NAME"]) && $this->status){
				if(self::checkType($row)){
					$this->db["ROWS"][] = $row;
				} else {
					self::printErr($fn, "Invalid type value.");
				}
			} else {
				self::printErr($fn, "Error adding row");
			}
		}
		
		public function deleteRow($where = "", $value = ""){
			$fn = " deleteRow";
			if($this->status){
				if($where == "" && $value == ""){
					$this->db["ROWS"] = array();
				} elseif($where == "" && $value != "" || $where != "" && $value == ""){
					self::printErr($fn, "Where or Value variable must be empty or contain value to delete.");
				} else {
					$r = 0;
					$c = false;
					foreach($this->db["COLUMNS_NAME"] as $colVal){
						if($colVal == $where){
							$c = true;
							break;
						}
						$r++;
					}
					if($c){
						$y = 0;
						foreach($this->db["ROWS"] as $row){
							if($row[$r] == $value){
								unset($this->db["ROWS"][$y]);
							}
							$y++;
						}
					} else{
						self::printErr($fn, "Column '". $where ."' not found");
					}
				}
			} else {
				self::printErr($fn, "Error deleting row");
			}
		}
		
		public function printTable(){
			$fn = "printTable";
			if($this->status){
				echo "
					<TABLE>
						<THEAD>
							<TR>";
							foreach($this->db["COLUMNS_NAME"] as $col){
								echo "<TD>". $col ."</TD>";
							}
							echo "</TR>";
						echo "
						</THEAD>
						<TBODY>";
							foreach($this->db["ROWS"] as $row){
								echo "<TR>";
									foreach($row as $value){
										echo "<TD>". $value ."</TD>";
									}
								echo "</TR>";
							}
						echo "
						</TBODY>
						</TABLE>";
			} else {
				self::printErr($fn, "Error printing table.");
			}
		}
		
	}

?>




