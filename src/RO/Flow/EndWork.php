<?php
/**
 * 
 * @copyright   Copyright (C) 2008 Rodin Shih
 * @author      Rodin Shih <schludern@gmail.com>
 * @license     http://www.opensource.org/licenses/bsd-license.php
 * @package     ROPH
 */

final class RO_Flow_EndWork extends RO_Flow_Work {
	protected $_targets = array();

	/**
	 * @see RO_Flow_Work::_run()
	 *
	 */
	protected function _run(){
	    if($this->_params && isset($this->_params['verbose'])){
            parent::_run();
        }
	}

	/**
	 * @see RO_Flow_Work::run()
	 *
	 * @param ArrayObject $data
	 * @return unknown
	 */
	public function run(ArrayObject $data){
		$this->_next = '';
		$this->_run();
		return $this->_next;
	}
}