<?php
 
/**
 * Confirms a record exists in a table.
 * 
 */  
class FinalView_Validate_Db_RecordExists extends FinalView_Validate_Db_Abstract  
{ 
    
    public function isValid($value) 
    {         
        $valid = true; 
        $this->_setValue($value); 
        
        $count = $this->_getRecordsCount($value);  
        
        if ($count < 1) { 
            $valid = false; 
            $this->_error(self::ERROR_NO_RECORD_FOUND); 
        } 
         
        return $valid; 
    }
    
}
