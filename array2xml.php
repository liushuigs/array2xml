<?php
function tab($len){
    $str = '';
    for($i = 0;$i<$len;$i++){
        $str .= "\t";
    }
    return $str;
}
function tabitem($key,$value,$tab){
    return tab($tab)."<$key><![CDATA[$value]]></$key>\n";
}
function object_to_array($object){
    $_array = is_object($object) ? get_object_vars($object) : $object;
    $array = array();
    foreach($_array as $key=>$value){
        $value = (is_array($value) || is_object($value)) ? 
            object_to_array($value) : $value;
        $array[$key] = $value;
    }
    return $array;
}
function xml_to_array($filename){
    $arr = simplexml_load_file($filename);
    $arr = object_to_array($arr);
    return $arr;
}
function array_to_xml(&$array,&$xml,$s_tab=0,$root=''){
    $next_tab = $s_tab+1;
    if(!is_array($array)){
        return ;
    }
    if(isset($array[0])){
        foreach($array as $key=>&$item){
            if(!is_numeric($key)){
                continue;
            }elseif(!is_array($item)){
                continue;
            }else{
                if($root){
                    $xml .= tab($s_tab)."<$root>\n";
                    array_to_xml($item,$xml,$next_tab);
                    $xml .= tab($s_tab)."</$root>\n";
                }
            }
        }
    }else{
        $root != '' && $xml .= tab($s_tab)."<$root>\n";
        foreach($array as $key=>&$item){
            if($key === '@attributes' || $key === 'comment'){
                continue;
            }
            if(is_numeric($key)){
                continue;
            }
            if(!is_array($item)){
                $xml .= tabitem($key,$item,$s_tab);
            }else{
                if(isset($item[0])){
                    array_to_xml($item,$xml,$s_tab,$key);
                }else{
                    $xml .= tab($s_tab)."<$key>\n";
                    array_to_xml($item,$xml,$next_tab);
                    $xml .= tab($s_tab)."</$key>\n";
                }
            }
        }
        $root != '' && $xml .= tab($s_tab)."</$root>\n";
    }
}
function get_attr(&$array,$key=''){
    return $key ? (isset($array['@attributes'][$key]) ? $array['@attributes'][$key] : '' ): $array['@attributes'];
}

$xml = '<?xml version="1.0" encoding="utf-8"?>'."\n";
$xml .= "<DOCUMENT>\n";
array_to_xml($result,$xml);
$xml .= "</DOCUMENT>";
