<?php

require_once APP_DIR.'/config/database.php';

class config
{

    var $link;
    var $arrdb = array();

    function __construct()
    {
        $dblistobj = new DATABASE_CONFIG();
        $this->arrdb = get_class_vars(get_class($dblistobj));
        if (!isset($this->link)) $this->db_connect();
    }

    function db_connect()
    {
        if (array_key_exists(SITECOMPLI_ENVIRONMENT, $this->arrdb)) {
            $host = $this->arrdb[SITECOMPLI_ENVIRONMENT]['host'];
            $login = $this->arrdb[SITECOMPLI_ENVIRONMENT]['login'];
            $password = $this->arrdb[SITECOMPLI_ENVIRONMENT]['password'];
            $database = $this->arrdb[SITECOMPLI_ENVIRONMENT]['database'];
            $this->link = mysqli_connect($host, $login, $password,$database);
            
        }
    }

    function reconnect_disconnect($res)
    {
        mysqli_free_result($res);   
		mysqli_next_result($this->link);  
    }

}

?>