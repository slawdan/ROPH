<?php
/**
 *
 * @copyright   Copyright (C) 2008 Rodin Shih
 * @author      Rodin Shih <schludern@gmail.com>
 * @license     http://www.opensource.org/licenses/bsd-license.php
 * @package     ROPH
 */

class RO_Web_Route extends RO_Flow_Work{

	/**
	 * actions namespace as prefix
	 *
	 * @var string
	 */
	protected $_actionNamespace;

	protected $_defaultPackage;

	protected $_defaultAction;
	
	protected $_routeStyle;
	
	protected $_packageKey;
	
	protected $_actionKey;

	/**
	 *
	 * @param $name
	 * @param $params
	 */
	public function __construct($name, $params) {
		parent::__construct($name, $params);
		if(isset($this->_params['ActionNamespace']) && is_string($this->_params['ActionNamespace']) && preg_match('/^[a-zA-Z]\w*$/', $this->_params['ActionNamespace'])){
			$this->_actionNamespace = $this->_params['ActionNamespace'];
		}
		if(isset($this->_params['DefaultPackage'])){
			$this->_defaultPackage = $this->_params['DefaultPackage'];
		}
		if(isset($this->_params['DefaultAction'])){
			$this->_defaultAction = $this->_params['DefaultAction'];
		}
		if(isset($this->_params['PackageKey'])){
			$this->_packageKey = $this->_params['PackageKey'];
		}
		if(isset($this->_params['ActionKey'])){
			$this->_actionKey = $this->_params['ActionKey'];
		}

		if(empty($this->_actionNamespace)){
			$this->_actionNamespace = 'RO';
		}
		if(empty($this->_defaultPackage)){
			$this->_defaultPackage = 'Default'; 
		}
		if(empty($this->_defaultAction)){
			$this->_defaultAction = 'Index';
		}
		
		if(empty($this->_packageKey)){
			$this->_packageKey = 'c';
		}
		if(empty($this->_actionKey)){
			$this->_actionKey = 'a';
		}
	}

	protected $_targets = array('NEXT', 'NOT_FOUND');

	protected function _run() {
		$_GET[$this->_packageKey] = isset($_GET[$this->_packageKey]) && $_GET[$this->_packageKey] ?  ucfirst($_GET[$this->_packageKey]) : $this->_defaultPackage;
		$_GET[$this->_actionKey] = isset($_GET[$this->_actionKey]) && $_GET[$this->_actionKey] ? ucfirst($_GET[$this->_actionKey]) : $this->_defaultAction;

		$exec = "{$this->_actionNamespace}_{$_GET[$this->_packageKey]}_{$_GET[$this->_actionKey]}";
		if(preg_match('/^\w+$/', $exec)){
			$this->_data['_EXEC_'] = $exec;
			$this->_data['_PATH_'] = $_SERVER['QUERY_STRING'];
			$this->_setNext('NEXT');
			return ;
		}
		$this->_setNext('NOT_FOUND');
		return ;
	}
}
