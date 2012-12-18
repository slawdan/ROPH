<?php 

class RO_Event_Dispatcher_Stop extends Exception{
}

class RO_Event_Dispatcher{
	const LEVEL_MAXIMUM = 0xFFFF;
	const LEVEL_MINIMUM = 0x0000;
	const LEVEL_HIGHEST = 0x0000;
	const LEVEL_HIGHER = 0x3FFF;
	const LEVEL_NORMAL = 0x7FFF;
	const LEVEL_LOWER = 0xBFFF;
	const LEVEL_LOWEST = 0xFFFF;
	
	/**
	 * @var RO_Event_PriorityQueue
	 */
	protected $_queue = null;
	
	/**
	 * @var RO_Event_Container
	 */
	protected $_container;
	
	protected $_currentEvent;
	
	protected $_data;
	
	public function __construct(){
		$this->_queue = new RO_Event_PriorityQueue(self::LEVEL_MINIMUM, self::LEVEL_MAXIMUM);
		$this->_data = new ArrayObject();
	}
	
	public function start(){
		try{
			$this->_currentEvent = $this->_queue->shift2();
			while($this->_currentEvent){
				if(!$this->_currentEvent){
					return ;
				}
				
				if(is_string($this->_currentEvent[0])){
					$event = $this->_container->getEventByName($this->_currentEvent[0]);
				}else{
					$event = $this->_container->getEvent($this->_currentEvent[0]);
				}
				/* @var $event RO_Event_IDispatchable */
				if($event){
					$event->handle($this->_data);
				}
				
				$this->_currentEvent = $this->_queue->shift2();
			}
		}catch (RO_Event_Dispatcher_Stop $ex){
		}
	}
	
	/**
	 * 
	 * @param ArrayObject $data
	 * @return RO_Event_Dispatcher
	 */
	public function data(ArrayObject $data = NULL){
		if(is_null($data)){
			return $this->_data;
		}else {
			$this->_data = $data;
			return $this;
		}
	}
	
	/**
	 * 
	 * @param RO_Event_Container $container
	 * @return RO_Event_Dispatcher|RO_Event_Container
	 */
	public function container(RO_Event_Container $container = NULL){
		if(is_null($container)){
			return $this->_container;
		}else{
			$this->_container = $container;
			return $this;
		}
	}
	
	/**
	 * 
	 * @param unknown_type $event
	 * @param unknown_type $level
	 * @return RO_Event_Dispatcher
	 */
	public function trigger($event, $level = null){
		if(is_null($level)){
			$level = self::LEVEL_NORMAL;
		}
		$this->_queue->queue($event, $level);
		return $this;
	}
	
	public function currentEvent(){
		if(isset($this->_currentEvent)){
			return $this->_currentEvent[0];	
		}
		return false;
	}
	
	public function currentLevel(){
		if(isset($this->_currentEvent)){
			return $this->_currentEvent[1];	
		}
		return false;
	}
}