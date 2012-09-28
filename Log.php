<?php
/**
 * 
 * @copyright   Copyright (C) 2010 Rodin Shih
 * @author      Rodin Shih <schludern@gmail.com>
 * @license     http://www.opensource.org/licenses/bsd-license.php
 * @package     ROPH
 */

/**
 * Universal logger with inheritant child logger available.
 * 
 * @package RO_Log
 * @subpackage RO_Log
 * @author Rodin <schludern@gmail.com>
 * @license New BSD Licence.
 * @copyright Rodin Shih (c) 2010.
 */
class RO_Log {
	public $name = 'root';
	public $level = 0;
	public $levelMax = PHP_INT_MAX;
	public $handlers = array();
	public $childLoggers = array();
	public $logLevels = array();
	static public $loggers = array();
	
	const NOTSET = 'NOTSET';
	const DEBUG = 'DEBUG';
	const INFO = 'INFO';
	const NOTICE = 'NOTICE';
	const WARN = 'WARN';
	const WARNING = 'WARNING';
	const ERROR = 'ERROR';
	const FATAL = 'FATAL';
	
	static public $defaultLogLevels = array(
		self::NOTSET  => 0,
		self::DEBUG   => 0,
		self::INFO    => 20,
		self::NOTICE  => 40,
		self::WARN    => 60,
		self::WARNING => 60,
		self::ERROR   => 80,
		self::FATAL   => 80,
	);
	
	static public function setLevel($levelName, $levelValue){
		self::$defaultLogLevels[$levelName] = $levelValue;
	}
	
	static public function unsetLevel($levelName){
		unset(self::$defaultLogLevels[$levelName]);
	}
	
	/**
	 * 
	 * @return RO_Log
	 */
	static public function rootLogger() {
		return self::getLogger();
	}
	
	/**
	 * 
	 * @param string $name
	 * @param RO_Log $parent
	 * @return RO_Log
	 */
	static public function getLogger($name = null, RO_Log $parent = null){
		$name = strval($name);
		if(!isset(self::$loggers[$name])){
			$logger = new self();
			if($parent || $name)
				$logger->name = strval($name);
			if(!is_null($parent))
				$parent->addChild($logger);
			self::$loggers[$name] = $logger;
			return $logger;
		}else{
			return self::$loggers[$name];
		}
	}
	
	public function __construct() {
		$this->logLevels = self::$defaultLogLevels;
	}
	
	/**
	 * 
	 * @param string $levelName
	 */
	public function getLevel($levelName){
		if(isset($this->logLevels[$levelName])){
			return $this->logLevels[$levelName];
		}else {
			return 0;
		}
	}

	/**
	 * 
	 * @param  function $func
	 * @param  array    $args
	 * @return RO_Log
	 */
	public function __call($func, $args){
		isset($this->logLevels[$func]) OR $func = self::NOTSET;
		return call_user_func(array($this, 'log'), $func, $args[0], isset($args[1]) ? $args[1] : null);
	}
	
	/**
	 * 
	 * @param  int|string $level
	 * @param  int|string $levelMax
	 * @return RO_Log
	 */
	public function setThreshold($level, $levelMax = null) {
		$this->level = is_int($level) ? $level : $this->getLevel($level);
		if(!is_null($levelMax)){
			$this->levelMax = is_int($levelMax) ? $levelMax : $this->getLevel($levelMax);
		}
		return $this;
	}
	
	/**
	 * 
	 * @param RO_Log_Handler_Abstract $handler
	 * @return RO_Log
	 */
	public function addHandler(RO_Log_Handler__Abstract $handler) {
		$this->handlers[] = $handler;
		return $this;
	}
	
	/**
	 * 
	 * @param RO_Log $child
	 * @return RO_Log
	 */
	public function addChild(RO_Log $child) {
		$this->childLoggers[] = $child;
		return $this;
	}
	
	/**
	 * 
	 * @param string $levelName
	 * @param string $message
	 * @param array $extras
	 * @return RO_Log
	 */
	public function log($levelName, $message, array $extras = null) {
		$args = func_get_args();
		//create log record
		$record = array(
			'level' => 0,
			'levelname' => $levelName,
			'message' => $message,
			'extras' => $extras ? $extras : array(),
		);
		$record['level'] = $this->getLevel($levelName);
		$this->_process($record);
		return $this;
	}
	
	protected function _process(array $record){
		if(!($this->level <= $record['level'] && $record['level'] <= $this->levelMax)){
//			echo $this->level . $record['level'] . $this->levelMax;
			return ;
		}
		$this->_handle($record);
		$this->_propagate($record);
		return ;
	}
	
	protected function _propagate(array $record){
		foreach ($this->childLoggers as $child){
			$child->_process($record);
		}
	}
	
	protected function _handle(array $record) {
		$record['name'] = $this->name;
		foreach ($this->handlers as $handler){
			$handler->handle($record);
		}
	}
}
