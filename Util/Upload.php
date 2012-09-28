<?php

class RO_Util_Upload{
	protected $_files = array();
	protected $_curFile = null;
	protected $_curFileKey = null;
	protected $_newFilename = '';
	/**
	 * -i -J -l -N -v available
	 * 
	 * @var unknown_type
	 */
	protected $_nameingPattern = '%Y-%m-%d';
	
	/**
	 * 
	 * @param array $files [optional] It's stucture MUST be similar with the $_FILE, else will cause unexpected problems. 
	 */
	public function __construct(array $files = NULL) {
		if(is_null($files)){
			$files = $_FILES;
		}
		$this->_files = $files;
	}

	protected function _init($key) {
		if(!isset($this->_files[$key])){
			throw new RO_Util_Upload_FileKeyNotExistError($key);
		}
		$this->_curFileKey = $key;
		$this->_curFile = $this->_files[$key];
		$this->_newFilename = '';
	}
	
	/**
	 * 
	 * @param unknown_type $pattern
	 * @return RO_Util_Upload|string
	 */
	public function nameingPattern($pattern = NULL) {
		if(is_null($pattern)){
			return $this->_nameingPattern;
		}
		$this->_nameingPattern = strval($pattern);
		return $this;
	}
	
	public function fileSaveName(){
		if(!$this->_newFilename){
			$this->_newFilename = $this->_genNewFilename();
		}
		return $this->_newFilename;
	}
	
	/**
	 *f原名
	 *t扩展名
	 *n随机数
	 *E reserved
	 **/
	protected function _genNewFilename(){
		if(!$this->_nameingPattern)
			return '';
		
		$pat = $this->_nameingPattern;
		$meta = pathinfo($this->_curFile['name']);
		$filename = $this->_curFile['basename'];
		$ext = $meta['extension'];
		$name = $meta['filename'];
		
		preg_match_all('/(?<!%)%(f|t|n+|E)/', $this->_nameingPattern, $matches, PREG_PATTERN_ORDER);
		$tokens = array_unique($matches[1]);
		$values = array();
		foreach ($tokens as $token){
			switch ($token){
				case 'f':
					$pat = preg_replace('/(?<!%)%f/', $name, $pat);
					break;
				case 't':
					$pat = preg_replace('/(?<!%)%t/', $ext, $pat);
					break;
				case 'E':
					//reserved flag
					//$filename = preg_replace('/(?<!%)%E/', $name, $filename);
					break;
				default: // process continous indefinetely "n" 
					$len = strlen($token);
					$len > 10 AND $len = 10;
					$rand = str_pad(mt_rand(0, pow(10, $len)), $len, '0', STR_PAD_LEFT);
					$pat = preg_replace('/(?<!%)%' . $token . '/', $rand, $pat);
			}
		}
		return strftime($pat);
	}
	
	public function allFileKeys(){
		return array_keys($this->_files);
	}
	
	public function fileMeta(){
		$meta = $this->_curFile;
		$meta['pathinfo'] = pathinfo($this->_curFile['name']);
		return $meta;
	}
	
	public function fileKey(){
		return $this->_curFileKey;
	}
	
	/**
	 * 
	 * @param string $key
	 * @return RO_Util_Upload
	 */
	public function file($key){
		$this->_init($key);
		return $this;
	}
	
	/**
	 * 
	 * @return RO_Util_Upload
	 */
	public function save($path){
		$meta = $this->_curFile;
		if(!is_dir($path) && !is_writable($path))
			throw new Exception('Save path incorrect');
		if(!@move_uploaded_file($meta['tmp_name'], $path . DIRECTORY_SEPARATOR . $this->fileSaveName()))
			throw new Exception('Move tmp file failed');
		return $this;
	}
}

class RO_Util_Upload_FileKeyNotExistError extends Exception{
	public function __construct($key){
		parent::__construct("File key \"{$key}\" not exist in uploaded files.");
	}
}