<?php
/**
 * CLI common utils
 * 
 * @author rodin
 *
 */
class RO_Cli{
	
	/**
	 * Check if script running under cli mode
	 * @return bool
	 */
	static public function isCli(){
		return strtolower(PHP_SAPI) === 'cli';
	}
	
	/**
	 * check if script is in the main section
	 * @return bool
	 */
	static public function isMain(){
		$trace = debug_backtrace();
		foreach ($trace as $t){
			if(in_array($t['function'], array('require', 'include', 'require_once', 'include_once', 'eval'))){
				if(!isset($t['class'])){
					return false;
				}
			}
		}
		return true;
	}
}
