<?php 
class FinalView_Grid
{
    /**
     *
     * @var Zend_View
     */
    protected $_view;
    protected $_data;
    protected $_titles;
    protected $_viewScript;
    protected $_uri;
    protected $_actions;
    protected $_buttons;
    protected $_buttonsList = array();
    protected $_actionsList = array();
    protected $_titlesList = array();
    protected $_fieldsDecorators = array();
    
    private $_uriParams = array();    
    /**
     * @var FinalView_Grid_Decorator_Abstract
     */
    protected $_decorator = null;
    /**
     * @var Doctrine_Pager
     */
    protected $_pager = null;
    
    public function __construct($titles = array(), $uri = '#')
    {
        $this->_view = new Zend_View();
        $this->_titles = $titles;
        $this->_uri = $uri;
    }
    /**
     * Set grid decorator
     * @param FinalView_Grid_Decorator_Abstract $decorator
     */
    public function setDecorator(FinalView_Grid_Decorator_Abstract $decorator)
    {
        $this->_decorator = $decorator;
    }
    /**
     * Get grid decorator
     * @return FinalView_Grid_Decorator_Abstract
     */
    public function getDecorator()
    {
        if (null === $this->_decorator) {
            $this->_decorator = new FinalView_Grid_Decorator_Standart($this);
        }
        
        return $this->_decorator;
    }
    
    /**
     * Set grid titles
     * @param array $titles
     */
    public function setTitles(array $titles)
    {
        foreach ($titles as $key => $title) {
            $this->addTitle($key, $title);
        }
        
        return $this;
    }
    
    public function setPager(Doctrine_Pager $pager)
    {
        $this->_pager = $pager;
        return $this;
    }
    
    public function setUri($uri)
    {
        $this->_uri = $uri;
        return $this;
    }
    
    public function setUriParams($params)
    {
        $this->_uriParams = $params;
        return $this;
    }
    
    public function getUriParams()
    {
        return $this->_uriParams;    
    }    
    
    /**
     * Add title
     * @param string $field
     * @param array $title
     * @return 
     */
    public function addTitle($field, $title)
    {
        if ($title instanceof FinalView_Grid_Title_Abstract) {
            $this->_titlesList[$field] = $title;
        } else {
            $titleClass = 'FinalView_Grid_Title_Decorator_'.ucfirst($title['decorator']);
            $this->_titlesList[$field] = new $titleClass($title['title']);
        }
        $this->_titlesList[$field]->setGrid($this);
    }
    
    public function setData($data)
    {
        $this->_data = $data;
        return $this;
    }

    public function addFieldsDecorators(array $decorators)
    {
        foreach ($decorators as $field => $decorator) {
            $this->addFieldDecorator($field, $decorator);
        }
    }
    
    public function addFieldDecorator($field, $decorator)
    {
        if ($decorator instanceof FinalView_Grid_Field_Decorator_Abstract) {
            $this->_fieldsDecorators[$field] = $decorator;
        } else {
            $fieldDecorator = 'FinalView_Grid_Field_Decorator_'.ucfirst($decorator);
            $this->_fieldsDecorators[$field] = new $fieldDecorator();
        }
        return $this;
    }
    
    public function setViewScript($script)
    {
        $this->_view->setScriptPath(dirname($script));
        $this->_viewScript = basename($script);
    }
    /**
     * Add grid buttons
     * 
     * @param array $buttons
     */
    public function addButtons(array $buttons)
    {
        foreach ($buttons as $button) {
            $this->addButton($button);
        }
    }
    /**
     * Add grid button
     * @param mixed $button - array or FinalView_Grid_Button_Abstract
     */
    public function addButton($button)
    {
        if ($button instanceof FinalView_Grid_Button_Decorator_Abstract) {
            $this->_buttonsList[$button['name']] = $button;
        } else {
            $buttonClass = 'FinalView_Grid_Button_Decorator_'.ucfirst($button['decorator']);
            $this->_buttonsList[$button['name']] = new $buttonClass($button);
        }
        
        $this->_buttonsList[$button['name']]->setGrid($this);
    }
    /**
     * add grid actions
     * @param array $action
     *  
     */
    public function addActions(array $actions)
    {
        foreach ($actions as $action) {
            $this->addAction($action);
        }
    }
    /**
     * Add grid action
     * @param mixed $action - array or FinalView_Grid_Action_Abstract
     * 
     */
    public function addAction($action)
    {
        if ($action instanceof FinalView_Grid_Action_Abstract) {
            $this->_actionsList[] = $action;
        } else {
            $actionClass = 'FinalView_Grid_Action_Decorator_'.ucfirst($action['decorator']);
            $this->_actionsList[] = new $actionClass($action);
        }
    }
    
    /**
     * @deprecated - Now must use <b>addActions</b> and <b>addAction</b>
     * @param object $actions
     * @return 
     */
    public function setActions($actions)
    {
        $this->_actions = $actions;
    }
    /**
     * @deprecated - Now must use <b>addButtons</b> and <b>addButton</b> 
     * @param array $buttons
     */
    public function setButtons($buttons)
    {
        $this->_buttons = $buttons;
    }
    
    public function render()
    {
        if (null === $this->_decorator) $this->getDecorator();
        $this->_decorator->setUri($this->_uri);
        $this->_decorator->preRender();
        
        $this->_decorator->preRenderRow();
        foreach ($this->_titlesList as $title) {
            $title->render();
        }
        
        $this->_decorator->postRenderRow();
        
        $fields = array_keys($this->_titlesList);
        
        foreach ($this->_pager->execute() as $index => $row) {
            $this->_decorator->preRenderRow($index);
            foreach ($fields as $field) {
                if ($field == '0') continue;
                if (!isset($this->_fieldsDecorators[$field])) {
                    $this->_fieldsDecorators[$field] = new FinalView_Grid_Field_Decorator_Standart();
                }
                
                $this->_fieldsDecorators[$field]->render($row, $field);
            }
            if (count($this->_actionsList) > 0) {
                $this->_actionsList[0]->preRender();
                foreach ($this->_actionsList as $action) {
                    $action->render($row);
                }
                $this->_actionsList[0]->postRender();
            }
            $this->_decorator->postRenderRow();
        }
        $this->_decorator->postRender();
        if (!empty($this->_buttonsList)) {
            foreach ($this->_buttonsList as $button) {
                $button->render();
            }
        }
        
        if (!is_null($this->_pager)) {
            $this->_decorator->renderPager($this->_pager);
        }
        
        $this->_decorator->endRender();
        
        echo $this->_highlight();
    }
    
    protected function _highlight() 
    {
        $content = '<script type="text/javascript">'.PHP_EOL;
        $content .= "$(document).ready(function () { ".PHP_EOL;
        $content .= 
        '
            $(\'table.fv_gridtable tr:not(:first)\')
            .hover(function()
            {
                $(this).addClass(\'hover\');
            }, function()
            {
                $(this).removeClass(\'hover\');
            });
        ';
        $content .= "});".PHP_EOL;
        $content .= "</script>".PHP_EOL;
        
        return $content;
    }
    
    public function __toString()
    {
        try {
            $this->_view->titles = $this->_titles;
            $this->_view->uri = $this->_uri;
            $this->_view->values = $this->_data;
            $this->_view->rowActions = $this->_actions;
            $this->_view->buttons = $this->_buttons;
            $data = $this->_view->render($this->_viewScript);
        }
        catch(Exception $e) {
            return $e->getMessage();
        }
        return $data;
    }
}
