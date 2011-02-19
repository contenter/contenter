<?php
class FinalView_Form_Model extends Zend_Form
{
    protected $_columnTypes = array(
        'integer' => 'text',
        'decimal' => 'text',
        'float' => 'text',
        'string' => 'text',
        'varchar' => 'text',
        'boolean' => 'checkbox',
        'timestamp' => 'text',
        'time' => 'text',
        'date' => 'text',
        'enum' => 'select'
    );

    protected $_columnValidators = array(
        'integer' => 'int',
        'float' => 'float',
        'double' => 'float'
    );

    protected $_model = '';

    protected $_formModel = array();

    protected $_record;

    public function setFormModel($formModel = array())
    {
        $this->_formModel = $formModel;
        if (!isset($this->_formModel['model'])) {
            throw new Exception('Initial model must be defined');
        }
        $this->_model = $this->_formModel['model'];
    }

    public function getFieldTypes($field = null)
    {
        if ($field === null) {
            return @$this->_formModel['fieldTypes'];
        }
        return @$this->_formModel['fieldTypes'][$field];
    }

    public function getFieldLabels($field = null)
    {
        if ($field === null) {
            return @$this->_formModel['fieldLabels'];
        }
        return @$this->_formModel['fieldLabels'][$field];
    }

    public function isIgnoreColumn($column)
    {
        return in_array($column, (array)@$this->_formModel['ignoreColumns']);
    }

    public function isSpecifiedRelation($relation)
    {
        return array_key_exists($relation, (array)@$this->_formModel['relations']);
    }

    protected function getTableModel()
    {
        return Doctrine::getTable($this->_model);
    }

    public function createForm()
    {
        $this->_preGenerate();
        $this->_generateForm();
        $this->_postGenerate();
    }

    /**
     * Override to provide custom pre-form generation logic
     */
    protected function _preGenerate()
    {
    }

    /**
     * Override to provide custom post-form generation logic
     */
    protected function _postGenerate()
    {
    }

    protected function _generateForm()
    {
        $this->_columnsToFields();
        $this->_relationsToFields();
    }

    protected function _columnsToFields()
    {
        foreach($this->_getColumns() as $name => $definition)
        {
            $type = $this->_columnTypes[$definition['type']];
            if($this->getFieldTypes($name))
                $type = $this->getFieldTypes($name);

            $field = $this->createElement($type, $name);
            $label = $name;

            if($this->getFieldLabels($name)){
                $label = $this->getFieldLabels($name);
            }

            if(isset($this->_columnValidators[$definition['type']]))
                $field->addValidator($this->_columnValidators[$definition['type']]);

            if(isset($definition['notnull']) && $definition['notnull'] == true)
                $field->setRequired(true);

            $field->setLabel($label);

            if($type == 'select' && $definition['type'] == 'enum')
            {
                foreach($definition['values'] as $text)
                {
                    $field->addMultiOption($text, ucwords($text));
                }
            }

            $this->addElement($field);
        }
    }

    protected function _relationsToFields()
    {
        if(empty($this->_formModel['relations'])) return;
        foreach($this->_formModel['relations'] as $relationName => $relationData)
        {
            if(!$this->getTableModel()->hasRelation($relationName)) continue;
            $relation = $this->getTableModel()->getRelation($relationName);

            $newSubForm = new FinalView_Form_Model(array(
                'formModel' =>  array('model' =>  $relation->getClass()) + $relationData,
                'elementsBelongTo'  =>  $relationName
            ));
            $newSubForm->createForm();
            $this->addSubForm($newSubForm, $relationName);
        }
    }

    protected function _getColumns($model = null)
    {
        $columns = array();
        foreach($this->getTableModel()->getColumns() as $name => $definition)
        {
            if((isset($definition['primary']) && $definition['primary']) ||
                !isset($this->_columnTypes[$definition['type']]) || $this->isIgnoreColumn($name))
                continue;

            $columns[$name] = $definition;
        }

        return $columns;
    }

    public function setRecord($record)
    {
        $this->_record = $record;
        return $this;
    }

    public function getRecord()
    {
        return $this->_record;
    }

    public function saveTo($saveModel, $values = null)
    {
        if ($values === null) {
            $values = $this->getValues();
        }

        foreach ($this->getSubForms() as $subForm) {
            $record = $subForm->getRecord();
            if ($record !== null){
                $id = $record->getTable()->getIdentifier();
                $arr = $values[$subForm->getName()];
                $arr[$id] = $record->$id;
                $values[$subForm->getName()] = array($arr);
            }
        }

        $saveModel->fromArray($values);
        $saveModel->save();
    }
}

