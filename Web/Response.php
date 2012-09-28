<?php
/**
 * 
 * @copyright   Copyright (C) 2008 Rodin Shih
 * @author      Rodin Shih <schludern@gmail.com>
 * @license     http://www.opensource.org/licenses/bsd-license.php
 * @package     ROPH
 */
 
abstract class RO_Web_Response extends Exception{
	public function __construct($data)
	{
		parent::__construct($data);
	}

	static public function make($type, $data) {
		if(is_subclass_of($type, 'RO_Web_Response')){
			$resp = new $type(serialize($data));
			return $resp;
//			throw $resp;
		}
		throw new Exception('Not a valid subtype of RO_Web_Response');
	}

	public function getData() {
		return unserialize($this->message);
	}

	abstract public function process();

	static public $HTTP_301 = "RO_Web_Response_Moved";
	static public $MOVED = "RO_Web_Response_Moved";
	static public $HTTP_302 = "RO_Web_Response_Redirect";
	static public $REDIRECT = "RO_Web_Response_Redirect";
}