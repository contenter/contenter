<?php
class FinalView_Grid_Button_Decorator_Standart extends FinalView_Grid_Button_Decorator_Abstract
{
    public function render()
    {
        $content = '<script type="text/javascript">'.PHP_EOL;
        $content .= "$(document).ready(function () { ".PHP_EOL;
        $content .= "    $('#btn_".$this->_button['name']."').click(function () { ".PHP_EOL;
        if (!empty($this->_button['confirm']) && $this->_button['confirm'] === true) {
            $content .= "if (confirm('Are you sure?')) { ".PHP_EOL;
            $content .= "    $('#grid_form').attr('action', '".$this->_button['url']."/".$this->_button['name']."');".PHP_EOL;
            $content .= "    $('#grid_form').submit();".PHP_EOL;
            $content .= "}".PHP_EOL;
        } else {
            $content .= "    $('#grid_form').attr('action', '".$this->_button['url']."/".$this->_button['name']."');".PHP_EOL;
            $content .= "    $('#grid_form').submit();".PHP_EOL;
        }
        $content .= "    });";
        $content .="});".PHP_EOL;
        $content .="</script>".PHP_EOL;
        
        $this->getGrid()->getDecorator()->addPostRenderContent($content);
        
        echo '<input type="button" name="'.$this->_button['name'].'" id="btn_'.$this->_button['name'].'" value="'.$this->_button['title'].'"/>'.PHP_EOL;
    }
}