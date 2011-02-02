<?php
class FinalView_Controller_Router extends Zend_Controller_Router_Route
{

    protected $_innerRoutes = array();
    protected $_parts = array();

    public function getVersion() {
        return 1;
    }

    public function __construct($route, $defaults = array(), $reqs = array(), Zend_Translate $translator = null, $locale = null)
    {

        if (strpos($route, '{') === false || strpos($route, '}') === false ) {
            throw new FinalView_Controller_Router_Exception('Router must have optional part in round brackets');
        }

        $route = trim($route, '/');
        $this->_defaults     = (array) $defaults;
        $this->_requirements = (array) $reqs;
        $this->_translator   = $translator;
        $this->_locale       = $locale;

        $pos_close = 0;

        while (($pos_open = strpos($route, '{', $pos_close)) !== false) {
            if (($pos_close = strpos($route, '}', $pos_open)) === false) {
                throw new FinalView_Controller_Router_Exception('not found close brackets in route '. $route);
            }

            $this->_parts[] = array(
                'part'  =>  substr($route, $pos_open, $pos_close - $pos_open + 1),
                'open'  =>  $pos_open,
                'close' =>  $pos_close
            );

        }

        if (($count = count($this->_parts)) == 0) {
            throw new FinalView_Controller_Router_Exception('Router must have optional part in round brackets');
        }

        $this->_route = $route;

        $countVariants = pow(2, $count);

        for ($i = 0; $i < $countVariants; $i++) {
            $this->_innerRoutes[] = new Zend_Controller_Router_Route($this->_buildSubRoute($i), $defaults, $reqs, $translator, $locale);
        }
    }

    protected function _buildSubRoute($i)
    {
        $route = $this->_route;
        for ($j = 0; $i >= $k = pow(2, $j); $j++) {
            if (($i & $k) == $k) {
                $route = str_replace($this->_parts[$j], '', $route);
            }
        }

        $route = str_replace('{', '', $route);
        $route = str_replace('}', '', $route);

        return $route;
    }

    public static function getInstance(Zend_Config $config)
    {
        $reqs = ($config->reqs instanceof Zend_Config) ? $config->reqs->toArray() : array();
        $defs = ($config->defaults instanceof Zend_Config) ? $config->defaults->toArray() : array();
        return new self($config->route, $defs, $reqs);
    }

    public function match($path, $partial = false)
    {
        foreach ($this->_innerRoutes as $route) {
            if ($params = $route->match($path, $partial)) {
                $this->setMatchedPath($route->getMatchedPath());
                return $params;
            }
        }

        return false;
    }

    public function assemble($data = array(), $reset = false, $encode = false, $partial = false)
    {
        unset($data['module'], $data['controller'], $data['action']);

        foreach ($this->_innerRoutes as $route) {

            $route_vars = $route->getVariables();
            $query_params = array_keys($data);

            $a = array_diff($route_vars, $query_params);
            if(empty($a)) {
                $b = array_diff($query_params, $route_vars);
                if (empty($b)) {
                    return $route->assemble($data, $reset, $encode, $partial);
                }
            }
        }

        throw new FinalView_Controller_Router_Exception(
            'Can not build route using provided parameters. Parmeters must satisfy route: '.$this->_route
        );
    }
}
