<?php
class FinalView_Doctrine_Cli extends Doctrine_Cli
{

    protected function includeAndRegisterTaskClasses()
    {       
        parent::includeAndRegisterTaskClasses();
        
        $this->includeAndRegisterDoctrineTaskClasses(
            LIBRARY_PATH . DIRECTORY_SEPARATOR . 'FinalView' . DIRECTORY_SEPARATOR . 'Doctrine' . DIRECTORY_SEPARATOR . 'Task'        
        );        
    }  
}
