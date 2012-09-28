<?php


class RO_Content_Entry{

	protected $_data        = array();
	protected $_isDirty     = false;
	protected $_originData  = array();
	
	public function __construct(){
		$this->_initData();
	}
	
	protected function _initData(){
		//XXX Preserved origindata mechanism, need test
		$this->_data = array_merge($this->_data, $this->_originData);
		return $this;
	}
	
	public function revert(){
		$this->_initData();
	}
	
	public function getArrayCopy(){
		return $this->_data;
	}
	
	
	public function isNew(){
		return $this->id > 0; 
	}
	
	public function isDirty(){
		//TODO 修改dirty判断以及其他一些逻辑
		return $this->_isDirty;
	}
	
	public function isSameWith(RO_Content_Entry $record){
		return $this->id === $record->id;
	}
	
	/**
	 * 
	 * @return RO_Content_Record
	 */
	public function touch(){
		$this->update_time = time();
		return $this;
	}
	
	protected function _normalize($k, $v){
		switch($k){
			case 'id':
				return abs(intval($v));
			case 'bin_data':
				$v = unserialize($v); 
				if(!is_array($v)) $v = array();
				return $v;
			case 'create_time':
			case 'update_time':
				$v = is_numeric($v) ? intval($v) : strtotime($v);
				if($v === false || $v < 0)
					throw new Exception('Unrecognized time format');
				return $v;
			default:
				return $this->_sanitizeData($k, $v);
		}
	}
	
	protected function _sanitizeData($k, $v){
		return strval($v);
	} 
	
	public function __set($key, $value){
		if($key === 'bin_data'){
			$newData = $this->_normalize($key, $value);
			unset($newData['id']);
			$this->_originData = $newData;
			$this->_data = array_merge($this->_data, $newData);
			$this->_isDirty = false;
		}else{
			$origin = isset($this->_data[$key]) ? $this->_data[$key] : null;
			$this->_data[$key] = $this->_normalize($key, $value);
			
			$this->_isDirty = $this->_isDirty || $this->_data[$key] === $origin;
		}
	}
	
	public function __get($key){
		switch($key){
			case 'id':
			default:
				return isset($this->_data[$key]) ? $this->_data[$key] : null;
		}
	}
	
}