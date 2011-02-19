<?php
class FinalView_Application extends Zend_Application
{
    public function setOptions(array $options)
    {
        if (!empty($options['config'])) {
            $local_config = $options['config'];
            unset($options['config']);
            if (is_array($local_config)) {
                $_options = array();
                foreach ($local_config as $tmp) {
                    $_options = $this->mergeOptions($_options, $this->_loadConfig($tmp));
                }
                $options = $this->mergeOptions($options, $_options);
            } else {
                $options = $this->mergeOptions($options, $this->_loadConfig($local_config));
            }            
        }
        
        parent::setOptions($options); 
        
        if (isset($local_config)) {
            $this->_options['config'] = $local_config;
        }  
    }
}
