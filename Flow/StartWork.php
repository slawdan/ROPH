<?php
/**
 * 
 * @copyright   Copyright (C) 2008 Rodin Shih
 * @author      Rodin Shih <schludern@gmail.com>
 * @license     http://www.opensource.org/licenses/bsd-license.php
 * @package     ROPH
 */
 
final class RO_Flow_StartWork extends RO_Flow_Work {
	protected $_targets = array('START');
    protected function _run(){
       if($this->_params && isset($this->_params['verbose'])){
           parent::_run();
       }
    }
}
