<?php
/**
 * 
 * @copyright   Copyright (C) 2008 Rodin Shih
 * @author      Rodin Shih <schludern@gmail.com>
 * @license     http://www.opensource.org/licenses/bsd-license.php
 * @package     ROPH
 */
 
class RO_Web_Response_Redirect extends RO_Web_Response {
	/**
	 * @see RO_Web_Response::process()
	 *
	 */
	public function process()
	{
		header('Location: '. $this->getData());
	}
}