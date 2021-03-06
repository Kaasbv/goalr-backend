<?php
//Include de 2 bestanden die wij nodig hebben
require_once(__DIR__ . "/../_models/UserModel.php");
require_once(__DIR__ . "/../_helpers/MysqlHelper.php");

//Maak een class aan voor deze api call
class UserRegister {

  public static function run(){
    session_start();
        
    MysqlHelper::startConnection();
    
    header('Content-Type: application/json'); //Header om aan te geven dat de response json is
    //POST variabelen ophalen
    $email = $_POST['email'];
    $username = $_POST['username'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $birthdate = $_POST['birthdate'];
    $password = $_POST['password'];
    $middlename = $_POST["middlename"] ?? "";

    //Check of gebruiker en email bestaat
    if(userModel::checkIfUserExists($username)){
      $data = ["execution" => 'failure', 'msg' => 'User already exists'];
      http_response_code(400);
      echo json_encode($data);
    }
    elseif(userModel::checkIfMailExists($email)){
      $data = ["execution" => 'failure', 'msg' => 'Mail already exists'];
      http_response_code(400);
      echo json_encode($data);
    }
    else{
    //Hash en salt het wachtwoord
    $password = password_hash($password, PASSWORD_DEFAULT);

    //Maak usermodel aan
    $user = new UserModel($firstname, $middlename, $lastname);
    $user->email = $email;
    $user->birthdate = $birthdate;
    $user->password = $password;
    $user->username = $username;

    //Voer create functie uit, insert op database
    $user->create();
    $data = ["execution" => 'success', 'msg' => 'New user created'];

    //Geef een response
    http_response_code(200); //Zet een http code Heel belangrijk!
    echo json_encode($data); // echo de data array in json formaat voor de frontend
    
  }
  //Sluit de connectie
  MysqlHelper::closeConnection();
  }
}

//Run de run functie hiervan
UserRegister::run();