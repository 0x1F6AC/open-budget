<?php
  $voices = $this->db
    ->where('bot_id', $bot_id)
    ->where('chat_id', $chat_id)
    ->where('status', '1')
    ->from("voices")
  ->count_all_results();
  $referrers = $this->db
    ->where('bot_id', $bot_id)
    ->where('owner_id', $chat_id)
    ->where('status', '1')
    ->from("referals")
  ->count_all_results();
?>
<table class="table">
  <tbody>
    <tr>
      <td>Ism</td>
      <?php
        $name = $firstname . ' ' . $lastname;  
        if (empty( $name )) {
          $name = '<span class="badge text-bg-danger">Kiritilmagan</span>';
        }
      ?>
      <td class="text-right"><?php echo $name;?></td>
    </tr>
    <tr>
      <td>Chat ID</td>
      <td class="text-right"><?php echo $chat_id;?></td>
    </tr>
    <tr>
      <td>Havola</td>
      <?php
        if (empty( $username )) {
          $username = '<span class="badge text-bg-danger">Kiritilmagan</span>';
        }else{
          $username = '@'.$username;
        }
      ?>
      <td class="text-right"><?php echo $username;?></td>
    </tr>
    <tr>
      <td>Ro'yxatdan o'tgan</td>
      <td class="text-right"><?php echo date('Y-m-d | H:i:s', $registered);?></td>
    </tr>
    <tr>
      <td>Oxirgi aktivlik</td>
      <td class="text-right"><?php echo date('Y-m-d | H:i:s', $lastaction);?></td>
    </tr>
    <tr>
      <td>Balans</td>
      <td class="text-right"><?php echo number_format($balance, 0, ',', ' ');?></td>
    </tr>
    <tr>
      <td>Ovozlar</td>
      <td class="text-right"><?php echo number_format($voices, 0, ',', ' ');?></td>
    </tr>
    <tr>
      <td>Referallar</td>
      <td class="text-right"><?php echo number_format($referrers, 0, ',', ' ');?></td>
    </tr>
  </tbody>
</table>