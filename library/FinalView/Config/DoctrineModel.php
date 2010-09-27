<?php
class FinalView_Config_DoctrineModel extends Zend_Config
{
    private $_root;

    public function __construct($model, $config_name, $allowModifications = false)
    {
        $this->_root = Doctrine::getTable($model)->getTree()->fetchRoot($config_name);
        
        if ( ! ($this->_root instanceof Doctrine_Record) || !$this->_root->exists()) {
            throw new Zend_Config_Exception('config ' . $config_name . ' is not exists in '. $model);
        }

        $dataArray = $this->_treeToArray($this->_root);

        parent::__construct($dataArray, $allowModifications);
    }
    
    public function getRoot()
    {
        return $this->_root;
    }
    
    private function _treeToArray(Doctrine_Record $node)
    {
        $data = array();
        foreach ($node->getNode()->getChildren() as $child) {
            $data[$child->name] = $child->getNode()->hasChildren() ? $this->_treeToArray($child) : $child->value;
        }
        return $data;
    }
}