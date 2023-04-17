<div class="content content-fixed">
      <div class="container pd-x-0 pd-lg-x-10 pd-xl-x-0">
        <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-30">
          <div>
            <h4 class="mg-b-0 tx-spacing--1">Bildirishnomalar</h4>
          </div>
          <div class="d-md-block">
            <a class="btn btn-sm pd-x-15 btn-primary btn-uppercase" href="#send_modal" data-bs-toggle="modal"><i data-feather="send" class="wd-10"></i></a>
            <a class="btn btn-sm pd-x-15 btn-secondary btn-uppercase" href="#filter_modal" data-bs-toggle="modal"><i data-feather="filter" class="wd-10"></i></a>
            <a class="btn btn-sm pd-x-15 btn-danger btn-uppercase" href="#trash_modal" data-bs-toggle="modal"><i data-feather="trash-2" class="wd-10"></i></a>
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
                        'url' => base_url('ajax/notifications/list/'.$bot_id),
                        'cache' => false
                    ],
                    'columns' => [
                        ['title' => 'Foydalanuvchi', 'data' => 'user'],
                        ['title' => 'Xabar', 'data' => 'message'],
                        ['title' => 'Vaqt', 'data' => 'time'],
                        ['title' => 'Bot', 'data' => 'bot']
                    ],
                    'columnDefs' => [
                        ['className' => 'text-center', 'targets' => [1, 2, 3]],
                        ['orderable' => false, 'targets' => [0,1,2,3]]
                    ]
                  ];
                  echo dtable_gen('notifications', $attr, $setings);
              ?>
          </div><!-- col -->
        </div><!-- row -->
      </div><!-- container -->
    </div><!-- content -->

    <div class="modal fade" id="trash_modal" tabindex="-1" role="dialog" aria-labelledby="trashModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content tx-14">
          <div class="modal-header">
            <h6 class="modal-title" id="trashModalLabel">Tozalash</h6>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
              <span aria-hidden="true"></span>
            </button>
          </div>
          <div class="modal-body">
            <form action="" method="POST" class="trash-notifications">
                <div class="row">
                  <div class="col-lg-12">
                    <div class="form-group">
                      <label>Botlar</label>
                      <select class="form-control notifications-trash-select2" multiple="multiple" name="bots[]">
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
                </div>
              </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger tx-13 trash-notifications-button">Tozalash</button>
            <button type="button" class="btn btn-secondary tx-13" data-bs-dismiss="modal">Yopish</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="filter_modal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content tx-14">
          <div class="modal-header">
            <h6 class="modal-title" id="filterModalLabel">Filtrlash</h6>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
              <span aria-hidden="true"></span>
            </button>
          </div>
          <div class="modal-body">
            <select class="form-select" onchange="return window.location.href = '<?php echo base_url();?>notifications/'+this.value" >
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
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary tx-13" data-bs-dismiss="modal">Yopish</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="send_modal" tabindex="-1" role="dialog" aria-labelledby="sendModalLabel2" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content tx-14">
          <div class="modal-header">
            <h6 class="modal-title" id="sendModalLabel2">Xabar yo'llash</h6>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
              <span aria-hidden="true"></span>
            </button>
          </div>
          <div class="modal-body">
            <form action="" method="POST" class="send-notifications">
                <div class="row">
                  <div class="col-lg-12">
                    <div class="form-group">
                      <label>Botlar</label>
                      <select class="form-control notifications-select2" multiple="multiple" name="bots[]">
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
                  <div class="col-lg-12">
                    <div class="form-group">
                      <label>Xabar matni</label>
                      <textarea class="form-control no-resize" rows="2" name="message"></textarea>
                    </div>
                  </div>
                </div>
              </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary tx-13 send-notifications-button">Yuborish</button>
            <button type="button" class="btn btn-secondary tx-13" data-bs-dismiss="modal">Yopish</button>
          </div>
        </div>
      </div>
    </div>