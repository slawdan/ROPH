<?php 

class RO_Event_DispatchableHandler extends RO_Event_Handler implements RO_Event_IDispatchable{
	protected $_name;
	
	public function __construct($name = null){
		$this->name($name);
	}
	
	/**
	 * @param RO_Event_Handler $handler
	 * @return RO_Event_DispatchableHandler
	 */
	public function handler(RO_Event_Handler $handler = NULL) {
		return $this;
	}

	/**
	 * @param unknown_type $name
	 */
	public function name($name = NULL) {
		if(is_null($name)){
			return $this->_name;
		}else{
			$this->_name = $name;
			return $this;
		}
	}
}