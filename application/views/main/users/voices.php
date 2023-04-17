<table class="table">
  <thead>
    <tr>
      <th scope="col">Raqam</th>
      <th scope="col">Bot</th>
      <th scope="col">Tashabbus</th>
      <th scope="col">Vaqt</th>
    </tr>
  </thead>
  <tbody>
    <?php
      foreach ($voices as $voice) {
        $bot = $this->db->get_where('bots', [
          'bot_id' => $voice['bot_id'],
          'status' => '1',
        ]);
        if ( $bot->num_rows() == 0 ) continue;
        $bot = $bot->row_array();
    ?>
    <tr>
      <td><?php echo format_phone('998'.$voice['phone'], ( $this->session->userdata('user_level') == '1' ));?></td>
      <td><?php echo $bot['username'];?></td>
      <td><a href="<?php echo $voice['board'];?>" target="_blank"><span class="badge text-bg-secondary">Ko'rish</span></a></td>
      <td><?php echo date('m-d | H:i:s', $voice['time']);?></td>
    </tr>
    <?php
      }
    ?>
  </tbody>
</table>