<?php

/**
 * Confirms a record does not exist in a table.
 *
 */
class FinalView_Validate_Db_NoRecordExists extends FinalView_Validate_Db_Abstract
{
    
    public function isValid($value)
    {
        $valid = true; 
        $this->_setValue($value); 
        
        $count = $this->_getRecordsCount($value);  
        
        if ($count > 0) { 
            $valid = false; 
            $this->_error(self::ERROR_RECORD_FOUND); 
        } 
         
        return $valid; 
    }
    
}
