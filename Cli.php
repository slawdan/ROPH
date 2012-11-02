<?php
/**
 * CLI common utils
 * 
 * @author Rodin Shih <schludern@gmail.com>
 * @package ROPH
 * @subpackage Cli
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

	/**
	 * get an option parser instance
	 * 
	 * @param string $shortUsage Short usage of the cli program
	 * @param string $details    Details of the cli program
	 * @return RO_Cli_Parser
	 */
	static public function optParser($shortUsage = NULL, $details = NULL){
		return new RO_Cli_Parser($shortUsage, $details);
	} 
	
	/**
	 * 
	 * @param string $cmd
	 * @param string $args
	 * @return array
	 */
	static public function exec($cmd){
	    $args = func_get_args();
	    array_shift($args);
	    foreach ($args as $k => $a) {
	        $args[$k] = escapeshellarg($a);
	    }
	    $argstr = implode(' ', $args);
	    
	    $output = '';
	    $return = 0;
	    exec($cmd . ' ' . $args, $output, $return);
	    return array($output, $return);
	}
	
	/**
	 * 
	 * @param string $cmd
	 * @return string
	 */
	static public function execOut($cmd){
	    list($output, ) = call_user_func_array(array(self, 'exec'), func_get_args());
	    return $output;
	}
	
	/**
	 * 
	 * @param string $cmd
	 * @return int
	 */
	static public function execReturn($cmd) {
	    list(, $return) = call_user_func_array(array(self, 'exec'), func_get_args());
	    return $return;
	}
	
	/**
	 * Clear all the cli args.
	 * 
	 * @return void
	 */
	static public function resetArgv(){
	    if(isset($_SERVER['argc'])) {
	        $_SERVER['argc'] = 1;
	    }
	    
	    if(isset($_SERVER['argv'])) {
    	    foreach ($_SERVER['argv'] as $k => $v) {
    	        if($k) {
    	            unset($_SERVER['argv'][$k]);
    	        }
    	    }
	    }
	    return;
	}
}
