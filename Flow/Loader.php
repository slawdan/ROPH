<?php
/**
 * 
 * @copyright   Copyright (C) 2008 Rodin Shih
 * @author      Rodin Shih <schludern@gmail.com>
 * @license     http://www.opensource.org/licenses/bsd-license.php
 * @package     ROPH
 */
 
abstract class RO_Flow_Loader{
	protected $_source;
	protected $_workData;

	public function __construct($source) {
			$this->_source = $source;
	}

	abstract protected function _load();
	
	protected function _build() {
		$flow = new RO_Flow_Runner($this->_workData['data']);
		foreach ($this->_workData['works'] as $n => $w){
			$work = $this->_createWorkObject($w['type'], $n, $w['params']);
			$flow->addWork($work, $w['targets'], $n);
		}
		$flow->setStart($this->_workData['start']);
		return $flow;
	}
	
	/**
	 * build the Runner object
	 *
	 * @return RO_Flow_Runner
	 */
	public function build(){
		$this->_load();
		return $this->_build();
	}

	protected function _createWorkObject($className, $name, $params = array()) {
		if(class_exists($className) && is_subclass_of($className, 'RO_Flow_Work')){
			$n = new $className($name, $params);
			return $n;
		}else {
			throw new Exception("Type \"$className\" not a Flow_Work type");
		}
	}

	/**
	 *
	 * @param mixed  $resource
	 * @param string $type
	 * @return RO_Flow_Loader
	 */
	static public function getLoader($resource, $type= 'RO_Flow_Loader_Array') {
		if(!is_subclass_of($type, 'RO_Flow_Loader')){
			throw new Exception("The type \"$type\" must use a subtype of RO_Flow_Loader");
		}
		$inst = new $type($resource);
		return $inst;
	}
}