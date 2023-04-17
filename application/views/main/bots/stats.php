<?php
  $users = $this->db
    ->where('bot_id', $bot_id)
    ->from("users")
  ->count_all_results();
  $voices = $this->db
    ->where('bot_id', $bot_id)
    ->where('status', '1')
    ->from("voices")
  ->count_all_results();
  $referrers = $this->db
    ->where('bot_id', $bot_id)
    ->from("referals")
  ->count_all_results();
?>
<table class="table">
  <tbody>
    <tr>
      <td>Foydalanuvchilar</td>
      <td class="text-right"><?php echo number_format($users, 0, ',', ' ');?></td>
    </tr>
    <tr>
      <td>Ovozlar</td>
      <td class="text-right"><?php echo number_format($voices, 0, ',', ' ');?> / <?php echo number_format($voice_limit, 0, ',', ' ');?></td>
    </tr>
    <tr>
      <td>Referallar</td>
      <td class="text-right"><?php echo number_format($referrers, 0, ',', ' ');?></td>
    </tr>
    <tr>
      <td>Ovoz summasi</td>
      <td class="text-right"><?php echo number_format($voice_price, 0, ',', ' ');?></td>
    </tr>
    <tr>
      <td>Referal summasi</td>
      <td class="text-right"><?php echo number_format($ref_price, 0, ',', ' ');?></td>
    </tr>
    <tr>
      <td>Minimal to'lov</td>
      <td class="text-right"><?php echo number_format($min_payment, 0, ',', ' ');?></td>
    </tr>
    <tr>
      <td>Bog'langan kanal/guruh</td>
      <td class="text-right"><?php echo !empty( $mandatory_link ) ? $mandatory_link : 'Kiritilmagan';?></td>
    </tr>
  </tbody>
</table>