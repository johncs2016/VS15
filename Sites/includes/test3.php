<?php
    include "data.php";
    include "XML.php";

    $obj = new weeklydata();
    $obj->setID(1);
    $obj->setUserID(1);
    $obj->setObsDate(new DateTime());
    $obj->setWeight(87.5);
    $obj->setWaistSize(90.3);
    $arr = (array)$obj;
    print_r($arr);
    die();
    //echo XMLSerializer::generateValidXmlFromObj($obj);
?>