<?php

/**
 * @package RO_Log
 * @subpackage RO_Log_Handler
 * @author Rodin Shih <schludern@gmail.com>
 */
abstract class RO_Log_Handler__Abstract{
	/**
	 * 
	 * @var RO_Log_Formatter
	 */
	public $formatter = null;
	
	public function setFormat($format){
		$this->formatter = !($format instanceof RO_Log_Formatter) ? (new RO_Log_Formatter($format)): $format;
	}
	
	protected function _format(array $record){
		if(!$this->formatter){
			$this->formatter = new RO_Log_Formatter();
		}
		return $this->formatter->format($record);
	}
	
	abstract public function handle(array $record); 
}

/**
 * @package RO_Log
 * @subpackage RO_Log_Handler
 * @author Rodin Shih <schludern@gmail.com>
 */
class RO_Log_Handler__Console extends RO_Log_Handler__Abstract{
	public function handle(array $record){
		echo $this->_format($record) . "\n";
	}
}

/**
 * @package RO_Log
 * @subpackage RO_Log_Handler
 * @author Rodin Shih <schludern@gmail.com>
 */
class RO_Log_Handler__File extends RO_Log_Handler__Abstract{
	protected $_fileName = '';
	public function __construct($fileName = 'app.log'){
		if($fileName){
			$this->_fileName = $fileName;
		}else {
			$this->_fileName = __FILE__ . '.log';
		}
	}
	
	public function handle(array $record){
		file_put_contents($this->_fileName, $this->_format($record) . "\n", FILE_APPEND);
	}
}

/**
 * @package RO_Log
 * @subpackage RO_Log_Handler
 * @author Rodin Shih <schludern@gmail.com>
 */
class RO_Log_Handler__RotateFile extends RO_Log_Handler__File{
	protected $_fileSizeLimit = 1048576;//1024 * 1024;
	protected $_cleared = false;
	
	public function __construct($filename, $fileSizeLimit = '1M'){
		parent::__construct($filename);
		if($fileSizeLimit){
			$unit = strtolower(substr($fileSizeLimit, -1, 1));
			$num = floatval($fileSizeLimit);
			switch ($unit){
				case 'g':
					$num *= 1024;
				case 'm':
					$num *= 1024;
				case 'k':
					$num *= 1024;
				case 'b':
				default:
					$this->_fileSizeLimit = $num;
			}
		}
	}
	
	public function handle(array $record){
		if(!(idate('s') % 10)){/*trigger ratio : 0.1*/
			if($this->_cleared){
				clearstatcache(true, $this->_fileName);
				$this->_cleared = true;
				if($this->_fileSizeLimit > 0 && (filesize($this->_fileName) >= $this->_fileSizeLimit)){
					rename($this->_fileName, $this->_fileName . "_" . date('Ymd_His') . "_RAND" . (mt_rand(0, 999) / 1000));
				}
			}
		}else{
			$this->_cleared = false;
		}
		parent::handle($record);
	}
}
