<?php
class FinalView_Config_Writer_DoctrineModel extends Zend_Config_Writer
{
    protected $_model;
    protected $_config_name;

    public function __construct(Zend_Config $config, $model, $config_name)
    {
        $this->setOptions(array(
            'config'        =>  $config,
            'model'         =>  $model,
            'configName'   =>  $config_name
        ));
    }
    
    public function setModel($model)
    {
        $this->_model = ucfirst($model);
    }
    
    public function setConfigName($name)
    {
        $this->_config_name = $name;
    }

    public function write()
    {
        $root = Doctrine::getTable($this->_model)->getTree()->fetchRoot($this->_config_name);
        
        if ($root instanceof Doctrine_Record && $root->exists()) {
            $root->getNode()->delete();
        }
        
        $this->_saveArray($this->_config);
    }
    
    private function _saveArray(Zend_Config $config, $parent = null)
    {
        if (is_null($parent)) {
            $parent = Doctrine::getTable($this->_model)->create(array(
                'name'          =>  'root',
                'config_name'   =>  $this->_config_name
            ));

            $parent->save();
            Doctrine::getTable($this->_model)->getTree()->createRoot($parent);
        }

        foreach ($config as $key => $value) {
            $node = new $this->_model;
            $node->name = $key;
            if (is_scalar($value)) {
                $node->value = $value;
            }
            $parent->refresh();
            $node->getNode()->insertAsLastChildOf($parent);

            if ($value instanceof Zend_Config) {
                $this->_saveArray($value, $node);
            }
        }
    }
}