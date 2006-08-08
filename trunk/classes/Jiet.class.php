<?php
require_once 'classes/class.jabber.php';

class Jiet
{
    var $objJabber = null;
    var $strLogfilename = 'log.txt';

    function Jiet()
    {
        $this->objJabber = new Jabber;
        $this->session_id = md5(rand, (microtime() ));

    }

    function connect($arrParams)
    {
        $this->objJabber->server = $arrParams['server'];
        $this->objJabber->port = $arrParams['port'];
        $this->objJabber->username = $arrParams['username'];
        $this->objJabber->password = $arrParams['password'];
        $this->objJabber->resource = $arrParams['resource'];
        $this->objJabber->enable_logging = $arrParams['enable_logging'];
        $this->objJabber->log_filename = sprintf("%s.log", $this->session_id);

        if(!$this->objJabber->Connect())
        {
            die('can not connect');
        }

        if(!$this->objJabber->SendAuth())
        {
            die('can not authorize');
        }

        return true;
    }

    function getRoster()
    {
        $this->objJabber->RosterUpdate();
        return($this->objJabber->roster);
    }
}

?>
