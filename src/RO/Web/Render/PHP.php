<?php
/**
 * 
 * @copyright   Copyright (C) 2008 Rodin Shih
 * @author      Rodin Shih <schludern@gmail.com>
 * @license     http://www.opensource.org/licenses/bsd-license.php
 * @package     ROPH
 */
 
class RO_Web_Render_PHP extends RO_Flow_Work {

	protected $_targets = array('NEXT');

	/**
	 * @see RO_Flow_Work::_run()
	 *
	 * @return RO_Flow_Work
	 */
	protected function _run(){
		$data = isset($this->_data['_VIEW_DATA_']) ? $this->_data['_VIEW_DATA_'] : '';
		ob_start();
//		var_dump($this->_data['_VIEW_DATA_']);
		include $this->_params['path'] . DIRECTORY_SEPARATOR . $this->_data['_VIEW_'];
		ob_end_flush();
		$this->_setNext('NEXT');
	}

}
