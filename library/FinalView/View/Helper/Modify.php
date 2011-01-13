<?php
/**
*/
class FinalView_View_Helper_Modify extends Zend_View_Helper_Abstract
{

    public function modify($value, $modifiers="") 
    {
        if ( $modifiers ) {
            $value = $this->_single($value, $modifiers);
        }
        return $value;
    }

    private function _single($value, $modifiers="")
    {
        foreach (explode('|',$modifiers) as $mod) {
            $args = explode(':', $mod);
            $m = array_shift($args);          

            switch (true) {
                case $m == 'escape': 
                    $value = $this->view->escape($value);
                    break;
                case (array_key_exists($m, $this->view->getFilters())  || (bool)$this->view->getPluginLoader('filter')->load($m, false)): 
                    $value = call_user_func_array(
                        array($this->view->getFilter($m), 'filter'),
                        array($value)
                    );
                    break;
                case function_exists($m):
                    $value = call_user_func_array($m, array_merge(array($value), $args));
                    break;
                default:
                    break;
            }
        }
        return $value;
    }
}
