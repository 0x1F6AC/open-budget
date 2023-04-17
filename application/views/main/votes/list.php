<div class="content content-fixed">
      <div class="container pd-x-0 pd-lg-x-10 pd-xl-x-0">
        <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-30">
          <div>
            <h4 class="mg-b-0 tx-spacing--1">Ovozlar</h4>
          </div>
          <div class="d-md-block">
            <a class="btn btn-sm pd-x-15 btn-warning btn-uppercase" href="#export_modal" data-bs-toggle="modal"><i data-feather="file-text" class="wd-10"></i></a>
            <a class="btn btn-sm pd-x-15 btn-secondary btn-uppercase" href="#filter_modal" data-bs-toggle="modal"><i data-feather="filter" class="wd-10"></i></a>
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
                        'url' => base_url('ajax/votes/'.$bot_id),
                        'cache' => false
                    ],
                    'columns' => [
                        ['title' => 'Foydalanuvchi', 'data' => 'chat_id'],
                        ['title' => 'Tashabbus', 'data' => 'board'],
                        ['title' => 'Phone', 'data' => 'phone'],
                        ['title' => 'Vaqt', 'data' => 'time'],
                        ['title' => 'Bot', 'data' => 'bot_id']
                    ],
                    'columnDefs' => [
                        ['className' => 'text-center', 'targets' => [1, 2, 3, 4]]
                    ],
                    'order' => [
                        [3, "desc"]
                    ]
                  ];
                  echo dtable_gen('votes', $attr, $setings);
              ?>
          </div><!-- col -->
        </div><!-- row -->
      </div><!-- container -->
    </div><!-- content -->

    <div class="modal fade" id="export_modal" tabindex="-1" role="dialog" aria-labelledby="trashModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content tx-14">
          <div class="modal-header">
            <h6 class="modal-title" id="trashModalLabel">Eksport qilish</h6>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
              <span aria-hidden="true"></span>
            </button>
          </div>
          <div class="modal-body">
            <form action="" method="POST" class="export-votes">
                <div class="row">
                  <div class="col-lg-12">
                    <div class="form-group">
                      <label>Botlar</label>
                      <select class="form-control export-select2" multiple="multiple" name="bots[]">
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
                    <div class="row">
                      <div class="col-lg-6">
                        <div class="form-group">
                          <label>Boshlanish vaqti</label>
                          <input class="form-control" type="date" name="start" value="<?php echo date('Y-m-d');?>" />
                        </div>
                      </div>
                      <div class="col-lg-6">
                        <div class="form-group">
                          <label>Tugash vaqti</label>
                          <input class="form-control" type="date" name="end" value="<?php echo date('Y-m-d');?>" />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-warning tx-13 export-votes-button">Yuklash</button>
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
            <select class="form-select" onchange="return window.location.href = '<?php echo base_url();?>votes/'+this.value" >
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

    <div class="d-none" id="export_data"></div>