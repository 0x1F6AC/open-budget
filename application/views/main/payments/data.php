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

  $user = $this->db->get_where('users', [
    'bot_id' => $bot_id,
    'chat_id' => $chat_id
  ]);

  if ( $user->num_rows() > 0 ) {
    $balance = $user->row()->balance;
  }else{
    $balance = 0;
  }
?>
<table class="table">
  <tbody>
    <tr>
      <td>Ovozlar</td>
      <td class="text-right"><?php echo number_format($voices, 0, ',', ' ');?></td>
    </tr>
    <tr>
      <td>Referallar</td>
      <td class="text-right"><?php echo number_format($referrers, 0, ',', ' ');?></td>
    </tr>
    <tr>
      <td>To'lov ma'lumoti</td>
      <td class="text-right"><?php echo $data;?></td>
    </tr>
    <tr>
      <td>Balans</td>
      <td class="text-right"><?php echo number_format($balance, 0, ',', ' ');?></td>
    </tr>
  </tbody>
</table>