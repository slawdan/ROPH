<?php

class RO_Event_PriorityQueue{
	protected $_queueData = array();
	
	protected $_queuePriorityMin;
	protected $_queuePriorityMax;
	
	protected $_middlePriority;
	
	public function __construct($priorityMin = 0, $priorityMax = 0xFFFF){
		$this->_queuePriorityMin = $priorityMin;
		$this->_queuePriorityMax = $priorityMax;
		$this->_middlePriority = intval(($this->_queuePriorityMax + $this->_queuePriorityMin) / 2);
	}
	
	public function shift(){
		$s = $this->shift2();
		return $s[0];
	}
	
	public function shift2(){
		 $queuePriorities = array_keys($this->_queueData);
		 sort($queuePriorities);
		 for($i = 0, $iMax = count($queuePriorities); $i < $iMax; $i ++){
		 	 $d = $this->_queueOut($queuePriorities[$i]);
		 	 if($d !== false){
		 	 	return array($d, $queuePriorities[$i]);
		 	 }
		 }
	}
	
	public function shiftPriority($priority){
		return $this->_queueOut($priority);
	}
	
	protected function _initQueue($priority) {
		if(!isset($this->_queueData[$priority]) || !is_array($this->_queueData[$priority])){
			if($this->_queuePriorityMin <= $priority && $priority <= $this->_queuePriorityMax)
				$this->_queueData[$priority] = array();
			else 
				throw new Exception('Priority not in normal range');
		}
	}
	
	protected function _queueOut($priority){
		if(isset($this->_queueData[$priority])
			&& is_array($this->_queueData[$priority])
			&& $this->_queueData[$priority]
		){
			$data = array_shift($this->_queueData[$priority]);
			return $data;
		}else{
			return false;
		}
	}
	
	protected function _queueIn($data, $priority) {
		$this->_initQueue($priority);
		$this->_queueData[$priority][] = $data;
	}
	
	public function queue($data, $priority = NULL){
		$this->_queueIn($data, is_null($priority) ? $this->_middlePriority : $priority);
	}
}