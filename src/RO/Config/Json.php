<?php
/**
 *
 * @copyright   Copyright (C) 2008 Rodin Shih
 * @author      Rodin Shih <schludern@gmail.com>
 * @license     http://www.opensource.org/licenses/bsd-license.php
 * @package     ROPH
 */

class RO_Config_Json implements ArrayAccess {

	protected $_configs = array();

	protected $_scheme = FALSE;

	protected $_rawContent = '';

	/**
	 * @see ArrayAccess::offsetExists()
	 *
	 * @param offset $offset
	 */
	public function offsetExists($offset)
	{
		return array_key_exists($offset, $this->_configs);
	}

	/**
	 * @see ArrayAccess::offsetGet()
	 *
	 * @param offset $offset
	 */
	public function offsetGet($offset)
	{
		return isset($this->_configs[$offset]) ? $this->_configs[$offset] : NULL;
	}

	/**
	 * @see ArrayAccess::offsetSet()
	 *
	 * @param offset $offset
	 * @param value $value
	 */
	public function offsetSet($offset, $value)
	{
		$this->_configs[$offset] = $value;
	}

	/**
	 * @see ArrayAccess::offsetUnset()
	 *
	 * @param offset $offset
	 */
	public function offsetUnset($offset)
	{
		unset($this->_configs[$offset]);
	}

	/**
	 * get the configs as an assoc array.
	 *
	 * @return array
	 */
	public function toArray()
	{
	    return $this->_configs;
	}

	/**
	 * constructor
	 *
	 * @param string   $configFile [OPTIONAL] Config path.If omitted, must explict call load() to load configs.
	 * @param callback $scheme     [OPTIONAL] Config scheme check function.If omitted, can use setScheme to set scheme.
	 */
	public function __construct($scheme = FALSE)
	{
		if(FALSE !== $scheme){
			$this->setScheme($scheme);
		}
	}

	/**
	 * set scheme validate callback function
	 *
	 * @param  callback         $schemeCallback
	 * @return RO_Config_Json                    current instance's reference.
	 */
	public function setScheme($schemeCallback)
	{
		$this->_scheme = $schemeCallback;
		return $this;
	}

	/**
	 * Validate the configs using the specified scheme check function.
	 *
	 * @return void
	 */
	public function check()
	{
		if($this->_scheme){
			return call_user_func($this->_scheme, $this->_configs);
		}else{
			return TRUE;
		}
	}

	/**
	 * Load config from a file
	 *
	 * @param  string $configFile
	 * @return RO_Config_Json
	 */
	public function load($configFile = '')
	{
		$content = $this->_readFile($configFile);
		$this->loads($content);
		return $this;
	}

	/**
	 * load json content from a string
	 *
	 * @return void
	 */
	public function loads($content)
	{
		$this->_rawContent = $content;
		$this->_parse();
		if(!$this->check()){
			throw new Exception('Config not valid');
		}
		return $this;
	}

	/**
	 * Parse json contents to assoc array.
	 *
	 * @return RO_Config_Json
	 */
	protected function _parse()
	{
		if($this->_rawContent){
			$this->_rawContent = preg_replace_callback('/(?:[\x80-\xff]..)+/', array('RO_Config_Json', 'utf8ToUnicode'), $this->_rawContent);
			$config = @json_decode($this->_rawContent, TRUE);
			$this->_configs = $config ? $config : array();
		}
		return $this;
	}

	/**
	 * Convert unicode \uxxxx to utf8 charactors
	 *
	 * @param  string $string
	 * @return string
	 */
	static public function unicodeToUtf8($string)
	{
		return json_decode('"' . $string[0] . '"');
	}

	/**
	 * Convert utf8 charactors to \uxxxx format
	 *
	 * @param  string $string
	 * @return string
	 */
	static public function utf8ToUnicode($string)
	{
		$string = json_encode($string[0]);

		$count = 0;
		$string2 = preg_replace('/^"|"$/', '', $string, -1, $count);

		if($count !== 2)
			throw new Exception('convert utf8 to unicode error: the outer quote not paired');
//		$strLen = strlen($string);
//		$string = isset($string{$strLen - 1}) && $string{$strLen - 1} == '"' ?
//					substr($string, 1, $strLen - 1) :
//						(isset($string{0}) && $string{0} == '"'?
//							substr($string, 1) :
//							$string);
//		if(isset($string{$strLen - 1}) && $string{$strLen - 1} == '"'){
//			$string = substr($string, 1, $strLen - 1);
//		}
//		if(isset($string{0}) && $string{0} == '"'){
//			$string = substr($string, 1);
//		}
		return $string2;
	}

	/**
	 * read contents from the file.
	 *
	 * @param  string $filename
	 * @return string             the contents
	 */
	protected function _readFile($filename)
	{
		$content = @file_get_contents($filename);
		if(!$content){
			throw new Exception("Config file [{$filename}] empty or not readable.");
		}else{
			return $content;
		}
	}

	/**
	 * @return RO_Config_Json
	 */
	protected function _encode()
	{
		$this->_rawContent = json_encode($this->_configs);
		$this->_rawContent = preg_replace_callback('/(?:\\\\u[0-9a-fA-F]{4})+/i', array('RO_Config_Json', 'unicodeToUtf8'), $this->_rawContent);
		return $this;
	}

	/**
	 * @return RO_Config_Json
	 */
	protected function _writeFile($filename, $content)
	{
		file_put_contents($filename, $content);
		return $this;
	}

	/**
	 * dump as string
	 *
	 * @return string
	 */
	public function dumps()
	{
		if(!$this->check()){
			throw new Exception('Config not valid');
		}
		$this->_encode();
		return $this->_rawContent;
	}

	/**
	 * Save to predefined file or a new file.
	 *
	 * If set a new file path, The current save path will change to the new one.
	 *
	 * @param  string         $configFile
	 * @return RO_Config_Json
	 */
	public function dump($configFile)
	{
		if($configFile){
			$this->_writeFile($configFile, $this->dumps());
			return $this;
		}
		throw new Exception('Config file save path not specified');
	}
	
	/**
	 * Create an instance of this class
	 *
	 * @param  callback       $scheme
	 * @return RO_Config_Json
	 */
	static public function build($scheme = FALSE) {
		$instance = new self($scheme);
		return $instance;
	}
	
}