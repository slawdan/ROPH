<?php 

/**
 * @package RO_Log
 * @subpackage RO_Log_Formatter
 * @author Rodin Shih <schludern@gmail.com>
 */
class RO_Log_Formatter{
	
	public $format = '(%time) (%name) (%levelname):(%message)';
	
	public function __construct($format = null) {
		if(!is_null($format))
			$this->format = $format;
	}
	
	public function format(array $record){
		$values = $record['extras'];
		isset($values['date']) OR $values['date'] = date('Y-m-d');
		isset($values['time']) OR $values['time'] = date('H:i:s');
		$values['levelname'] = $record['levelname'];
		$values['level'] = $record['level'];
		$values['name'] = $record['name'];
		
		$format = $this->format;
		$str = preg_replace('/\(%message\)/', $record['message'], $format);
		//WARNING: maybe got unexpected result for replace the things brutely
		foreach ($values as $n => $v){
			$str = preg_replace('/\(%' . addslashes($n) . '\)/', $v, $str);
		}
		
		return $str;
	}
}