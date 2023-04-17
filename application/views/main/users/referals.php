<table class="table">
  <thead>
    <tr>
      <th scope="col">Ism</th>
      <th scope="col">Havola</th>
      <th scope="col">Vaqt</th>
    </tr>
  </thead>
  <tbody>
    <?php
      foreach ($referals as $referal) {
        $user = $this->db->get_where('users', [
          'chat_id' => $referal['chat_id'],
          'bot_id' => $referal['bot_id'],
        ]);
        if ( $user->num_rows() == 0 ) continue;
        $user = $user->row_array();
    ?>
    <tr>
      <?php
        if (empty( $user['username'] )) {
          $user['username'] = '<span class="badge text-bg-danger">Kiritilmagan</span>';
        }else{
          $user['username'] = '@'.$user['username'];
        }

        $user['name'] = $user['firstname'] . ' ' . $user['lastname'];  
        if (empty( $user['name'] )) {
          $user['name'] = '<span class="badge text-bg-danger">Kiritilmagan</span>';
        }
      ?>
      <td><?php echo $user['name'];?></td>
      <td><?php echo $user['username'];?></td>
      <td><?php echo date('Y-m-d | H:i:s', $referal['time']);?></td>
    </tr>
    <?php
      }
    ?>
  </tbody>
</table>