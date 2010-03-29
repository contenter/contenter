<?php 
class FinalView_Grid_ColumnsCollection implements Iterator
{
    const APPEND_AFTER_COLUMN       = 'APPEND_AFTER_COLUMN';
    const APPEND_BEFORE_COLUMN      = 'APPEND_BEFORE_COLUMN';
    const APPEND_FIRST              = 'APPEND_FIRST';
    const APPEND_LAST               = 'APPEND_LAST';
    
    private $_columns;
    private $_columnsIndex = array(
        '__begin__' =>  array(
            'next'  =>  '__end__', 'prev'   =>  null
        ),
        '__end__' =>  array(
            'next'  =>  null, 'prev'   =>  '__begin__'
        ),        
    );
    
    private $_currentColumn;
        
    public function __get($columnName)
    {
        return $this->getColumn($columnName);
    }
    
    public function resetColumns()
    {
        $this->_columns = null;
        $this->_columnsIndex = array(
            '__begin__' =>  array(
                'next'  =>  '__end__', 'prev'   =>  null
            ),
            '__end__' =>  array(
                'next'  =>  null, 'prev'   =>  '__begin__'
            ),        
        );
        
        $this->_currentColumn = null;
    } 
    
    public function addColumn(FinalView_Grid_Column $column, 
        $appendType = FinalView_Grid_ColumnsCollection::APPEND_LAST, $relatedColumn = null)
    {                
        switch ($appendType) {
            case FinalView_Grid_ColumnsCollection::APPEND_LAST:
                $columnName = $this->getLastColumn()?$this->getLastColumn()->getName():'__begin__';
                $this->insertColumnAfter($column, $columnName);
            break;
            case FinalView_Grid_ColumnsCollection::APPEND_FIRST:
                $columnName = $this->getFirstColumn()?$this->getFirstColumn()->getName():'__end__';
                $this->insertColumnBefore($column, $columnName);
            break;
            case FinalView_Grid_ColumnsCollection::APPEND_AFTER_COLUMN:
                if (!is_null($relatedColumn) && $this->getColumn($relatedColumn) ) {
                    $this->insertColumnAfter($column, $relatedColumn);	
                }                
            break;
            case FinalView_Grid_ColumnsCollection::APPEND_BEFORE_COLUMN:
                if (!is_null($relatedColumn) && $this->getColumn($relatedColumn) ) {
                    $this->insertColumnBefore($column, $relatedColumn);	
                } 
            break;                                    
        }
    }
    
    public function getColumn($name)
    {
        if (isset($this->_columns[$name])) {
        	return $this->_columns[$name];
        }        
    }
    
    public function insertColumnAfter(FinalView_Grid_Column $column, $relatedColumn)
    {
        if (!$this->_columnsIndex[$relatedColumn]) {
            throw new FinalView_Grid_Exception('Wrong Related Column');
        }
        
        $columnName = $column->getName();

        $this->_columns[$columnName] = $column;
        
        $next = $this->_columnsIndex[$relatedColumn]['next'];
        
        $this->_columnsIndex[$relatedColumn]['next'] = $columnName;
        
        $this->_columnsIndex[$columnName]['next'] = $next;
        $this->_columnsIndex[$columnName]['prev'] = $relatedColumn;
        
        $this->_columnsIndex[$next]['prev'] = $columnName;

        $this->_currentColumn = $columnName;        
    }
    
    public function insertColumnBefore(FinalView_Grid_Column $column, $relatedColumn)
    {
        if (!$this->_columnsIndex[$relatedColumn]) {
            throw new FinalView_Grid_Exception('Wrong Related Column');
        }
        
        $columnName = $column->getName();

        $this->_columns[$columnName] = $column;
        
        $prev = $this->_columnsIndex[$relatedColumn]['prev'];
        
        $this->_columnsIndex[$relatedColumn]['prev'] = $columnName;
        
        $this->_columnsIndex[$columnName]['next'] = $relatedColumn;
        $this->_columnsIndex[$columnName]['prev'] = $prev;
        
        $this->_columnsIndex[$prev]['next'] = $columnName;
        
        $this->_currentColumn = $columnName;              
    }
    
    public function getFirstColumn()
    {
        if (($firstColumnName = $this->_columnsIndex['__begin__']['next']) == '__end__') {
            return null;
        }
        
        return $this->_columns[$firstColumnName];
    }
    
    public function getLastColumn()
    {
        if (($lastColumnName = $this->_columnsIndex['__end__']['prev']) == '__begin__') {
            return null;
        }
        
        return $this->_columns[$lastColumnName];
    }
    
    public function current()
    {
        if (empty($this->_columns)) {
        	return false;
        }
        
        if ($this->_currentColumn === null) {
        	$this->_currentColumn = $this->_columnsIndex['__begin__'];
        }
        
        return $this->_columns[$this->_currentColumn];
    }
    
    public function key()
    {
        return $this->_currentColumn;
    }
    
    public function next()
    {
        if (empty($this->_columns)) {
        	return false;
        }        
        $nextItem = $this->_columnsIndex[$this->_currentColumn]['next'];
        
        if ($nextItem == '__end__') {
            $this->_currentColumn = '__end__';
            return false;
        }        
        
        $this->_currentColumn = $nextItem;
        
        return $this->_columns[$this->_currentColumn]; 
    }
    
    public function rewind()
    {
        $this->_currentColumn = $this->_columnsIndex['__begin__']['next'];    
    }
    
    public function valid()
    {
        if (empty($this->_columns)) {
        	return false;
        }
        
        if ($this->_currentColumn == '__end__' || $this->_currentColumn == '__begin__') {
        	return false;
        }        
        return array_key_exists($this->_currentColumn, $this->_columns);
    }        
    
    
}