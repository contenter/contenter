<?php  

/**
 * Class for Database record validation
 * 
 */  
abstract class FinalView_Validate_Db_Abstract extends Zend_Validate_Abstract    
{   
    /**
     * Error constants
     */ 
    const ERROR_NO_RECORD_FOUND = 'noRecordFound';  
    const ERROR_RECORD_FOUND    = 'recordFound';  
   
    /**
     * @var array Message templates
     */    
    protected $_messageTemplates = array
    (
        self::ERROR_NO_RECORD_FOUND => 'No record matching %value% was found', 
        self::ERROR_RECORD_FOUND    => 'A record matching %value% was found'
    );     
 
    /**
     * @var string
     */    
    protected $_model = '';    
    
    /**
     * @var string
     */    
    protected $_selector = array(); 
     
    public function __construct($model, $selector)    
    {
        $this->_model   = (string) $model;    
        $this->_selector   = (string) $selector;   
    }
     
    
    protected function _getRecordsCount($value)
    {
        return Doctrine::getTable($this->_model)->countByParams(array(
            $this->_selector => $value
        ));
    }	
}
