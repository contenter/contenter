<?php
class FinalView_Doctrine_Migration_Diff extends Doctrine_Migration_Diff
{
   
    protected function _diff($from, $to)
    {
        // Load the from and to models
        $baseModels = Doctrine::loadModels($from.'/generated');
        $allModels = Doctrine::loadModels($from);
        
        $customModels = array_diff($allModels, $baseModels);
        
        foreach ($customModels as $key => $modelName) {
        	if (in_array('Base'.$modelName, $baseModels)) {
                $_fromModels[$key] = $modelName;
            }
        }
        
        $fromModels = Doctrine::initializeModels($_fromModels);
        $toModels = Doctrine::initializeModels(Doctrine::loadModels($to));

        // Build schema information for the models
        $fromInfo = $this->_buildModelInformation($fromModels);
        $toInfo = $this->_buildModelInformation($toModels);

        // Build array of changes between the from and to information
        $changes = $this->_buildChanges($fromInfo, $toInfo);

        $this->_cleanup();

        return $changes;    
    }       
}