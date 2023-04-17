<?php
if(!function_exists('dtable_gen')){
    function dtable_gen($name='', $attributes=[], $set=[])
    {
    	$ci = & get_instance();
    	$csrf = $ci->config->item('dtable_csrf');
    	if ($name == '') {return FALSE;}

    	$result = "<table";
  		if (count($attributes) > 0) {
    		foreach ($attributes as $attr_key => $attr_value) {
      			$result .= " {$attr_key}=\"{$attr_value}\"";
    		}
  		}
  
  		$result .= " datatable=\"{$name}\"";
  		
  		if ($csrf) {
  			$result .= " datatable-secured=\"true\"";
  		}
  		
  		if (count($set) > 0) {
    		if (array_key_exists('processing', $set)) {
      			$result .= ' datatable-processing="'.$set['processing'].'"';
    		}

    		if (array_key_exists('serverSide', $set)) {
      			$result .= ' datatable-serverside="'.$set['serverSide'].'"';
    		}

    		if (array_key_exists('responsive', $set)) {
      			$result .= ' datatable-responsive="'.$set['responsive'].'"';
    		}

    		if (array_key_exists('serverMethod', $set)) {
      			$result .= ' datatable-servermethod="'.$set['serverMethod'].'"';
    		}

    		if (array_key_exists('ajax', $set)) {
      			$result .= dtable_json('datatable-ajax', $set['ajax']);
    		}

    		if (array_key_exists('fnRowCallback', $set)) {
      			$result .= dtable_json('datatable-fnrowcallback', $set['fnRowCallback']);
    		}

    		if (array_key_exists('columns', $set)) {
      			$result .= dtable_json(' datatable-columns', $set['columns']);
    		}

    		if (array_key_exists('lengthMenu', $set)) {
      			$result .= dtable_json(' datatable-lengthmenu', $set['lengthMenu']);
    		}

    		if (array_key_exists('columnDefs', $set)) {
      			$result .= dtable_json(' datatable-columndefs', $set['columnDefs']);
    		}

    		if (array_key_exists('order', $set)) {
      			$result .= dtable_json(' datatable-order', $set['order']);
    		}

    		if (array_key_exists('buttonsDom', $set)) {
      			$result .= ' datatable-buttons-dom="'.$set['buttonsDom'].'"';
    		}

    		if (array_key_exists('buttons', $set)) {
      			$result .= dtable_json(' datatable-buttons', $set['buttons']);
    		}
  		}
  		$result .= "></table>";
  		return $result;
    }
}

if(!function_exists('dtable_json')){
	function dtable_json($attr, $arr)
	{
		return ' '.$attr.'="'.htmlentities(json_encode($arr), ENT_QUOTES, 'UTF-8').'"';
	}
}
?>