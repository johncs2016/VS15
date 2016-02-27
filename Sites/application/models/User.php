<?php

class User extends Model {

	protected 	$_sessionName = null,
				$_cookieName = null,
				$_isLoggedIn = false;
	
	public function __construct($user = null) {
		parent::__construct('users');

		$this->_sessionName = SESSION_NAME;
		$this->_cookieName = COOKIE_NAME;

		// Check if a session exists and set user if so.
		if(Session::exists($this->_sessionName) && !$user) {
			$user = Session::get($this->_sessionName);

			if($this->find(is_numeric($user) ? $this->_db->getPrimaryKey($this->_tableName) : 'username', $user)) {
				$this->_isLoggedIn = true;
			} else {
				$this->logout();
			}
		} else {
			$this->find(is_numeric($user) ? $this->_db->getPrimaryKey($this->_tableName) : 'username', $user);
		}
	}
	
	public function getByUserName($username) {
		if(!$username) $username = $this->data()->username;
		return $this->find('username', $username);
	}
	
	public function update($fields = array(), $id = null) {
		if(!$id && $this->isLoggedIn()) {
			$id = $this->data()->id;
		}
		
		if(!$this->_db->update($this->_tableName, $id, $fields)) {
			throw new Exception('There was a problem updating.');
		}
	}

	public function login($username = null, $password = null, $remember = false) {
		
		if(!$username && !$password && $this->exists()) {
			Session::put($this->_sessionName, $this->data()->id);
		} else {
			$user = $this->getByUserName($username);
			if($user) {
				if($this->data()->password === Hash::make($password, $this->data()->salt)) {
					Session::put($this->_sessionName, $this->data()->id);

					if($remember) {
						$hash = Hash::unique();
						$userSession = new UserSession();
						$hashCheck = $userSession->getByUserID($this->data()->id);

						if(!$hashCheck) {
							$userSession->create(array(
								'user_id' => $this->data()->id,
								'hash' => $hash
							));
						} else {
							$hash = $userSession->data()->hash;
						}

						Cookie::put($this->_cookieName, $hash, COOKIE_EXPIRY);
					}

					return true;
				}
			}
		}

		return false;
	}
	
	public function hasPermission($key) {
		$group = new UserGroup($this->data()->group_id);
		
		if($group->exists()) {
			$permissions = json_decode($group->first()->permissions, true);

			if($permissions[$key] === 1) {
				return true;
			}
		}

		return false;
	}

	public function isLoggedIn() {
		return $this->_isLoggedIn;
	}

	public function logout() {
		$session = new UserSession();
		$session->getByUserID($this->data()->id);
		if ($session->exists()) {
			$session->delete();
		}

		Cookie::delete($this->_cookieName);
		Session::delete($this->_sessionName);
	}
}