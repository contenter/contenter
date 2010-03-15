<?php
class FinalView_Doctrine_Cli extends Doctrine_Cli
{


    public function loadTasks($directories = array())
    {                
        if (empty($directories)) {        	
            $directories = array( 
                null,
                LIBRARY_PATH . DIRECTORY_SEPARATOR . 'FinalView' . DIRECTORY_SEPARATOR . 'Doctrine' . DIRECTORY_SEPARATOR . 'Task',                
            );
        }
        
        foreach ($directories as $dir) {
        	parent::loadTasks($dir);
        }
    }    
}