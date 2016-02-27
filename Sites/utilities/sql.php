<?php
	class SQL {
		
		private $_action= 'SELECT',
				$_fields = array(),
				$_tableName,
				$_where = array(),
				$_operators = array(),
				$_andor = array(),
				$_order = null,
				$_asc = true;
				
		public function __construct($table,
									$action = 'SELECT',
									$fields = array(),
									$where = array(),
									$operators = array(),
									$andor = array(),
									$order = null,
									$asc = true
						) {
			$this->_action = $action;
			$this->_tableName = $table;
			$this->_fields = $fields;
			$this->_where = $where;
			$this->_operators = $operators;
			$this->_andor = $andor;
			$this->_asc = $asc;
			$this->setOrder($order);
		}
		
		public function __toString() {
			$sql = $this->_action;
			$sql .= (!empty($this->_fields) ? ' ' . implode(', ', $this->_fields) : ($this->_action == 'DELETE' ? '' : ' *'));
			$sql .= ' FROM ' . $this->_tableName;
			if (!empty($this->_where)) {
				$wh = 'WHERE ';
				$i = 0;
				
				$cond = [];
				
				foreach ($this->_where as $key => $value) {
					$cond[] = $this->condition($key, $this->_operators[$i], $value);
					$i++;
				}
				
				if (count($cond) == 1) {
					$sql .= $this->setWhere($cond[0]);
				} else {
					$condSQL = $this->complex($cond[0], $cond[1], $this->_andor[0]);
					if (count($cond2) > 2) {
						$i = 2;
						while($i < count($cond)) {
							$condSQL = $this->complex($conSQL, $cond[i], $this->andor[$i - 1]);
						}
					}
				}
			}
			$sql .= $this->_order;

			return $sql;
		}
		
		public function condition($field, $operator, $value) {
			$operators = array('=', '>', '<', '>=', '<=', 'LIKE');
			
			if(in_array($operator, $operators)) {
				return ($field . ' ' . $operator . ' ?');
			} else {
				return false;
			}
		}
		
		public function between($field) {
			return $field . ' BETWEEN ? AND ?';
		}
		
		public function complex($cond1, $cond2, $flag = false) {
			return '(' . $cond1 . ') ' . ($flag == true ? 'AND' : 'OR') . ' (' . $cond2 . ')';
		}
		
		public function setWhere($condition = null) {
			if(!$condition) {
				return null;
			} else {
				return ' WHERE ' . $condition;
			}
		}
		
		private function getASC() {
			return ' ' . ($this->_asc == true ? 'ASC' : 'DESC');
		}
		
		public function setOrder($field = null) {
			if(!$field) {
				$this->_order = null;
			} else {
				$this->_order = ' ORDER BY ' . $field . $this->getASC();
			}
		}
	}
?>
