<?php 

interface RO_Event_IDispatchable{
	public function handler(RO_Event_Handler $handler = NULL);
	
	public function name($name = NULL);
	
	public function handle(ArrayObject $data);
}