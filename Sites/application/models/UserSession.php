<?php
	class UserSession extends Model {
	
		public function __construct($session = null) {
			parent::__construct('users_session');
			$this->find($this->_db->getPrimaryKey($this->_tableName), $session);
		}
		
		public function getByUserID($user) {
			return $this->find('user_id', $user);
		}
	}