<div id="content">
  <h1>Список пользователей</h1>

  <table style="width:100%" >
  <tr>
    <th colspan="5">Список пользователей</th> 

  </tr>

  <?php 

    $users = $result['templateCtrlResult']['result'];

    //debugger($users);

    for ($i=0; $i < count($users['users']); $i++) { 
        

      echo '<tr>';
      echo '<td><a href="/profile/'.$users['users'][$i]['user_id'].'">'.$users['users'][$i]['user_name'].'</a></td>';
      echo '<td>'.date('Y M D H:m', $users['users'][$i]['user_registration_date']).'</td>';

      if ($users['allowEditing'] == 1) {
        echo '<td><a href="/profile/edit/'.$users['users'][$i]['user_id'].'">Редактировать</a></td>';
      } else {
        echo '<td></td>';
      }

      if ($users['allowRemoving'] == 1) {
        echo '<td><a href="/profile">Заблокировать</a></td>';
      } else {
        echo '<td></td>';
      }

      if ($users['users'][$i]['user_activated'] == 1) {
        echo '<td style="background-color: #c4f9a6;">Активирован</td>';
      } else {
        echo '<td style="background-color: #f9b5a6;">Не Активирован</td>';
      }
      echo '</tr>';
    }


    /*
    //$users = array('user1' => array('name' => 'skjdfjksf', 'regdate' => 'ksjdksdfjkh'));

    foreach($users as $key => $value) {

      echo '<tr>';
      echo '<td><a href="/profile/848484">'.$value['name'].'</a></td>';
      echo '<td>'.$value['regdate'].'</td>';
      echo '<td><button>Редактировать</button></td>';
      echo '<td><button>Заблокировать</button></td>';
       echo '<td style="background-color: #c4f9a6;">Активирован</td>';
      echo '</tr>';
    }
    */

  ?>

</table>

  <p><br /><br /></p>
</div>
</div>