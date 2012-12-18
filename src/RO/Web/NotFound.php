<?php
/**
 * 
 * @copyright   Copyright (C) 2008 Rodin Shih
 * @author      Rodin Shih <schludern@gmail.com>
 * @license     http://www.opensource.org/licenses/bsd-license.php
 * @package     ROPH
 */
 
class RO_Web_NotFound extends RO_Flow_Work{

	protected $_targets = array('NEXT');
	
	protected function _run() {
		$this->_data['_VIEW_'] = '404';
		$this->_data['_VIEW_DATA_'] = array(
			'path' => $_SERVER['REQUEST_URI'],
		);
		$this->_setNext('NEXT');
	}
}
