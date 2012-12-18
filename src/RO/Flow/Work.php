<?php
/**
 * 
 * @copyright   Copyright (C) 2008 Rodin Shih
 * @author      Rodin Shih <schludern@gmail.com>
 * @license     http://www.opensource.org/licenses/bsd-license.php
 * @package     ROPH
 */
 
class RO_Flow_Work{
	protected $_targets = array('');
	protected $_name;
	protected $_params;
	protected $_next;
	
	/**
	 * Common runtime data
	 *
	 * @var ArrayObject
	 */
	protected $_data;
	
	public function __construct($name, array $params = null) {
		$this->_name = $name;
		$this->_data = null;
		$this->_next = '';
		$this->_params = $params ? $params : array();
	}
	
	public function __toString() {
		return $this->_name;
	}
	
	/**
	 * get the predefined targets name list
	 *
	 * @return array
	 */
	public function getTargetsDefine() {
		return $this->_targets;
	}
	
	/**
	 * set the next work
	 *
	 * @param  string $next
	 * @return RO_Flow_Work
	 */
	protected function _setNext($next) {
		if(in_array($next, $this->_targets)){
			$this->_next = $next;
			return $this;
		}else{
			throw new Exception("Next target not in Work({$this})'s defined list");
		}
	}
	
	protected function _run(){
		echo 'running ' . $this . '...';
	}
	
	public function run(ArrayObject $data) {
		$this->_data = $data;
		$this->_setNext($this->_targets[0]);
		$this->_run();
		return $this->_next;
	}
}
