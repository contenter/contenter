<?php
class FinalView_Access_Rules_Abstract
{
    protected $_params = array();
    
    public function __construct(array $params)
    {
        $this->_params = $params;
    }
}
