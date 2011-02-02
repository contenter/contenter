<?php

class FinalView_Doctrine_Table extends Doctrine_Table
{
    private $_queryParams;
    private $_query;

    private $_joinedTables = array();

    /**
     * @return Doctrine_Query
     */
    final public function _getQuery()
    {
        if ($this->_query === null) {
            $this->_query = $this->createQuery($this->getTableName());
        }

        return $this->_query;
    }

    public function resetQuery()
    {
        $this->_query = null;

        foreach ($this->_joinedTables as $table) {
            $table->resetQuery();
        }

        $this->_joinedTables = array();
    }

    final public function findByParams($params = array(), $hydrationMode = null)
    {
        $this->build($this->_getQuery(), $params);

        $result = $this->_getQuery()->execute(array(), $hydrationMode);

        $this->resetQuery();

        return $result;
    }

    final public function findOneByParams($params = array(), $hydrationMode = null)
    {
        $this->build($this->_getQuery(), $params);

        $result = $this->_getQuery()->limit(1)->fetchOne(array(), $hydrationMode);

        $this->resetQuery();

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

        $this->resetQuery();

        return $pager;
    }

    final public function countByParams($params = array())
    {
        $this->build($this->_getQuery(), $params);

        $result = $this->_getQuery()->count(array());

        $this->resetQuery();

        return $result;
    }

    final public function updateByParams($params = array(), $values = array())
    {
        foreach ( $values as $key=>$value ) {
            $this->_getQuery()->set($key, $value);
        }

        $this->build($this->_getQuery()->update(), $params);

        $result = $this->_getQuery()->execute(array());

        $this->resetQuery();

        return $result;
    }

    final public function deleteByParams($params = array())
    {
        $this->build($this->_getQuery()->delete(), $params);

        $result = $this->_getQuery()->execute(array());

        $this->resetQuery();

        return $result;
    }

    public function build($query, $params)
    {
        $this->_queryParams = $params;
        $this->_query = $query;

        $filter = new Zend_Filter_Word_UnderscoreToCamelCase();

        foreach ($params as $param=>$value) {
            $method = lcfirst($filter->filter($param)) . 'Selector';
            if (!method_exists($this, $method)) {
                if ($this->hasColumn($param) ) {
                    $this->_fieldSelector($param, $value);
                    continue;
                }else{
                    throw new FinalView_Doctrine_Table_Exception('there is no selector ' . $param . ' in model ' . get_class($this) );
                }
            }

            $this->$method($value);
        }

        //$this->_getQuery()->useResultCache(true);

        return $this->_getQuery();
    }

    protected function innerJoin($relation, array $params = array(), $on = '')
    {
        $tableObject = $this->getRelation($relation)->getTable();

        if (!empty($on)) {
            $on = ' ON (' . $on . ')';
        }

        $this->_getQuery()->innerJoin($this->getTableName() . '.' . $relation . ' ' . $tableObject->getTableName() . $on);
        $this->_joinedTables[] = $tableObject;

        return $tableObject->build($this->_query, $params);
    }

    protected function LeftJoin($relation, array $params = array(), $on = '')
    {
        $tableObject = $this->getRelation($relation)->getTable();

        if (!empty($on)) {
            $on = ' ON (' . $on . ')';
        }

        $this->_getQuery()->LeftJoin($this->getTableName() . '.' . $relation . ' ' . $tableObject->getTableName() . $on );
        $this->_joinedTables[] = $tableObject;

        return $tableObject->build($this->_query, $params);
    }

    private function _fieldSelector($field_name, $value)
    {
        if (is_null($value)) {
            $this->_getQuery()->addWhere($this->getTableName().'.' . $field_name . ' IS NULL');
        } else {
            $this->_getQuery()->addWhere($this->getTableName().'.' . $field_name . ' = ?', $value);
        }
    }

    protected function limitSelector($params)
    {
        $this->_getQuery()
            ->limit($params['per_page'])
            ->offset($params['per_page']*$params['page'])
            ;
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
