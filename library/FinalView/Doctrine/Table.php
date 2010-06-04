<?php
class FinalView_Doctrine_Table extends Doctrine_Table
{
    private $_queryParams;
    private $_query;
    
    
    final public function _getQuery()
    {
        if ($this->_query === null) {
        	$this->_query = $this->createQuery($this->getTableName());
        }
        
        return $this->_query;
    }
    
    private function _resetQuery()
    {
        $this->_query = null;
    } 
    
    final public function findByParams($params = array(), $hydrationMode = null)
    {
        $this->build($this->_getQuery(), $params);

        $result = $this->_getQuery()->execute(array(), $hydrationMode);
        
        $this->_resetQuery();
        
        return $result;
    }
    
    final public function findOneByParams($params = array(), $hydrationMode = null)
    {
        $this->build($this->_getQuery(), $params);

        $result = $this->_getQuery()->limit(1)->fetchOne(array(), $hydrationMode);
        
        $this->_resetQuery();
        
        return $result;
    }
    
    final public function findPageByParams($params = array(), $pageNum, $perPage, $hydrationMode = null)
    {
        $this->build($this->_getQuery(), $params);
        
        $query = clone($this->_getQuery());
        $pager = new Doctrine_Pager(
            $query,
            $pageNum,
            $perPage
        );
        
        $this->_resetQuery();
        
        return $pager;
    }
    
    final public function countByParams($params = array())
    {
        $this->build($this->_getQuery(), $params);
        
        $result = $this->_getQuery()->count(array());
        
        $this->_resetQuery();
        
        return $result;        
    }    
    
    public function build($query, $params)
    {
        $this->_queryParams = $params;
        $this->_query = &$query;
         
        $filter = new Zend_Filter_Word_UnderscoreToCamelCase();
        foreach ($params as $param=>$value) {
            
            $method = $filter->filter($param).'Selector';            
            if (!method_exists($this, $method)) {
            	throw new FinalView_Doctrine_Table_Exception('undefined condition in findByParams');
            }
            
            $this->$method($value);
        }        
        
        return $this->_getQuery();      
    }
    
    protected function innerJoin($relation, $params, $on = '')
    {    
        $tableObject = $this->getRelation($relation)->getTable();
        
        if (!empty($on)) {
        	$on = ' ON (' . $on . ')';
        }
        
        $this->_getQuery()->innerJoin($this->getTableName() . '.' . $relation . ' ' . $tableObject->getTableName() . $on);
      
        return $tableObject->build(&$this->_query, $params);
    }
    
    protected function LeftJoin($relation, $params, $on = '')
    {    
        $tableObject = $this->getRelation($relation)->getTable();
        
        if (!empty($on)) {
        	$on = ' ON (' . $on . ')';
        }

        $this->_getQuery()->LeftJoin($this->getTableName() . '.' . $relation . ' ' . $tableObject->getTableName() . $on );
    
        return $tableObject->build(&$this->_query, $params);
    }
    
    protected function orderBySelector($sort)
    {
        $this->_getQuery()->addOrderBy($this->getTableName().'.'.$sort['field'].' '.$sort['direction'] );                
    }
    
    protected function fieldsSelector($fields)
    {
        $this->_getQuery()->select( implode(', ', $fields) );
    }            
}
