<?php

	abstract class pdoClass {
		
		public static function getColumnMeta() {
			$meta = array(
				"fields"		=> array(),
				"fieldMeta"		=> array(),
				"primaryKey"	=> NULL
			);
			
			if (count($meta["fields"]) == 0 || count($meta["fieldMeta"]) == 0) {
				$sql = "SHOW COLUMNS FROM ".get_called_class();
				
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
					if ($col['Key'] == "PRI" && empty($meta['primaryKey'])) {
						$meta['primaryKey'] = $colName;
					}
					$colType = self::parseColumnType($col['Type']);
					$meta['fieldMeta'][$colName] = $colType;
				}
			}
			return $meta;
		}
		
		protected static function parseColumnType($colType) {
			$colInfo = array();
			$colParts = explode(" ", $colType);
			if ($fparen = strpos($colParts[0], "(")) {
				$colInfo['type'] = substr($colParts[0], 0, $fparen);
				$colInfo['pdoType'] = '';
				$colInfo['length'] = str_replace(")", "", substr($colParts[0],$fparen+1));
				$colInfo['attributes'] = isset($colParts[1]) ? $colParts[1] : NULL;
			}
			else {
				$colInfo['type'] = $colParts[0];
			}
			foreach (self::pdoBindTypes() as $pKey => $pType) {
				if (strpos(' '.strtolower($colInfo['type']).' ', $pKey)) {
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
				'char'		=>	PDO::PARAM_STR,
				'int'		=>	PDO::PARAM_INT,
				'bool'		=>	PDO::PARAM_BOOL,
				'date'		=>	PDO::PARAM_STR,
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
				
		public static function getSelect($called_class = false) {
			if (!$called_class) {
				$called_class = get_called_class();
			}
			return "SELECT " . implode(', ', self::getFields()) . " FROM " . strtolower($called_class);
		}

		protected function save() {

			$db = self::getConnection();
			$updates = array();
			$pk = self::getPrimaryKey();
			
			$fields = self::getFields();

			$insert = "INSERT INTO " . strtolower(get_called_class()) . " (" . implode(", ", $fields) . ")";
			$function1 = function ($value) {
				return ':' . $value;
			};

			$function2 = function ($val1, $val2) {
				return $val1 . " = " . $val2;
			};

			$bindparams = array_map($function1, $fields);
			$insert .= " VALUES (" . implode(', ', $bindparams) . ")";
			$f = $fields;

			$trunc1 = array_shift($bindparams);
			$trunc2 = array_shift($f);
			$replace = array_map($function2, $f, $bindparams);

			$insert .= " ON DUPLICATE KEY UPDATE " . implode(', ', $replace);
			try {
				$statement = $db->prepare($insert);
			
				foreach ($fields as $field) {
					$property = '_'.$field;
					$type = gettype($this->$property);
					if ($type == 'object') {
						$type = get_class($this->$property);
					}
					if ($type == 'DateTime') {
						$dt_object = $this->$property;
						$dt_format = $dt_object->format('Y-m-d');
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

			include("CRUD_config.php");

			$return = null;

			try {
				$dsn = $config['driver'].':host='.$config['hostname'].';dbname='.$config['dbname'];
//				echo $config['hostname'], '<br />';
//				die();
				$return = new PDO($dsn, $config['username'], $config['password']);
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

			$select = self::getSelect() . " WHERE userid = " . $_SESSION['userid'] . " ORDER BY obsdate " . ($flag ? "ASC" : "DESC") . " LIMIT 1";
			$return = array();
			$row = $db->query($select)->fetch(PDO::FETCH_ASSOC);

			foreach($row as $key => $value) {
				$property = '_'.$key;
				$return[$property] = $value;
			}

			return new static($return);
		}

		public static function getPreviousNext($id = false, $flag = true) {
			
			$db = self::getConnection();

			$curr_rec = (!$id ? self::getInitialCurrent() : self::get($id));
			$strDate = $curr_rec->getObsDate()->format('Y-m-d');

			$select = self::getSelect() . " WHERE obsdate " . ($flag ? '<' : '>') .
				" :odate AND userid = " . $_SESSION['userid'] . " ORDER BY obsdate " . ($flag ? "DESC" : "ASC") . " LIMIT 1";

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
			
			$select = "SELECT $field FROM $class WHERE (((obsdate " . ($flag ? '<' : '>') .
				" :odate) AND (userid = " . $_SESSION['userid'] . ")) AND ($field <> 0)) ORDER BY obsdate " . ($flag ? "DESC" : "ASC") . " LIMIT 1";
				
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
			
			if($field == "obsdate") {
				$select = "SELECT {$min_max}({$field}) AS $field FROM $class WHERE userid = $user";
			} else {
				$startdate = self::fixDate($startdate, true);
				$enddate = self::fixDate($enddate, false);
				$mindate = $startdate->format('Y-m-d');
				$maxdate = $enddate->format('Y-m-d');
				$select = "SELECT {$min_max}($field) AS $field FROM $class WHERE (((userid = $user AND ($field <> 0))) AND ((obsdate >= :mindate) AND (obsdate <= :maxdate)))";
			}

			$statement = $db->prepare($select);
			if($field != "obsdate") {
				$statement->bindparam(':mindate', $mindate, PDO::PARAM_STR);
				$statement->bindparam(':maxdate', $maxdate, PDO::PARAM_STR);
			}

			$statement->execute();
			$row = $statement->fetch(PDO::FETCH_ASSOC);

			return $row[$field];
		}

		public static function getFirstLastValue($field, $start = false, $end = false, $flag = true) {
			
			$db = self::getConnection();

			$start = self::fixDate($start, true);
			$end = self::fixDate($end, false);
			
			$str_start = $start->format('Y-m-d');
			$str_end = $end->format('Y-m-d');
			
			$select = "SELECT ($field) AS $field FROM " . strtolower(get_called_class()) . " WHERE (((userid = " . $_SESSION['userid'] .
				") AND ($field <> 0)) AND ((obsdate >= :start) AND (obsdate <= :end))) ORDER BY obsdate " . ($flag ? "DESC" : "ASC") . " LIMIT 1";
			$statement = $db->prepare($select);
			$statement->bindparam(':start', $str_start, PDO::PARAM_STR);
			$statement->bindparam(':end', $str_end, PDO::PARAM_STR);

			$statement->execute();
			$row = $statement->fetch(PDO::FETCH_ASSOC);

			return $row[$field];
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
		
		public static function getMySQLVersion() {
			$db = static::getConnection();
			
			$sql = 'SELECT version() AS version;';
			
			$query = $db->query($sql);
			$result = $query->fetch(PDO::FETCH_ASSOC);
			
			$return = $result['version'];
			
			$db = null;
			
			return $return;
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
		
		public static function getAll($start = null, $rowcount = null, $wh = null) {

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
				$return = array();
				foreach ($db->query(self::getSelect() . $where . $get_order . $limit, PDO::FETCH_ASSOC) as $row) {
					$values = array();
					foreach ($row as $key => $value) {
						$values['_'.$key] = $value;
					}
					$return[] = new static($values);
				}

				$db = null;
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

			$db = null;
			
		}

		public static function getPrimaryKey() {

			$table = strtolower(get_called_class());

			$sql = "SHOW INDEX FROM {$table} WHERE Key_name = 'PRIMARY'";

			$db = self::getConnection();

			$query = $db->prepare($sql);

			$query->execute();

			$result = $query->fetchAll(PDO::FETCH_ASSOC);

			return $result[0]["Column_name"];
		}
		
		public static function create(Array $properties) {
			
			$db = self::getConnection();

			$pk = "_".self::getPrimaryKey();

			$object = new static($properties);
			$object->$pk = null;
			$object->save();
			$object->$pk = $db->lastInsertID();
			return $object;

			$db = null;
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

			$db = null;
		}
	}
?>
