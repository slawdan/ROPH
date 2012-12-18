<?php
/**
 * 
 * @copyright   Copyright (C) 2008 Rodin Shih
 * @author      Rodin Shih <schludern@gmail.com>
 * @license     http://www.opensource.org/licenses/bsd-license.php
 * @package     ROPH
 */
 
class RO_Web_Error extends RO_Flow_Work{

	protected $_targets = array('NEXT');

	protected function _run() {
		$ex = $this->_data['_ERROR_'];
		/*@var $ex Exception*/
		$this->_data['_VIEW_'] = 'Error';
		$this->_data['_VIEW_DATA_'] = array(
			'URL' => $_SERVER['REQUEST_URI'],
			'CODE' => $ex->getCode(),
			'FILE' => $ex->getFile(),
			'LINE' => $ex->getLine(),
			'MESSAGE' => $ex->getMessage(),
			'CALLSTACK' => $ex->getTraceAsString(),
		);
		$this->_setNext('NEXT');
	}
}
