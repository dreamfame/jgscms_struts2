<?php
/**
 * Created by PhpStorm.
 * User: liu liu
 * Date: 2018/12/27
 * Time: 15:13
 */

    require_once 'DBHelper.php';
    require_once '../Extensions/Security.php';
    require_once '../Extensions/LoadXmlData.php';
    header("Content-Type: text/html;charset=utf-8");

    Class LoginStatusServer{
        public $db;
        public $conn;
        public $dbase;
        public $db_table;
        public function AreaServer()
        {
            $this->db = new DBHelper();
            $xc = new XmlControl();
            $this->dbase = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","db",0,"name");
            $this->db_table = $xc->GetXmlAttribute("../ProjectConfig/DBase.xml","table",12,"name");
            $this->conn = $this->db->Open($this->dbase);
        }

        public function LogIn(){

        }
    }