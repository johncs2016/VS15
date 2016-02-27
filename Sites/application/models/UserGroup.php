<?php
	class UserGroup extends Model {
	
		public function __construct($group = null) {
			parent::__construct('groups');
			$this->find($this->_db->getPrimaryKey($this->_tableName), $group);
		}
	}