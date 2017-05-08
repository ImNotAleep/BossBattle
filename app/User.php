<?php

class User extends TelegramApp\User {
	public function __construct($input = NULL, $db = NULL){
		  parent::__construct($input, $db);
	}

	public function update($key, $value, $table = 'user', $idcol = 'id'){
		// get set variables and set them to DB-table
		$query = $this->db
			->where($idcol, $this->id)
		->update($table, [$key => $value]);
		if($this->db->getLastErrno() !== 0){
			throw new Exception('Error en la consulta: ' .$this->db->getLastError());
		}
		return $query;
	}

	protected function insert($data, $table){
		return $this->db->insert($table, $data);
	}

	protected function delete($table, $where, $value, $usercol = FALSE){
		if($usercol !== FALSE){
			$this->db->where($usercol, $this->id);
		}
		return $this->db
			->where($where, $value)
		->delete($table);
	}

	public function register($name, $class = NULL, $referal = NULL){
		$data = [
			'id' => $this->id,
			'name' => $name,
			'class' => $class,
			'exp' => 0,
			'level' => 1,
			'register_date' => date("Y-m-d H:i:s"),
			'last_date' => date("Y-m-d H:i:s"),
			'blocked' => FALSE,
			'referal' => $referal,
		];
		return $this->db->insert('user', $data);
	}

	public function load($force = FALSE){
		// load variables and set them here.
		if($this->loaded && !$force){ return TRUE; }
		$query = $this->db
			->where('id', $this->id)
		->getOne('user');
		if(empty($query)){ return NULL; }
		foreach($query as $k => $v){
			$this->$k = $v;
		}

		$this->loaded = TRUE;
		return TRUE;
	}
}
