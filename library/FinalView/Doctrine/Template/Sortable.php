<?php

class FinalView_Doctrine_Template_Sortable extends Doctrine_Template
{

    protected $_options = array('manyListsColumn' => null);

    public function setTableDefinition()
    {
        $this->hasColumn('position', 'integer', 4);
        $this->addListener(new FinalView_Doctrine_Listener_Sortable());
    }

    public function getPrevious()
    {
        $many = $this->_options['manyListsColumn'];

        $query = $this->getInvoker()->getTable()->createQuery()
            ->addWhere('position < ?', $this->getInvoker()->position)
            ->orderBy('position DESC');
        if (!empty($this->_options['manyListsColumn'])) {
            $this->_applyManyListColumns($query, (array)$this->_options['manyListsColumn']);
        }

        return $query->fetchOne();
    }

    public function getNext()
    {
        $query = $this->getInvoker()->getTable()->createQuery()
            ->addWhere('position > ?', $this->getInvoker()->position)
            ->orderBy('position ASC');
        if (!empty($this->_options['manyListsColumn'])) {
            $this->_applyManyListColumns($query, (array)$this->_options['manyListsColumn']);
        }

        return $query->fetchOne();
    }

    private function _applyManyListColumns(Doctrine_Query $query, array $many_lists_column)
    {
        foreach ($many_lists_column as $column) {
            if (is_null($this->getInvoker()->$column)) {
                $query->addWhere($column . ' IS NULL');
            } else {
                $query->addWhere($column . ' = ?', $this->getInvoker()->$column);
            }
        }
    }

    public function swapWith(Doctrine_Record $record2)
    {
        $record1 = $this->getInvoker();

        $many = $this->_options['manyListsColumn'];
        if (!empty($many)) {

            $record1_values = $record2_values = array();
            foreach ((array)$many as $column) {
                $record1_values[] = $record1->$column;
                $record2_values[] = $record2->$column;
            }
            if (count(array_diff($record1_values, $record2_values)) ||
                count(array_diff($record2_values, $record1_values)))
            {
                throw new Doctrine_Record_Exception('Cannot swap items from different lists.');
            }

            /*if ($record1->$many != $record2->$many) {
                throw new Doctrine_Record_Exception('Cannot swap items from different lists.');
            }*/
        }

        $conn = $this->getTable()->getConnection();
        $conn->beginTransaction();

        $pos1 = $record1->position;
        $pos2 = $record2->position;
        $record1->position = $pos2;
        $record2->position = $pos1;
        $record1->save();
        $record2->save();

        $conn->commit();
    }

    public function moveUp()
    {
        $prev = $this->getInvoker()->getPrevious();
        if ($prev) {
            $this->getInvoker()->swapWith($prev);
        }
    }

    public function moveDown()
    {
        $next = $this->getInvoker()->getNext();
        if ($next) {
            $this->getInvoker()->swapWith($next);
        }
    }

}