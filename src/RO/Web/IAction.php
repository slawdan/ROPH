<?php
/**
 * 
 * @copyright   Copyright (C) 2008 Rodin Shih
 * @author      Rodin Shih <schludern@gmail.com>
 * @license     http://www.opensource.org/licenses/bsd-license.php
 * @package     ROPH
 */
 
interface RO_Web_IAction{
	/**
	 * the main business action which will be invoked by RO_Web_Exec
	 *
	 * @param  array $runtimeData 
	 * @return array
	 */
	public function run($runtimeData);
}