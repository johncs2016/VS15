<?php

abstract class Model {
	protected 	$_db,
				$_tableName,
				$_data = Array();
	
	public function __construct($tableName) {
		$this->_db = DB::getInstance();
		$this->_tableName = $tableName;
	}

	public function find($field, $value) {
		$data = $this->_db->get($this->_tableName, array(), array($field => $value), array('='));

		if($data->count()) {
			$this->_data = $data->first();
			return true;
		} else {
			return false;
		}
	}
	
	public function getByID($id = null) {
		if(!$id) $id = $this->data()->id;
		return $this->find($this->_db->getPrimaryKey($this->_tableName), $id);
	}

	public function exists() {
		return (!empty($this->_data)) ? true : false;
	}

	public function data() {
		return $this->_data;
	}
	
	public function create($fields = array()) {

		if(!$this->_db->insert($this->_tableName, $fields)) {
			throw new Exception('There was a problem creating this record.');
		}
	}
	
	public function update($fields = array(), $id = null) {
		if(!$id) {
			$id = $this->data()->id;
		}
		
		if(!$this->_db->update($this->_tableName, $id, $fields)) {
			throw new Exception('There was a problem updating.');
		}
	}
	
	public function delete($id = null) {
		if(!$id) {
			$id = $this->data()->id;
		}
		
		if(!$this->_db->delete($this->_tableName, array($field, '=', $id))) {
			throw new Exception('There was a problem with deleting this record.');
		}
	}
}
