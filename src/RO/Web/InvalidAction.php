<?php
/**
 * 
 * @copyright   Copyright (C) 2008 Rodin Shih
 * @author      Rodin Shih <schludern@gmail.com>
 * @license     http://www.opensource.org/licenses/bsd-license.php
 * @package     ROPH
 */
 
class RO_Web_InvalidAction extends RO_Flow_Work{

	protected $_targets = array('NEXT');
	
	protected function _run() {
		$this->_data['_VIEW_'] = 'error';
		$this->_data['_VIEW_DATA_'] = array(
			'path' => $this->_data['_PATH_'],
		);
		$this->_data['_ERROR_'] = new Exception('Action [' . $this->_data['_EXEC_'] . '] is an invalid action');
		$this->_setNext('NEXT');
	}
}
