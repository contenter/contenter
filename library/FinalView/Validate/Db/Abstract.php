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
    protected $_field = '';    
       
    /**
     * @var mixed
     */   
    protected $_exclude = null;   
   
    /**
     * Setting $exclude allows a single record to be excluded from matching.
     * Exclude can either be a String containing a where clause, or an array with `field` and `value` keys
     * to define the where clause added to the sql.  
     * A database adapter may optionally be supplied to avoid using the registered default adapter. 
     * 
     * @param string $model The database model to validate against
     * @param string $field The field to check for a match
     * @param array $exclude An optional where clause or field/value pair to exclude from the query
     */   
    public function __construct($model, $field, array $exclude = array())    
    {    
        $this->_exclude = $exclude;   
        $this->_model   = (string) $model;    
        $this->_field   = (string) $field;   
    }
    
    /**
    * Set exclude values
    * 
    * @param array $exclude
    */
    public function setExclude(array $exclude) 
    {
        $this->_exclude = $exclude;
        return $this;
    }
     
    /**
     * Run query.
     *
     * @param  String $value
     * @return boolean
     */ 
    protected function _query($value) 
    {
        $query = Doctrine_Query::create()
             ->select('COUNT(*)')
             ->from($this->_model)
             ->where($this->_field . ' = ?', $value)
             ->limit(1)
             ;
        if (is_array($this->_exclude)) {
            foreach ($this->_exclude as $field => $value) {
                $query->andWhere($field . ' != ?', $value);
            }
        }
        
        return $query->execute(array(), Doctrine::HYDRATE_SINGLE_SCALAR) > 0;
    } 
	
}
