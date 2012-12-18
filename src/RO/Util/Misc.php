<?php
class RO_Util_Misc{
	public static $urlBase = '';
	static public function url(){
		$args = func_get_args();
		$split = array_search('=', $args, true);
		$keys = array_slice($args, 0, $split, 1);
		$values = array_slice($args, $split + 1, $split, 1);
		count($keys) > count($values) AND $values = array_pad($values, $split, '');
		$kv = array_combine($keys, $values);
		//http_build_query result will be affected by gpc_quote settings.
		//if anything unexpected happened, check the gpc_quotes first.
		return self::$urlBase . "?" . http_build_query($kv);
	}
	
	static public function formVar(){
		$args = func_get_args();
		$split = array_search('=', $args, true);
		$keys = array_slice($args, 0, $split, 1);
		$values = array_slice($args, $split + 1, $split, 1);
		count($keys) > count($values) AND $values = array_pad($values, $split, '');
		$kv = array_combine($keys, $values);
		$str = '';
		foreach ($kv as $k => $v){
			$str .= '<input type="hidden" name="' . htmlspecialchars($k, ENT_QUOTES) . '" value="' . htmlspecialchars($v, ENT_QUOTES) . '"/>';
		} 
		return $str;
	}
	
	static public function pager($pageSize, $itemsCount, $cur){
		$items = abs(intval($itemsCount));
		$size = abs(intval($pageSize));
		$size OR $size = 20;
		$pages = ceil($items / $size);
		$cur = max(1, min(intval($cur), $pages));
		$prev = max(1, min($cur - 1, $pages));
		$next = max(1, min($cur + 1, $pages));
		$start = $cur ? ($cur - 1) * $size : 0;
		
		$list = array();
		$list[] = floor(($cur) / 2);
		$list[] = $cur;
		$list[] = ceil(($pages + $cur) / 2);
//		sort($list = array_unique($list));
		
		return compact('items', 'size', 'cur', 'pages', 'next', 'prev', 'list', 'start');
	}
}