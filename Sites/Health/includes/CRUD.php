<?php

	abstract class pdoClass {
            
                protected static $driver = 'sqlsrv';
                protected static $servername = "(local)\sqlexpress";
                protected static $username = 'sa';
                protected static $dbname = 'Health';
                protected static $password = 'hibs2016';
		
		public static function getColumnMeta() {
			$meta = array(
				"fields"		=> array(),
				"fieldMeta"		=> array(),
				"primaryKey"	=> NULL
			);
			
			if (count($meta["fields"]) == 0 || count($meta["fieldMeta"]) == 0) {
                            $sql = "SELECT c.name Field, c.is_identity [Key], t.name Type, c.max_length,
                                    c.Precision, c.Scale, c.is_nullable, c.collation_name 
                            FROM sys.columns c 
                            INNER JOIN sys.types t ON t.system_type_id=c.system_type_id
                            WHERE object_id=object_id('" . get_called_class() . "')
                            ORDER BY column_id";
				
				try {
					$db = self::getConnection();
					$query = $db->prepare($sql);
					$query->execute();
					$result = $query->fetchAll(PDO::FETCH_ASSOC);
				}
				catch (PDOException $e) {
					$e->getMessage();
					return false;
				}
				foreach ($result as $key => $col) {
					$colName = $col['Field'];
					$meta['fields'][$colName] = $col;
					if ($col['Key'] == "1" && empty($meta['primaryKey'])) {
						$meta['primaryKey'] = $colName;
					}
					$colType = self::parseColumnType(array('Type' => $col['Type'], 'Length' => $col['max_length']));
					$meta['fieldMeta'][$colName] = $colType;
				}
			}
			return $meta;
		}
		
		protected static function parseColumnType($colType) {
			$colInfo['type'] = $colType['Type'];
			$colInfo['pdoType'] = '';
			$colInfo['length'] = $colType['Length'];
			//$colInfo['attributes'] = isset($colParts[1]) ? $colParts[1] : NULL;
			$pdoType = '';
			foreach (self::pdoBindTypes() as $pKey => $pType) {
				if ($colInfo['type'] == $pKey) {
					$colInfo['pdoType'] = $pType;
					break;
				}
				else {
					$colInfo['pdoType'] = PDO::PARAM_STR;
				}
			}
			return $colInfo;
		}
		
		protected static function pdoBindTypes() {
			return array (
				'varchar'	=>	PDO::PARAM_STR,
				'int'		=>	PDO::PARAM_INT,
				'float'		=>	PDO::PARAM_STR,
				'datetime'	=>	PDO::PARAM_STR,
				'time'		=>	PDO::PARAM_INT,
				'text'		=>	PDO::PARAM_STR,
				'blob'		=>	PDO::PARAM_LOB,
				'binary'	=>	PDO::PARAM_LOB
			);
		}
		
		protected function __construct(Array $properties) {
			foreach ($properties as $key => $property) {
				if(property_exists(get_called_class(), $key)) {
					$this->$key = $property;
				}
				else {
					echo '<p>Property "'.$key.'" does not exist."</p>';
				}
			}
			self::fixProperties($this);

			if ($this->_id == 0) $this->_id = null;
		}
                
                protected static function fieldType($field) {
                    
                    $meta = self::getColumnMeta();
                    
                    $types = $meta['fieldMeta'];
                    
                    return $types[$field]['type'];
                }
                
                protected static function isDateField($field) {
                    
                    $type = self::fieldType($field);
                    
                    if ($type == 'date' || $type == 'datetime') {
                        return true;
                    } else {
                        return false;
                    }
                }
				
		protected static function fixProperties($object) {
			$meta = self::getColumnMeta();
			
			$types = $meta['fieldMeta'];
			foreach ($types as $key => $type) {
				$property = '_'.$key;
				switch ($type['type']) {
					case 'date':
						if(!empty($object->$property)) $object->$property = new DateTime($object->$property);
						break;
					case 'time':
						$object->$property = new DateTime($object->$property);
						break;
					case 'datetime':
						if(!empty($object->$property)) $object->$property = new DateTime($object->$property);
						break;
					case 'timestamp':
						$object->$property = new DateTime($object->$property);
						break;
					case 'float':
						$object->$property = (float)$object->$property;
						break;
					case 'double':
						$object->$property = (double)$object->$property;
						break;
					case 'int':
						$object->$property = (int)$object->$property;
						break;
					case 'bigint':
						$object->$property = (int)$object->$property;
						break;
					case 'mediumint':
						$object->$property = (int)$object->$property;
						break;
					case 'smallint':
						$object->$property = (int)$object->$property;
						break;
					case 'tinyint':
						$object->$property = (int)$object->$property;
						break;
					case 'bool':
						$object->$property = ($object->$property == 1) ? true : false;
						break;
					case 'bit':
						$object->$property = ($object->$property == 1) ? true : false;
						break;
					default:
						break;
				}
			}
		}
		
		public static function fixDate($dte = false, $flag = true) {

			$dte2 = DateTime::CreateFromFormat('Y-m-d H:i:s', self::getMinMaxValue('obsdate', false, false, $flag) . ' 00:00:00');
			$dte = ($dte ? $dte : $dte2);
			
			return ($flag ? ($dte->getTimestamp() < $dte2->getTimestamp() ? $dte2 : $dte) : ($dte->getTimestamp() > $dte2->getTimestamp() ? $dte2 : $dte));
		}
		
		public static function getFields($called_class = false) {
				
			static $fields = array();
			if (!$called_class) {
				$called_class = get_called_class();
			}
			
			if (!array_key_exists($called_class, $fields)) {
				$reflection_class = new ReflectionClass($called_class);
				$properties = array();
				foreach ($reflection_class->getProperties() as $property) {
					if (substr($property->name, 0, 1) == "_") {
						$properties[] = $property->name;
						$fields[$called_class][] = substr($property->name, 1);
					}
				}
			}
			
			return $fields[$called_class];
		}
				
		public static function getSelect($top = false, $called_class = false) {
			if (!$called_class) {
				$called_class = get_called_class();
			}
			return "SELECT " . (!$top ? "" : " TOP " . $top . " ") . implode(', ', self::getFields()) . " FROM " . strtolower($called_class);
		}

		protected function save() {

			$db = self::getConnection();
                        $table = strtolower(get_called_class());

			$updates = array();
			$pk = self::getPrimaryKey();
			
			$fields = self::getFields();

			$insert = "INSERT INTO " . $table . " (";
                        $update = "UPDATE " . $table . " SET ";
                        
			$function1 = function ($value) {
				return ':' . $value;
			};

			$function2 = function ($val1, $val2) {
				return $val1 . " = " . $val2;
			};

			$bindparams = array_map($function1, $fields);
			$f = $fields;

			$trunc1 = array_shift($bindparams);
			$trunc2 = array_shift($f);
			$replace = array_map($function2, $f, $bindparams);
            $insert .=  implode(", ", $f) . ")";
			$insert .= " VALUES (" . implode(', ', $bindparams) . ")";
            $update .= implode(', ', $replace);
            $id_property = '_'.$pk;
            $exists = "SELECT $pk FROM $table WHERE $pk = ?";
                      
            $query = $db->prepare($exists);
            $query->execute(array($this->$id_property));
            $row = $query->fetch(PDO::FETCH_ASSOC);

            $sql = (!$row ? $insert : $update . " WHERE $pk = " . $this->$id_property);

            //echo $this->_numberofstepswalked . '<br>';

            //die($sql);

            try {
				$statement = $db->prepare($sql);
                                
				foreach ($f as $field) {
					$property = '_'.$field;
					$type = gettype($this->$property);
					if ($type == 'object') {
						$type = get_class($this->$property);
					}
					if ($type == 'DateTime') {
						$dt_object = $this->$property;
						$dt_format = $dt_object->format('Y-m-d H:i:s');
						$statement->bindvalue(":" . $field, $dt_format);
					}
					else {
						$statement->bindparam(":" . $field, $this->$property);
					}
				}
			
				$statement->execute();
			}
			catch (PDOException $e) {
				echo $e->getMessage();
			}
		}
		
		public static function getConnection() {

			$return = null;

			try {
				$dsn = self::$driver.':server='.self::$servername.';Database='.self::$dbname.';';
				$return = new PDO($dsn, self::$username, self::$password);
				$return->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				return $return;
			}
			catch(PDOException $e) {
				echo $e->getMessage();
				return false;
			}
		}

		protected static function disconnect() {
			self::$dboconnection = null;
		}

		public static function getInitialCurrent($flag = false) {
			
			$db = self::getConnection();

			$select = self::getSelect(1) . " WHERE userid = " . $_SESSION['userid'] . " ORDER BY obsdate " . ($flag ? "ASC" : "DESC");
			$return = array();
			$row = $db->query($select)->fetch(PDO::FETCH_ASSOC);

			foreach($row as $key => $value) {
				$property = '_'.$key;
				$return[$property] = $value;
			}

			return new static($return);
		}
                
                protected function getCurrentObject() {
                    return $this;
                }

		public static function getPreviousNext($id = false, $flag = true) {
			
			$db = self::getConnection();

			$curr_rec = (!$id ? self::getInitialCurrent() : self::get($id));
			$strDate = $curr_rec->getObsDate()->format('Y-m-d');

			$select = self::getSelect(1) . " WHERE obsdate " . ($flag ? '<' : '>') .
				" :odate AND userid = " . $_SESSION['userid'] . " ORDER BY obsdate " . ($flag ? "DESC" : "ASC");

			$statement = $db->prepare($select);

			$statement->bindparam(':odate', $strDate, PDO::PARAM_STR);
			$statement->execute();
			$row = $statement->fetch(PDO::FETCH_ASSOC);
			if(!empty($row)) {
				$return = array();
				foreach($row as $key => $value) {
					$property = '_'.$key;
					$return[$property] = $value;
				}

				return new static($return);
			} else {
				return $curr_rec;
			}
		}
		
		public static function getPrevNextValue($field, $obsdate = false, $flag = true) {
			
			$db = self::getConnection();
			
			$class = strtolower(get_called_class());
			
			$obsdate = self::fixDate($obsdate, $flag);
			$str_date = $obsdate->format('Y-m-d');
			
			$select = "SELECT TOP 1 $field FROM $class WHERE (((obsdate " . ($flag ? '<' : '>') .
				" :odate) AND (userid = " . $_SESSION['userid'] . ")) AND ($field <> 0)) ORDER BY obsdate " . ($flag ? "DESC" : "ASC");
				
			$statement = $db->prepare($select);
			
			$statement->bindparam(':odate', $str_date, PDO::PARAM_STR);
			$statement->execute();
			$row = $statement->fetch(PDO::FETCH_ASSOC);
			return (!empty($row) ? $row[$field] : self::getMinMaxValue($field, false, false, $flag));
		}

		public static function getMinMaxValue($field, $startdate = false, $enddate = false, $flag = true) {
			
			$db = self::getConnection();

			$min_max = ($flag ? "MIN" : "MAX");
			$class = strtolower(get_called_class());
			$user = $_SESSION['userid'];
			
			if(self::isDateField($field)) {
				$select = "SELECT {$min_max}({$field}) AS $field FROM $class WHERE userid = $user";
			} else {
				$startdate = self::fixDate($startdate, true);
				$enddate = self::fixDate($enddate, false);
				$mindate = $startdate->format('Y-m-d');
				$maxdate = $enddate->format('Y-m-d');
				$select = "SELECT {$min_max}($field) AS $field FROM $class WHERE (((userid = $user AND ($field <> 0))) AND ((obsdate >= :mindate) AND (obsdate <= :maxdate)))";
			}
                        
			$statement = $db->prepare($select);
			if(!self::isDateField($field)) {
				$statement->bindparam(':mindate', $mindate, PDO::PARAM_STR);
				$statement->bindparam(':maxdate', $maxdate, PDO::PARAM_STR);
			}

			$statement->execute();
			$row = $statement->fetch(PDO::FETCH_ASSOC);
                        $ret = $row[$field];
                        if (self::isDateField($field)) {
                            $ret = date('d-M-Y', strtotime($ret));
                            $dte = new DateTime($ret);
                            $ret = $dte->format('Y-m-d');
                        }
			return $ret;
		}

		public static function getFirstLastValue($field, $start = false, $end = false, $flag = true) {
			
			$db = self::getConnection();

			$start = self::fixDate($start, true);
			$end = self::fixDate($end, false);
			
			$str_start = $start->format('Y-m-d');
			$str_end = $end->format('Y-m-d');
			
			$select = "SELECT TOP 1 ($field) AS $field FROM " . strtolower(get_called_class()) . " WHERE (((userid = " . $_SESSION['userid'] .
				") AND ($field <> 0)) AND ((obsdate >= :start) AND (obsdate <= :end))) ORDER BY obsdate " . ($flag ? "DESC" : "ASC");
			$statement = $db->prepare($select);
			$statement->bindparam(':start', $str_start, PDO::PARAM_STR);
			$statement->bindparam(':end', $str_end, PDO::PARAM_STR);

			$statement->execute();
			$row = $statement->fetch(PDO::FETCH_ASSOC);

			return $row[$field];
		}
                
                protected static function has_date($called_class = false) {
                    if (!$called_class) {
                        $called_class = get_called_class();
                    }
                    
                    $fields = static::getFields($called_class);
                    
                    return (!array_search('obsdate', $fields) ? false : true);
                }

		public static function getByDate($startdate = null, $enddate = null) {

			if($startdate == null) $startdate = false;
			if($enddate == null) $enddate = false;
			$startdate = self::fixDate($startdate, true);
			$enddate = self::fixDate($enddate, false);
			$str_startdate = $startdate->format('Y-m-d');
			$str_enddate = $enddate->format('Y-m-d');

			$db = self::getConnection();

			$select = self::getSelect() . " WHERE (((obsdate >= :startdate) AND (obsdate <= :enddate)) AND (userid = " . $_SESSION['userid'] . "))";
		
			$statement = $db->prepare($select);

			$statement->bindparam(':startdate', $str_startdate, PDO::PARAM_STR);
			$statement->bindparam(':enddate', $str_enddate, PDO::PARAM_STR);
			$statement->execute();
			
			$data = $statement->fetchAll(PDO::FETCH_ASSOC);
			
			$return = array();

			foreach ($data as $row) {
				$values = array();
				foreach ($row as $key => $value) {
					$property = '_'.$key;
					$values[$property] = $value;
				}
				$return[] = new static($values);
			}

			return $return;

			$db = null;
		}
		
		public static function get($id) {
		
			$called_class = get_called_class();

			$db = self::getConnection();
			
			$select = self::getSelect() . " WHERE id = :id" ;
			$statement = $db->prepare($select);
			
			$statement->bindparam(':id', $id, PDO::PARAM_INT);
			$statement->execute();
			
			$return = array();
			
			$row = $statement->fetch(PDO::FETCH_ASSOC);

			foreach ($row as $key => $value) {
				$property = '_'.$key;
				$return[$property] = $value;
			}
			
			$return = new static($return);
			
			$db = null;

			return $return;
		}
		
		private static function getWhere($wh = null) {
		
			$called_class = get_called_class();
			$has_userid = property_exists($called_class, '_userid');
                        
			$warr = array();
			if ($wh != null) {
				foreach ($wh as $key => $value) {
					$prop = "_" . $key;
					if (property_exists($called_class, $prop)) {
						$q = (is_string($value) ? "'" : "");
						$warr[] = $key . " = " . $q . $value . $q;
					} elseif ($key == 'sdate') {
						$q = "'";
						$warr[] = 'obsdate >= ' . $q . $value . $q;
					} elseif ($key == 'edate') {
						$q = "'";
						$warr[] = 'obsdate <= ' . $q . $value . $q;
					}
				}
				$wval = implode(" AND ", $warr);
			} else {
				$wval = "";
			}

			$where = (!$has_userid ? ($wval == "" ? "" : " WHERE " . $wval) : " WHERE userid = " . $_SESSION['userid'] . ($wval == "" ? "" : " AND " . $wval));
			
			return $where;
		}
                
                public static function getAsJSON($start = null, $rowcount = null, $wh = null) {

			$called_class = get_called_class();

			$where = self::getWhere($wh);

			$has_date = property_exists($called_class, '_obsdate');

			$get_order = ($has_date ? " ORDER BY obsdate" : "");
				
			$total = self::getRecordCount($wh);
				
			if(!isset($start) && !isset($rowcount)) {
				$start = 1;
				$rowcount = $total;
			}
			elseif (!isset($start) || !isset($rowcount)) {
				$rowcount = (isset($start) ? $start : $rowcount);
				$start = 1;
			}
                    
			
			if ($start < 1) {
				$start = 1;
			}
			
			$end = $start + $rowcount - 1;
			if ($end > $total) {
				$rowcount = $total - $start;
			}
			
			if ($start == 1 && $end == $total) {
				$limit = "";
			}
			else {
				$limit = " LIMIT ";
				if ($start == 1) {
					$limit .= $rowcount;
				}
				else {
					$limit .= $start . ", " . $rowcount;
				}
			}

			try {
				$db = self::getConnection();
				$rows = $db->query(self::getSelect($limit) . $where . $get_order, PDO::FETCH_ASSOC);
				$return = json_encode($rows);

				$db = null;
				return $return;
			}
			catch (PDOException $e) {
				echo $e->getMessage();
				return false;
			}
                    
                }
		
		public static function getAll($start = null, $rowcount = null, $wh = null) {

			$called_class = get_called_class();
                        
			$where = self::getWhere($wh);
                        
                        $fields = implode(', ', static::getFields());
                        $pk = static::getPrimaryKey();

			$has_date = property_exists($called_class, '_obsdate');

			$get_order = ($has_date ? " ORDER BY obsdate" : "");
				
			$total = self::getRecordCount($wh);
				
			if(!isset($start) && !isset($rowcount)) {
				$start = 1;
				$rowcount = $total;
			}
			elseif (!isset($start) || !isset($rowcount)) {
				$rowcount = (isset($start) ? $start : $rowcount);
				$start = 1;
			}
			
			if ($start < 1) {
				$start = 1;
			}
			
			$end = $start + $rowcount - 1;
			if ($end > $total) {
				$rowcount = $total - $start;
               			$end = $start + $rowcount - 1;
			}
                        
                        $sql = "SELECT " . $fields . " FROM $called_class) WHERE RowNumber BETWEEN $start AND $end";
                        
                        
			try {
				$db = self::getConnection();
				$return = array();
				foreach ($db->query($sql, PDO::FETCH_ASSOC) as $row) {
					$values = array();
					foreach ($row as $key => $value) {
						$values['_'.$key] = $value;
					}
					$return[] = new static($values);
				}

				return $return;
			}
			catch (PDOException $e) {
				echo $e->getMessage();
				return false;
			}
		}
		
		public static function getRecordCount($wh = null) {
			
			$called_class = get_called_class();

			$where = self::getWhere($wh);

			$sql = "SELECT COUNT(*) AS RecCount FROM " . strtolower($called_class) . $where;
                        
			
			$db = self::getConnection();
			
			$query = $db->prepare($sql);
			
			$query->execute();
			
			$result = $query->fetchAll(PDO::FETCH_ASSOC);
                        
			return $result[0]['RecCount'];

		}

		public static function getPrimaryKey() {

			$table = strtolower(get_called_class());
                        
                        $sql = "SELECT COLUMN_NAME col FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                                WHERE TABLE_NAME = '" . $table . "' AND LEFT(CONSTRAINT_NAME, 1) = 'P'";

			$db = self::getConnection();

			$query = $db->prepare($sql);

			$query->execute();

			$result = $query->fetchAll(PDO::FETCH_ASSOC);

			return $result[0]["col"];
		}
		
		public static function create(Array $properties) {
			
			$db = self::getConnection();

			$pk = "_".self::getPrimaryKey();

			$object = new static($properties);
			$object->$pk = null;
			$object->save();
			$object->$pk = $db->lastInsertID();
			return $object;

		}
		
		public function update(Array $properties) {
			foreach ($properties as $key => $value) {
				if (property_exists($this, $key)) {
					$this->$key = $value;
				}
			}
			$this->save();
		}
		
		public function updateProperty($key, $value) {
			if (property_exists($this, $key)) {
				$this->$key = $value;
			}
			$this->save();
		}
		
		public static function delete($id = null) {
			
			$db = self::getConnection();
			
			$delete = "DELETE FROM " . strtolower(get_called_class()) . (isset($id) ? " WHERE id = :id" : " WHERE userid = " . $_SESSION['userid']);

			$statement = $db->prepare($delete);
			if (isset($id)) $statement->bindparam(':id', $id, PDO::PARAM_INT);
			
			$statement->execute();
		}
	}
?>
