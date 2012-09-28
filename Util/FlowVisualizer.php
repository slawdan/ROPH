#!/usr/bin/env php
<?php
define('RO_BASE_PATH', dirname(dirname(__FILE__)));
require RO_BASE_PATH . '/Loader.php';

RO_Loader::register(RO_BASE_PATH, 'RO_');

if(RO_Util_CLI::isCLI() && RO_Util_CLI::isMain()){
	exit(RO_Util_FlowVisualizer::__main__());
}

class RO_Util_FlowVisualizer {
	
	protected $_configFile = '';
	protected $_name = '';
	
	public function __construct($configFile, $name = '') {
		$this->_configFile = realpath($configFile);
		$this->name($name);
	}
	
	/**
	 * 
	 * @return RO_Util_FlowVisualizer
	 */
	public function name($name = null){
		if(is_null($name)){
			return $this->_name ? $this->_name : str_replace('.', '_', basename($this->_configFile));
		}else{
			$this->_name = $name;
			return $this;
		}
	}

	public function generate(){
		try{
			$c = require (realpath($this->_configFile));
		}catch(Exception $ex){
			throw new Exception('Format error');
		}
		
		$str = array();
		$str[] = 'digraph ' . $this->name() . ' {';
		foreach($c['works'] as $workName => $work){
			$str[] = "\"$workName\";";
		}
		foreach($c['works'] as $workName => $work){
			foreach($work['targets'] as $branch => $target){
				$str[] = "\"$workName\" -> \"$target\" [label=\"$branch\"];";
			}
		}
		$str[] = "}";
		return implode("\n", $str);
	}
	
	public function __toString() {
		return $this->generate();
	}
	
	static function showHelp(){
		echo '/path/to/php FlowVisualizer.php ConfigFile [Name]';
	}
	
	static function __main__(){
		if($GLOBALS['argc'] < 2){
			self::showHelp();
			return 1;
		}else{
			if($GLOBALS['argc'] >= 3){
				$name = $GLOBALS['argv'][2];
			}
			$config = $GLOBALS['argv'][1];
		}
		$obj = new self($config, $name);
		echo $obj;
	}
}