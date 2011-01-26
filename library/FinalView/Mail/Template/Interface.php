<?php

interface FinalView_Mail_Template_Interface
{

    public function getSubject();

    public function getBodyText();

    public function getBodyHtml();

}