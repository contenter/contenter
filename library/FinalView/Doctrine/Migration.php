<?php
class FinalView_Doctrine_Migration extends Doctrine_Migration
{
    public function __construct($directory = null)
    {
        parent::__construct($directory);
        
        $this->_process = new FinalView_Doctrine_Migration_Process($this);
    }
}