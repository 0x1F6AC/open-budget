<div class="content content-fixed">
      <div class="container pd-x-0 pd-lg-x-10 pd-xl-x-0">
        <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-30">
          <div>
            <h4 class="mg-b-0 tx-spacing--1">Referallar</h4>
          </div>
          <div class="d-md-block">
            <select class="form-select" onchange="return window.location.href = '<?php echo base_url();?>referrers/'+this.value" >
              <option value="list">Barchasi</option>
              <?php
                if( $this->session->userdata('user_level') == '1' ){
                  $bots = $this->db->get('bots');
                  if ( $bots->num_rows() > 0) {
                    foreach ($bots->result_array() as $bot) {
                      $selected = $bot_id == $bot['bot_id'] ? ' selected' : '';
                      echo "<option value=\"{$bot['bot_id']}\"{$selected}>{$bot['username']}</option>";
                    }
                  }
                }else{
                  $user_bots = explode(',', $this->session->userdata('user_bots'));
                  if ( !empty( $user_bots ) ) {
                    foreach ($user_bots as $bot) {
                      $bot_data = $this->db->get_where('bots', [
                        'bot_id' => $bot,
                        'status' => '1',
                      ]);
                      if ( $bot_data->num_rows() == 0 ) continue;
                      $bot_data = $bot_data->row_array();
                      $selected = $bot_id == $bot_data['bot_id'] ? ' selected' : '';
                      echo "<option value=\"{$bot_data['bot_id']}{$selected}\">{$bot_data['username']}</option>";
                    }
                  }
                }
              ?>
            </select>
          </div>
        </div>

        <div class="row g-2 mg-b-40">
          <div class="col-lg-12 ">
              <?php
                  $attr = [
                      'class' => 'table table-striped m-0 table-actions-bar  nowrapdt-head-right',
                      'style' => 'width: 100%',
                  ];
                  $setings = [
                    'processing' => true,
                    'serverSide' => true,
                    'responsive' => true,
                    'serverMethod' => 'post',
                    'ajax' => [
                        'url' => base_url('ajax/referrers/'.$bot_id),
                        'cache' => false
                    ],
                    'columns' => [
                        ['title' => 'Bot', 'data' => 'bot_id'],
                        ['title' => 'Foydalanuvchi', 'data' => 'chat_id'],
                        ['title' => 'Referal', 'data' => 'owner_id'],
                        ['title' => 'Vaqt', 'data' => 'time']
                    ],
                    'columnDefs' => [
                        ['className' => 'text-center', 'targets' => [1, 2, 3]]
                    ],
                    'order' => [
                        [3, "desc"]
                    ]
                  ];
                  echo dtable_gen('referrers', $attr, $setings);
              ?>
          </div><!-- col -->
        </div><!-- row -->
      </div><!-- container -->
    </div><!-- content -->