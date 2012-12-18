<?php
/**
 *
 * @copyright   Copyright (C) 2008 Rodin Shih
 * @author      Rodin Shih <schludern@gmail.com>
 * @license     http://www.opensource.org/licenses/bsd-license.php
 * @package     ROPH
 */
 
class RO_Web_Exec extends RO_Flow_Work {

	protected $_targets = array('NEXT', 'ERROR', 'RESPONSE', 'INVALID_ACTION');

	/**
	 * @see RO_Flow_Work::_run()
	 *
	 * @return RO_Flow_Work
	 */
	protected function _run(){
		$exec = $this->_data['_EXEC_'];
		if(!(class_exists($exec, TRUE) && in_array('RO_Web_IAction', class_implements($exec)))){
			$this->_setNext('INVALID_ACTION');
			return ;
		}
		
		try{
			$exec = new $this->_data['_EXEC_']();
			$this->_data['_VIEW_'] = $this->_data['_EXEC_'];
			$this->_data['_VIEW_DATA_'] = $exec->run($this->_data);
			$this->_setNext('NEXT');
		}catch (RO_Web_Response $ex){
			$ex->process();
			$this->_setNext('RESPONSE');
		}catch (Exception $ex){
			$this->_data['_ERROR_'] = $ex;
			$this->_setNext('ERROR');
		}
	}
}
