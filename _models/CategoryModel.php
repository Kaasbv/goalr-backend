<?php
//Include de bestanden de wij nodig hebben
require_once(__DIR__ . "/../_models/Model.php");
require_once(__DIR__ . "/../_helpers/MysqlHelper.php");

class CategoryModel extends Model {
  
  public function __construct(
    public string $name,
    public string $username
  ){}
  public string $date_created;
  public string $date_updated;

  public static function listByUsername($username){
    //SQL query om usernames van Category table te halen
    $category_query = "SELECT * FROM `Category` WHERE `username` = ?";

    $response = MysqlHelper::runPreparedQuery($category_query, [$username], ["s"]);
    if(empty($response)) return false;
  
    foreach($response as $row){
      $object = new CategoryModel($row['username'], $row['name']);
      self::fillObject($object, $row);
      $data[] = $object;
    }

    return $data;
  }

    // Nieuwe category opslaan in DB
    public function create(){
      $query = "INSERT INTO `Category` (name, username) VALUES (?, ?) ";
  
      MysqlHelper::runPreparedQuery($query, [$this->name, $this->username], ["s", "s"]);
    }
}

?>
