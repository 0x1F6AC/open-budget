<div class="content content-fixed">
      <div class="container pd-x-0 pd-lg-x-10 pd-xl-x-0">
        <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-30">
          <div>
            <h4 class="mg-b-0 tx-spacing--1">To'lovlar</h4>
          </div>
          <div class="d-md-block">
            <select class="form-select" onchange="return window.location.href = '<?php echo base_url();?>payments/'+this.value" >
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
                        'url' => base_url('ajax/payments/list/'.$bot_id),
                        'cache' => false
                    ],
                    'columns' => [
                        ['title' => 'Foydalanuvchi', 'data' => 'chat_id'],
                        ['title' => 'Vaqt', 'data' => 'time'],
                        ['title' => 'Bot', 'data' => 'bot_id'],
                        ['title' => 'Holat', 'data' => 'status'],
                        ['title' => 'To\'lov ma\'lumoti', 'data' => 'data'],
                        ['title' => 'To\'lov summasi', 'data' => 'balance'],
                        ['title' => 'Harakat', 'data' => 'action']
                    ],
                    'columnDefs' => [
                        ['className' => 'text-center', 'targets' => [1, 2, 3, 4, 5, 6]],
                        ['orderable' => false, 'targets' => [5,6]]
                    ],
                    'order' => [
                        [1, "desc"]
                    ]
                  ];
                  echo dtable_gen('payments', $attr, $setings);
              ?>
          </div><!-- col -->
        </div><!-- row -->
      </div><!-- container -->
    </div><!-- content -->

    <div class="modal fade" id="paymentdata" tabindex="-1" role="dialog" aria-labelledby="paymentData" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content tx-14">
          <div class="modal-header">
            <h6 class="modal-title" id="paymentData">Statistika</h6>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
              <span aria-hidden="true"></span>
            </button>
          </div>
          <div class="modal-body"></div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary tx-13" data-bs-dismiss="modal">Yopish</button>
          </div>
        </div>
      </div>
    </div>