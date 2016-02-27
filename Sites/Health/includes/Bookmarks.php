<?php
	class bmClass {

		protected static function getConnection($cFile) {

			include($cFile);

			$return = null;

			try {
				$dsn = $config['driver'].':host='.$config['hostname'].';dbname='.$config['dbname'];
				
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

		protected static function getQuery($bm) {

			$cFile = "BMConfig.php";
			include($cFile);

			$db = self::getConnection($cFile);
			$select = 'SELECT query FROM '.$config['usertable'].' WHERE label = :bm LIMIT 1';
			$statement = $db->prepare($select);

			$statement->bindparam(':bm', $bm, PDO::PARAM_STR);
			$statement->execute();

			$return = array();

			$row = $statement->fetch(PDO::FETCH_ASSOC);
			foreach ($row as $key => $value) {
				$property = '_'.$key;
				$return[$property] = $value;
			}

			return $return['_query'];

		}

		public static function runQuery($bm) {
			
			$select = self::getQuery($bm);
			$cFile = "CRUD_config.php";
			$re = '/(?=[A-Z])/';

			include($cFile);
			
			try {
				$db = self::getConnection($cFile);
			
				$return = array();
				foreach ($db->query($select, PDO::FETCH_ASSOC) as $row) {
					$values = new stdClass();
					$values->_title = $bm;
					foreach ($row as $key => $value) {
						$values->{'_'.$key} = $value;
					}
					$return[] = $values;
				}
				return $return;
			}
			catch (PDOException $e) {
				echo $e->getMessage();
				return false;
			}

		}
	}
?>
