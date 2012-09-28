<?php
/**
 *
 * @copyright   Copyright (C) 2008 Rodin Shih
 * @author      Rodin Shih <schludern@gmail.com>
 * @license     http://www.opensource.org/licenses/bsd-license.php
 * @package     ROPH
 */

class RO_Loader{
	static protected $_registerePaths = array();
	static protected $_useCache = null;
	static protected $_expiredTime;
	static protected $_cacheId = null;

	protected $_path;
	protected $_prefix;
	
	protected $_needCreateCache = false;
	protected $_needCachedFiles = array();
	protected $_cacheFileName = '';
	
	public $removePrefix = true;

	protected function __construct($path = null, $prefix = null) {
		if($path === null)
			$this->_path = dirname(dirname(__FILE__));
		else
		{
			if(is_dir($path))
				$this->_path = realpath($path);
			else{
				throw new Exception('Loader\'s path not valid');
			}
		}
		
		if($prefix){
			$this->_prefix = $prefix;
		}else{
			$this->_prefix = '';
		}
		$this->_initCache();
	}
	
	public function __destruct(){
		if($this->_needCreateCache){
			$this->_createCache();
		}
	}
	
	protected function _initCache(){
		if(self::$_useCache){
			$this->_cacheFileName = $this->_getCacheFileName();
			if(!file_exists($this->_cacheFileName)){
				$this->_needCreateCache = true;
			}else{
				require $this->_cacheFileName;
			}
		}
	}
	
	protected function _getCacheFileName(){
		return 	self::$_useCache . DIRECTORY_SEPARATOR . 
				self::$_cacheId . '_' . 
				($this->_prefix ? $this->_prefix : '*_') . 
				dechex(crc32($this->_path)) . 
				dechex(self::$_expiredTime ? intval(time() / self::$_expiredTime) : 0) . 
				'.class';
	}
	
	protected function _createCache(){
		$f = @fopen($this->_cacheFileName, 'w');
		if(!$f){
			trigger_error('Class cache cannot write:' . $this->_cacheFileName, E_USER_ERROR);
			return false;
		}
		foreach($this->_needCachedFiles as $k => $file){
			$c = php_strip_whitespace($file);
			if($k && $c[0] == '<' && $c[1] == '?'){
				if($c[2] == 'p' && $c[3] == 'h' && $c[4] == 'p'){
					$c = substr($c, 5);
				}else{
					$c = substr($c, 2);
				}
			}
				
			$c = preg_replace('/\?>\s*$/', '', $c);
			fwrite($f, $c);
		}
		fclose($f);
		return true;
	}

	public function loadClass($className) {
		$tempName = substr($className, 0, strlen($this->_prefix)) === $this->_prefix
						? ( $this->removePrefix
							 ? substr($className, strlen($this->_prefix))
							 : $className
						)
						: false;
		if($tempName === false) return false;
		
		$tempName = str_replace('.' , '_', 
						str_replace('_', DIRECTORY_SEPARATOR, 
							current(explode('__', $tempName))
						)
					);
		
		$file = $this->_path . DIRECTORY_SEPARATOR . $tempName . '.php';
		if($file && is_file($file) && is_readable($file)){
			require $file;
			if($this->_needCreateCache) $this->_needCachedFiles[] = $file;
			return true;
		}else {
			return false;
		}
	}

	public function autoload($classname) {
		return $this->loadClass($classname);
	}

	static protected function _set($path, $inst){
		self::$_registerePaths[$path] = $inst;
	}

	static protected function _isRegistered($path) {
		return isset(self::$_registerePaths[$path]);
	}

	static protected function _unset($path) {
		if(self::_isRegistered($path))
			self::$_registerePaths[$path] = null;
	}

	static public function register($path, $prefix = null, $removePrefix = true) {
		if(!$path)
			throw new Exception('path cannot be null.');
		if(self::_isRegistered($path))
			throw new Exception("path[$path] already registered.");
		$loader = new RO_Loader($path, $prefix);
		$loader->removePrefix = $removePrefix;
		spl_autoload_register(array($loader, 'autoload'));
		self::_set($path, $loader);
		return TRUE;
	}

	static public function unregister($path) {
		if(!self::_isRegistered($path)) return FALSE;
		spl_autoload_unregister(self::$_registerePaths[$path]);
		self::_unset($path);
		return TRUE;
	}
	
	static public function useCache($cacheDir = null, $expiredTime = 0, $cacheId = null){
		self::$_useCache = $cacheDir;
		self::$_expiredTime = intval($expiredTime);
		self::$_cacheId = $cacheId;
	}
}