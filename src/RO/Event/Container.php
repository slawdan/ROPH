<?php 

class RO_Event_Container{
	protected $_events = array();
	
	public function load(){
		throw new Exception('Not implemented');
	}
	
	public function dump(){
		throw new Exception('Not implemented');
	}
	
	public function register(RO_Event_IDispatchable $event, $overrideIfRegistered = TRUE){
		if(!$overrideIfRegistered){
			if($this->isRegistered($event)){
				throw new Exception('event already registered');
			}
		}
		$this->_events[$event->name()] = $event; 
	}
	
	public function unregister(RO_Event_IDispatchable $event){
		if(!$this->isRegistered($event)){
			return;
		}
		unset($this->_events[$event->name()]);
	}
	
	public function unregisterByName($eventName){
		if(!$this->isRegisteredByName($eventName)){
			return ;
		}
		unset($this->_events[$eventName]);
	}
	
	public function isRegistered(RO_Event_IDispatchable $event) {
		return isset($this->_events[$event->name()]) && $this->_events[$event->name()];
	}
	
	public function isRegisteredByName($eventName) {
		return isset($this->_events[$eventName]) && $this->_events[$eventName];
	}
	
	public function getEvent(RO_Event_IDispatchable $event){
		return $this->isRegistered($event) ? $this->_events[$event->name()] : false;
	}
	
	public function getEventByName($eventName){
		return $this->isRegisteredByName($eventName) ? $this->_events[$eventName] : false;
	}
}