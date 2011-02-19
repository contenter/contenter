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

        $this->resetColumnsIndex();

        $this->_currentColumn = null;

        return $this;
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

        return $this;
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

        return $this;
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

        return $this;
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

    public function isExistColumn($column)
    {
        if ($column == '__begin__' || $column == '__end__' || !isset($this->_columns[$column])) {
            return false;
        }
        return true;
    }

    public function removeColumn($column)
    {
        if (!$this->isExistColumn($column) ) {
            throw new FinalView_Grid_Exception('column ' . $column . 'doesn\'t exist');
        }

        unset($this->_columns[$column]);
        $prev = $this->_columnsIndex[$column]['prev'];
        $next = $this->_columnsIndex[$column]['next'];
        $this->_columnsIndex[$prev]['next'] = $next;
        $this->_columnsIndex[$next]['prev'] = $prev;

        unset($this->_columnsIndex[$column]);

        return $this;
    }

    public function removeColumns(array $columns)
    {
        foreach ($columns as $c) {
            $this->removeColumn($c);
        }

        return $this;
    }

    public function removeAllExcept(array $columns)
    {
        $_cols = array_keys($this->_columns);

        foreach ( array_diff($_cols, $columns) as $c) {
            $this->removeColumn($c);
        }

        return $this;
    }

    public function upColumn($column)
    {
        if (!$this->isExistColumn($column) ) {
            throw new FinalView_Grid_Exception('column doesn\'t exist');
        }

        if ($this->_columnsIndex[$column]['prev'] == '__begin__') {
            return;
        }

        $prevColumn = $this->_columnsIndex[$column]['prev'];
        $nextColumn = $this->_columnsIndex[$column]['next'];

        $prevPrevColumn = $this->_columnsIndex[$prevColumn]['prev'];

        $this->_columnsIndex[$prevColumn]['next'] = $nextColumn;
        $this->_columnsIndex[$prevColumn]['prev'] = $column;

        $this->_columnsIndex[$column]['prev'] = $prevPrevColumn;
        $this->_columnsIndex[$column]['next'] = $prevColumn;

        $this->_columnsIndex[$prevPrevColumn]['next'] = $column;
        $this->_columnsIndex[$nextColumn]['prev'] = $prevColumn;

        return $this;
    }

    public function downColumn($column)
    {
        if (!$this->isExistColumn($column) ) {
            throw new FinalView_Grid_Exception('column doesn\'t exist');
        }

        if ($this->_columnsIndex[$column]['next'] == '__end__') {
            return;
        }

        $nextColumn = $this->_columnsIndex[$column]['next'];

        $this->upColumn($nextColumn);

        return $this;
    }

    private function resetColumnsIndex()
    {
        $this->_columnsIndex = array(
            '__begin__' =>  array(
                'next'  =>  '__end__', 'prev'   =>  null
            ),
            '__end__' =>  array(
                'next'  =>  null, 'prev'   =>  '__begin__'
            ),
        );

        return $this;
    }

    public function move($column,
        $movingType = FinalView_Grid_ColumnsCollection::APPEND_LAST, $relationColumn = null)
    {
        switch ($movingType) {
            case FinalView_Grid_ColumnsCollection::APPEND_LAST:
                $columnName = $this->getLastColumn()?$this->getLastColumn()->getName():'__begin__';
                $this->moveColumnAfter($column, $columnName);
            break;
            case FinalView_Grid_ColumnsCollection::APPEND_FIRST:
                $columnName = $this->getFirstColumn()?$this->getFirstColumn()->getName():'__end__';
                $this->moveColumnBefore($column, $columnName);
            break;
            case FinalView_Grid_ColumnsCollection::APPEND_AFTER_COLUMN:
                if (!is_null($relationColumn) && $this->getColumn($relationColumn) ) {
                    $this->moveColumnAfter($column, $relationColumn);
                }
            break;
            case FinalView_Grid_ColumnsCollection::APPEND_BEFORE_COLUMN:
                if (!is_null($relationColumn) && $this->getColumn($relationColumn) ) {
                    $this->moveColumnBefore($column, $relationColumn);
                }
            break;
        }

        return $this;
    }

    public function setColumnsOrder(array $order)
    {
        $this->resetColumnsIndex();

        foreach ($order as $column) {
            if (!isset($this->_columns[$column])) {
                continue;
            }

            $this->move($column, FinalView_Grid_ColumnsCollection::APPEND_LAST);

            $newColumns[$column] = $this->_columns[$column];
        }

        if (isset($newColumns)) {
            $this->_columns = $newColumns;
        }

        return $this;
    }

    private function moveColumnAfter($column, $relatedColumn)
    {
        if (!$this->isExistColumn($column)) {
            throw new FinalView_Grid_Exception('column doesn\'t exist');
        }

        if (!array_key_exists($relatedColumn, $this->_columnsIndex) || $relatedColumn == '__end__') {
            throw new FinalView_Grid_Exception('not found relation column');
        }

        if (isset($this->_columnsIndex[$column])) {
            $prev = $this->_columnsIndex[$column]['prev'];
            $next = $this->_columnsIndex[$column]['next'];
            $this->_columnsIndex[$prev]['next'] = $next;
            $this->_columnsIndex[$next]['prev'] = $prev;
        }

        $next = $this->_columnsIndex[$relatedColumn]['next'];

        $this->_columnsIndex[$relatedColumn]['next'] = $column;

        $this->_columnsIndex[$column]['next'] = $next;
        $this->_columnsIndex[$column]['prev'] = $relatedColumn;

        $this->_columnsIndex[$next]['prev'] = $column;

        return $this;
    }

    private function moveColumnBefore($column, $relatedColumn)
    {
        if (!$this->isExistColumn($column)) {
            throw new FinalView_Grid_Exception('column doesn\'t exist');
        }

        if (!array_key_exists($relatedColumn, $this->_columnsIndex) || $relatedColumn == '__begin__') {
            throw new FinalView_Grid_Exception('not found relation column');
        }

        if (isset($this->_columnsIndex[$column])) {
            $prev = $this->_columnsIndex[$column]['prev'];
            $next = $this->_columnsIndex[$column]['next'];
            $this->_columnsIndex[$prev]['next'] = $next;
            $this->_columnsIndex[$next]['prev'] = $prev;
        }

        $prev = $this->_columnsIndex[$relatedColumn]['prev'];

        $this->_columnsIndex[$relatedColumn]['prev'] = $column;

        $this->_columnsIndex[$column]['next'] = $relatedColumn;
        $this->_columnsIndex[$column]['prev'] = $prev;

        $this->_columnsIndex[$prev]['next'] = $column;

        return $this;
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
