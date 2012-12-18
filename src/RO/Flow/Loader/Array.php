<?php
/**
 * 
 * @copyright   Copyright (C) 2008 Rodin Shih
 * @author      Rodin Shih <schludern@gmail.com>
 * @license     http://www.opensource.org/licenses/bsd-license.php
 * @package     ROPH
 */
 
class RO_Flow_Loader_Array extends RO_Flow_Loader {

	/**
	 * @see RO_Flow_Loader::load()
	 *
	 */
	protected function _load()
	{
		if(!is_array($this->_source))
			throw new Exception('source data must be an array');
		if(!isset($this->_source['works']) || !is_array($this->_source['works']) && $this->_source['works']){
			throw new Exception('works must defined in source data defined properly');
		}else{
			$works = $this->_source['works'];
			foreach ($works as $name => $work){
				if(!is_string($name))
					throw new Exception("work $name must be a string");
				if(!isset($work['type']))
					throw new Exception("work $name must define type");
				if(!isset($work['targets']))
					throw new Exception("work $name must define targets");
				$work['params'] = isset($work['params']) ? $work['params'] : array();
				$works[$name] =$work;
			}
		}
		if(!isset($this->_source['start']) || !is_string($this->_source['start'])){
			throw new Exception('start work must defined properly');
		}else{
			$start = $this->_source['start'];
		}
		$data = isset($this->_source['data']) ? $this->_source['data'] : array();

		$this->_workData['works'] = $works;
		$this->_workData['start'] = $start;
		$this->_workData['data']  = $data;
	}
}