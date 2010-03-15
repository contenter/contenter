<?php

class FinalView_Paginator_Adapter_DoctrineQuery 
    implements Zend_Paginator_Adapter_Interface
{
    /**
     * Database query
     *
     * @var Doctrine_Query
     */
    protected $_query = null;

    /**
     * Total item count
     *
     * @var integer
     */
    protected $_rowCount = null;

    /**
     * Constructor.
     *
     * @param Doctrine_Query $q The select query
     */
    public function __construct(Doctrine_Query $q)
    {
        $this->_query = $q;
    }

    /**
     * Sets the total row count
     *
     * @param  Doctrine_Query|integer $totalRowCount Total row count integer
     *                                               or query
     * @return Zend_Paginator_Adapter_DbSelect $this
     * @throws Zend_Paginator_Exception
     */
    public function setRowCount($rowCount)
    {
        if ($rowCount instanceof Doctrine_Query) {
            $this->_rowCount = $rowCount->count();
        } else if (is_integer($rowCount)) {
            $this->_rowCount = $rowCount;
        } else {
            /**
             * @see Zend_Paginator_Exception
             */
            require_once 'Zend/Paginator/Exception.php';

            throw new Zend_Paginator_Exception('Invalid row count');
        }

        return $this;
    }

    /**
     * Returns an array of items for a page.
     *
     * @param  integer $offset Page offset
     * @param  integer $itemCountPerPage Number of items per page
     * @return array
     */
    public function getItems($offset, $itemCountPerPage)
    {
        return $this->_query->limit($itemCountPerPage)
                            ->offset($offset)
                            ->execute();
    }

    /**
     * Returns the total number of rows in the result set.
     *
     * @return integer
     */
    public function count()
    {
        if ($this->_rowCount === null) {
            $this->setRowCount(
                $this->_query->count()
            );
        }
        return $this->_rowCount;
    }
}