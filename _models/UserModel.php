<?php
//Include de bestanden de wij nodig hebben
require_once(__DIR__ . "/../_models/Model.php");
require_once(__DIR__ . "/../_helpers/MysqlHelper.php");
require_once(__DIR__ . "/../_models/FollowerModel.php");

class UserModel extends Model {
  public string $username;
  public string $email;
  public string $firstname;
  public string $middlename;
  public string $lastname;
  public string $birthdate;
  public string $password;
  public string $date_created;
  public string $date_updated;
  public string $pf_path;

  //Check of meegegeven email al in de user tabel bestaat
  public static function checkIfMailExists($email){
    $checkquery = "SELECT * FROM User WHERE email = ?";
    $sql = MysqlHelper::runPreparedQuery($checkquery, [$email], ["s"]);
    
    return !empty($sql);
  }
  
  //Check of meegegeven username al in de tabel bestaat
  public static function checkIfUserExists($username){
    $checkquery = "SELECT * FROM User WHERE username = ?";
    $sql = MysqlHelper::runPreparedQuery($checkquery, [$username], ["s"]);

    return !empty($sql);
  }

  //Update een gebruiker
  public function update(){
    $query = "
      UPDATE User SET email = ?, firstname = ?, middlename = ?, lastname = ?, birthdate = ?, password = ? WHERE username = ?";
      
      MysqlHelper::runPreparedQuery($query, [
        $this->email,
        $this->firstname,
        $this->middlename,
        $this->lastname,
        $this->birthdate,
        $this->password,
        $this->username
    ], ["s", "s", "s", "s", "s", "s","s"]);
    
  }


  //Insert een nieuwe gebruiker in de user tabel
  public function create(){
      $query = "
          INSERT INTO User
          (username, email, firstname, middlename, lastname, birthdate, password)
          VALUES(?, ?, ?, ?, ?, ?, ?);
      ";

      MysqlHelper::runPreparedQuery($query, [
          $this->username,
          $this->email,
          $this->firstname,
          $this->middlename,
          $this->lastname,
          $this->birthdate,
          $this->password
      ], ["s", "s", "s", "s", "s", "s", "s"]);

  }

  public static function getByUsername($username) {
    $query = "
        SELECT * FROM User vm
        WHERE username = ?
    ";

    $response = MysqlHelper::runPreparedQuery($query, [$username], ["s"]);
    if(empty($response)) return false;
    [$data] = $response;

    $object = new UserModel($data["firstname"], $data["middlename"], $data["lastname"]);

    self::fillObject($object, $data);

    return $object;
  }

  public function listFollowers(){
    $followers = FollowerModel::listByUsername($this->username);
    return $followers ? $followers : [];
  }

  public static function getByEmail($email) {
    $query = "
        SELECT * FROM User vm
        WHERE email = ?
    ";

    $response = MysqlHelper::runPreparedQuery($query, [$email], ["s"]);
    if(empty($response)) return false;
    [$data] = $response;

    $object = new UserModel($data["firstname"], $data["middlename"], $data["lastname"]);

    self::fillObject($object, $data);

    return $object;
  }

  public function __construct($firstname, $middlename, $lastname) {
    $this->firstname = $firstname;
    if (isset($middlename)) {
      $this->middlename = $middlename;
    }
    $this->lastname = $lastname;
  }

  public function checkPassword($password) {//Controlleren of $password het wachtwoord van de gebruiker is.
    $hash = $this->password;
    return password_verify($password, $hash);
  }

  public function changePassword($password){
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $query = "
      UPDATE User
      SET password = ?
      WHERE email = ?
    ";

    MysqlHelper::runPreparedQuery($query, [$hash, $this->email], ["s", "s"]);
  }

  public function listCategory(){
    $username = CategoryModel::listByUsername($this->username);
    if (!$username){
      return [];
    } else {
      return $username;
    }
  }

  public function uploadImage($target_file){
    $query = "
      UPDATE User
      SET pf_path = ?
      WHERE email = ?
    ";
    
    MysqlHelper::runPreparedQuery($query, [$target_file, $this->email], ["s", "s"]);
  }

  public static function search($search){
    $search = "%" . $search . "%";

    $query = "
      SELECT * FROM User
      WHERE username LIKE ?
    ";

    $results = MysqlHelper::runPreparedQuery($query, [$search], ["s"]);
    $count = count($results);
    if($count == 0) {
      return false;
    }

    $models = [];

    foreach($results as $row){
      $model = new UserModel($row["firstname"], $row["middlename"], $row["lastname"]);
      self::fillObject($model, $row);

      array_push($models, $model);
    }

    return $models;
  }
}

?>
