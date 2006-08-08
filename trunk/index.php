<?php
require_once 'classes/Jiet.class.php';

$strAction = trim(addslashes(@$_REQUEST['action']));
switch($strAction)
{
    default:
        $arrData = null;
        $strTemplate = 'main.php';
}

require_once 'templates/'.$strTemplate;

?>
