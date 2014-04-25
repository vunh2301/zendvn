<?php
class Zendvn_Config_Writer_Ini extends Zend_Config_Writer_Ini
{
    protected function _prepareValue($value)
    {
        if (is_integer($value) || is_float($value)) {
            return $value;
        } elseif (is_bool($value)) {
            return ($value ? 'true' : 'false');
        } elseif (strpos($value, APPLICATION_PATH) !== false) {
            return 'APPLICATION_PATH "' . str_replace(APPLICATION_PATH, '', $value) .  '"';
        } elseif (strpos($value, '"') === false) {
            return '"' . $value .  '"';
        } else {
            throw new Exception('Value can not contain double quotes "');
        }
    }

}
