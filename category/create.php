<?php
//Include de 2 bestanden die wij nodig hebben
include_once(__DIR__ . "/../_models/CategoryModel.php");
include_once(__DIR__ . "/../_helpers/MysqlHelper.php");

//Maak een class aan voor deze api call
class CategoryCreate {
  public static function run(){
    session_start();
    //Start een connectie
    MysqlHelper::startConnection();

    //Zet hier je code neer

    //Sluit de connectie
    MysqlHelper::closeConnection();
  }
}

//Run de run functie hiervan
CategoryCreate::run();