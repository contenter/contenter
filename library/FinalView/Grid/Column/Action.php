<?php
class FinalView_Grid_Column_Action extends FinalView_Grid_Column
{

    protected $_url;
    protected $_route_params;
    protected $_label;

    public function __construct($name, $label, $url_or_route = null,
        $iteratorFields_or_route_map = array(), $query_string_part_params = array())
    {
        parent::__construct($name, 'action.phtml');

        $this->_url = $url_or_route;

        if (is_string($this->_url)) {
            $this->_query_string_params = array_values($iteratorFields_or_route_map);
        }else{
            $this->_route_params = $iteratorFields_or_route_map;
            $this->_query_string_params = $query_string_part_params;
        }

        $this->_label = $label;
    }

    public function handler($params, FinalView_Grid_Renderer $view)
    {
        $view->columnName = $this->getName();

        $url_params = array();

        foreach ($this->_query_string_params as $param) {
            if (isset($params[$param])) {
                $url_params[$param] = $params[$param];
            }
        }

        $view->url_params = $url_params;
        $view->url = $this->_getUrl($params);
        $view->label = $this->_label;
    }

    protected function _getUrl($params)
    {
        switch (true) {
            case ($this->_url instanceof Zend_Controller_Router_Route_Abstract):
                foreach ($this->_route_params as  $route_param => $param) {
                    if (isset($params[$param])) {
                        $route_params[$route_param] = $params[$param];
                    }
                }

                try {
                    return $this->_url->assemble($route_params);
                } catch (Zend_Controller_Router_Exception $exception) {
                    trigger_error($exception->getMessage(), E_USER_ERROR);
                }
            break;
            case is_string($this->_url):
                return $this->_url;
            break;
            default:
                return $this->_url;
            break;
        }
    }
}
