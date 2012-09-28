<?php
/**
 *
 * @copyright   Copyright (C) 2008 Rodin Shih
 * @author      Rodin Shih <schludern@gmail.com>
 * @license     http://www.opensource.org/licenses/bsd-license.php
 * @package     ROPH
 */

class RO_Web_Route_Regexp extends RO_Flow_Work{
	
	protected $_targets =  array('NEXT', 'NOT_FOUND');

	/**
	 * actions namespace as prefix
	 *
	 * @var string
	 */
	protected $_actionNamespace;

	protected $_rules;
	
	protected $_matched;
	protected $_mappedto;
	protected $_keys;
	
	protected $_usePathType;
	
	const TYPE_PATH = 0;
	const TYPE_QUERYSTRING = 1;
	const TYPE_BOTH = 2; 
	
	protected $_pathTypes = array('PHP_SELF', 'QUERY_STRING', 'REQUEST_URI');

	/**
	 * __construct__
	 *
	 * @param string $name
	 * @param string $params
	 * @param array  $rules
	 */
	public function __construct($name, $params) {
		parent::__construct($name, $params);
		if(isset($this->_params['ActionNamespace']) && is_string($this->_params['ActionNamespace']) && preg_match('/^[a-zA-Z]\w*$/', $this->_params['ActionNamespace'])){
			$this->_actionNamespace = $this->_params['ActionNamespace'];
		}else{
			$this->_actionNamespace = 'RO';
		}
		if(isset($this->_params['Type']) && isset($this->_pathTypes[$this->_params['Type']])){
			$this->_usePathType = $this->_params['Type']; 
		}else{
			$this->_usePathType = self::TYPE_PATH;
		}
		
		if(isset($this->_params['Rules']) && is_array($this->_params['Rules']) && $this->_params['Rules']){
			$this->_rules = $this->_params['Rules'];
		}else{
			$this->_rules = array();
		}
	}
	
	protected function _run() {
		$this->_matched = array();
		$this->_mappedto = '';
		$this->_keys = array();
		
		$path = $_SERVER[$this->_pathTypes[$this->_usePathType]];
		
		try{
			$prefix = isset($this->_rules[0]) ? strval($this->_rules[0]) : '';
			array_walk($this->_rules, array($this, '_find'), array('prefix' => $prefix, 'path' => $path, 'level' => 0));
		}catch (Exception $ex){
			$this->_data['_EXEC_'] = $this->_actionNamespace . '_' . $this->_mappedto;
			$this->_data['_PATH_'] = $path;
			$this->_data['_MAPPED_PATH_'] = $this->_matched;
			$_GET = array_merge($this->_keys, $_GET);
			$this->_setNext('NEXT');
			return ;
		}
		$this->_setNext('NOT_FOUND');
		return ;
	}
	
	protected function _find($item, $key, $extra) {
		if(is_int($key)) return;
		$match = array();
		if(!preg_match($key, $extra['path'], $match)) return;
		
		$this->_matched[$extra['level']] = $key;
		$this->_keys = array_merge($this->_keys, $match);
		if(is_array($item)){
			if(isset($item[0])){
				$extra['prefix'] .= $item[0];
			}
			$extra['path'] = preg_replace($key, '', $extra['path'], 1);
			
			array_walk($item, array($this, '_find'), array('prefix' => $extra['prefix'], 'path' => $extra['path'], 'level' => $extra['level'] + 1));
		}else{
			$item = strval($item);
			foreach ($match as $k => $v){
				$item = str_replace("($k)", $v, $item);
			}
			$this->_mappedto = $extra['prefix'] . strval($item);
			throw new Exception();
		}
	}
}
