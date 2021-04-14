<?php
class ModMetaMwMainSearchHelper
{
    /**
     * @param   array  $params An object containing the module parameters
     */    
    public static function getTest($params) {
        $str = '<pre>';
        $str .= "params: \n";
        foreach($params as $key => $val) {
          $str .= "[".$key."] => ";
          $str .= $val;
          $str .= "\n";
        }
        $str .= '</pre>';
        return $str;
    }
}
?>
