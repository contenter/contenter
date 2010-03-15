<?php
class FinalView_Grid_Title_Decorator_Checkbox extends FinalView_Grid_Title_Decorator_Abstract
{
    public function render()
    {
        $content = '<script type="text/javascript">'.PHP_EOL;
        $content .= "$(document).ready(function () { ".PHP_EOL;
        $content .= "$('#checkAll').click(function () { ".PHP_EOL;
        $content .= "if(this.checked) { ".PHP_EOL;
        $content .= "$(this).parents('table').find('input[type=\"checkbox\"][class=\"fv_fieldcheckbox\"]').attr('checked', true);".PHP_EOL;
        $content .= "        } else { ".PHP_EOL;
        $content .= " $(this).parents('table').find('input[type=\"checkbox\"][class=\"fv_fieldcheckbox\"]').attr('checked', false);".PHP_EOL;
        $content .= "        }".PHP_EOL;
        $content .= "        return true;".PHP_EOL;
        $content .= "    });".PHP_EOL;
        $content .= "});".PHP_EOL;
        $content .= "</script>".PHP_EOL;
        $this->getGrid()->getDecorator()->addPostRenderContent($content);
        echo '<th class="fv_checkall"><input type="checkbox" id="checkAll" />'.$this->_title.'</th>'.PHP_EOL;
    }
}
