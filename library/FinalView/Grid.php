<?php 
class FinalView_Grid extends FinalView_Grid_Entity_Abstract
{

    const GRID_NAME = 'grid';
    
    protected $_columns;
    protected $_plugins = array();
    protected $_iterator;
    
    protected $_renderer;
    
    protected $_script = 'grid.phtml';
    
    public function __construct(array $options = array())
    {
        $forbidden = array();        
        
        if (isset($options['plugins'])) {
            foreach ((array)$options['plugins'] as $plugin) {
                $this->addPlugin($plugin);
            }            
        }
        
        foreach ($options as $key => $value) {
            $normalized = ucfirst($key);
            if (in_array($normalized, $forbidden)) {
                continue;
            }

            $method = 'set' . $normalized;
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }        
    }
       
    public function getName()
    {
        return self::GRID_NAME;
    }
    
    private function _initPlugins()
    {
        foreach ($this->_plugins as $plugin) {
            $plugin->init();
        }
    } 
    
    public function addPlugin(FinalView_Grid_Plugin_Abstract $plugin)
    {        
        $plugin->setGrid($this);
        
        $this->_plugins[$plugin->name] = $plugin;
        
        $this->getRenderer()->addScriptPath($plugin->getScriptsPath());
    }
    
    public function getPlugins()
    {
        return $this->_plugins;
    }
    
    public function getPlugin($name)
    {
        if (isset($this->_plugins[$name])) return $this->_plugins[$name]; 
    }
    
    public function setIterator($iterator)
    {
        $this->_iterator = $iterator;        
    }
    
    public function getIterator()
    {
        return $this->_iterator;
    }
    
    public function setColumns(array $columns)
    {
        $this->getColumns()->resetColumns();
        
        foreach ((array)$columns as $column) {
            $this->addColumn($column);
        }        
    }
    
    public function setColumnsFromIterator()
    {        
        $iterator = $this->getIterator();
        if (is_null($iterator)) {
            throw new FinalView_Grid_Exception('not defined iterator');
        }
        
        if ($iterator instanceof Doctrine_Collection) {
            $columns = $iterator->getTable()->getColumnNames();
        }else{
            $row = reset($iterator);
            if (!$row) {
                throw new FinalView_Grid_Exception('cannot define columns from iterator');
            }            
            $columns = array_keys($row);            
        }     
        
        foreach ($columns as $column) {
            $_columns[] = new FinalView_Grid_Column_Iterator($column); 
        }
        
        $this->setColumns($_columns);
    }
    
    public function addColumn(FinalView_Grid_Column $column, 
        $appendType = FinalView_Grid_ColumnsCollection::APPEND_LAST, $relatedColumn = null)
    {
        $this->getColumns()->addColumn($column, $appendType, $relatedColumn);
    }
    
    public function getColumns()
    {
        if (is_null($this->_columns)) {
            $this->_columns = new FinalView_Grid_ColumnsCollection;
        }
        
        return $this->_columns;
    }
    
    public function getRenderer()
    {
        if ($this->_renderer === null) {
            $this->_renderer = new FinalView_Grid_Renderer($this);
        }
        
        return $this->_renderer;
    }
    
    public function render($entity, $inputParams = array())
    {
        switch (true) {
            case is_string($entity):
                $entityName = $entity;
            break;
            case $entity instanceof FinalView_Grid_Entity_Abstract:
                $entityName = $entity->getName();
            break;
            default:
               throw new FinalView_Grid_Exception('Not valid entity for rendering');
            break;
        }
        $this->getRenderer()->clearScript();
        $currNamespace = $this->getRenderer()->currentNamespace();
        $this->getRenderer()->useNamespace($entityName);
        
        $filter = new Zend_Filter_Word_UnderscoreToCamelCase();
        $customHandler = $filter->filter($entityName).'Handler';              
        if (method_exists($this, $customHandler)) {     
            $this->$customHandler($inputParams, $this->getRenderer() );
        }elseif(is_object($entity)){             
            $entity->handler($inputParams, $this->getRenderer());
        }else{
            $this->getRenderer()->assign($inputParams);
        }
        
        $this->getRenderer()->entity = $entity;        

        if ($this->getRenderer()->getScript() === null) {
            if (is_object($entity)) {
                $this->getRenderer()->setScript($entity->getScript());                 
            }else{
                $this->getRenderer()->setScript($entityName.'.phtml');    
            }            
        }
                
        $content = $this->getRenderer()->renderScript();
        $this->getRenderer()->useNamespace($currNamespace);
        
        return $content;
    }
    
    public function FormHeaderHandler($params, $view)
    {
        $view->url = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
        $view->method = 'post';
    }
    
    public function TableHandler($params, $view)
    {
        $view->attribs = array(
            'class' =>  'fv-grid-table'
        );
    }
    
    public function TableRowHandler($params, $view)
    {
        $className = (intval($params['key']) % 2) ? 'odd' : 'event';
        $view->tr_attribs = array(
            'class' =>  'row '.$className
        );
        $view->row = $params['row'];
    }
    
    public function ColumnHandler($params, $view)
    {
        $view->td_attribs = array(
            'class' =>  'column'
        );
        $view->row = $params['row'];
        $view->column = $params['column'];           
    }
    
    public function TableHeaderHandler($params, $view)
    {
        $view->tr_attribs = array(
            'class' =>  'row-header'
        );
        $view->th_attribs = array(
            'class' =>  'column-header'
        );        
    }
    
    public function __toString()
    {
        try{
            $this->_initPlugins();
            return $this->render($this);        
        }catch(Exception $e){
            trigger_error($e->getMessage(), E_USER_ERROR);        
        }
        return '';
    }    
}
