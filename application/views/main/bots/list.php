<div class="content content-fixed">
      <div class="container pd-x-0 pd-lg-x-10 pd-xl-x-0">
        <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-30">
          <div>
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb breadcrumb-style1 mg-b-10">
                <li class="breadcrumb-item">Telegram botlar</li>
                <li class="breadcrumb-item active" aria-current="page">Ro'yxat</li>
              </ol>
            </nav>
            <h4 class="mg-b-0 tx-spacing--1">Telegram botlarni boshqarish</h4>
          </div>
          <?php
            if( $this->session->userdata('user_level') == '1' ){
          ?>
          <div class="d-md-block">
            <a class="btn btn-sm pd-x-15 btn-primary btn-uppercase mg-l-5" href="<?php echo base_url('bots/add');?>"><i data-feather="plus" class="wd-10 mg-r-5"></i> Qo'shish</a>
          </div>
          <?php
            }
          ?>
        </div>

        <div class="row g-2 mg-b-3">
          <div class="col-lg-12">
              <?php
                  $attr = [
                      'class' => 'table table-striped m-0 table-actions-bar  nowrapdt-head-right',
                      'style' => 'width: 100%',
                  ];
                  if( $this->session->userdata('user_level') == '1' ){
                    $setings = [
                      'processing' => true,
                      'serverSide' => true,
                      'responsive' => true,
                      'serverMethod' => 'post',
                      'ajax' => [
                          'url' => base_url('ajax/bots/list'),
                          'cache' => false
                      ],
                      'columns' => [
                          ['title' => 'Nomi', 'data' => 'name'],
                          ['title' => 'Chat ID', 'data' => 'bot_id'],
                          ['title' => 'Havola', 'data' => 'username'],
                          ['title' => 'Limit', 'data' => 'voice_limit'],
                          ['title' => 'Referal', 'data' => 'ref_mode'],
                          ['title' => 'Obuna', 'data' => 'mandatory_subscription'],
                          ['title' => 'Holat', 'data' => 'status'],
                          ['title' => 'Harakat', 'data' => 'action']
                      ],
                      'columnDefs' => [
                          ['className' => 'text-center', 'targets' => [1, 2, 3, 4, 5, 6]],
                          ['orderable' => false, 'targets' => [7]]
                      ],
                      'order' => [
                          [0, "desc"]
                      ]
                    ];
                  }else{
                    $setings = [
                      'processing' => true,
                      'serverSide' => true,
                      'responsive' => true,
                      'serverMethod' => 'post',
                      'ajax' => [
                          'url' => base_url('ajax/bots/list'),
                          'cache' => false
                      ],
                      'columns' => [
                          ['title' => 'Chat ID', 'data' => 'bot_id'],
                          ['title' => 'Havola', 'data' => 'username'],
                          ['title' => 'Limit', 'data' => 'voice_limit'],
                          ['title' => 'Referal', 'data' => 'ref_mode'],
                          ['title' => 'Obuna', 'data' => 'mandatory_subscription'],
                          ['title' => 'Holat', 'data' => 'status'],
                          ['title' => 'Harakat', 'data' => 'action']
                      ],
                      'columnDefs' => [
                          ['className' => 'text-center', 'targets' => [1, 2, 3, 4, 5, 6]],
                          ['orderable' => false, 'targets' => [6]]
                      ],
                      'order' => [
                          [0, "desc"]
                      ]
                    ];
                  }
                  echo dtable_gen('bots', $attr, $setings);
              ?>
          </div><!-- col -->
        </div><!-- row -->
      </div><!-- container -->
    </div><!-- content -->

    <div class="modal fade" id="botstats" tabindex="-1" role="dialog" aria-labelledby="botStats" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content tx-14">
          <div class="modal-header">
            <h6 class="modal-title" id="botStats">Statistika</h6>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
              <span aria-hidden="true"></span>
            </button>
          </div>
          <div class="modal-body"></div>
          <div class="modal-footer">
            <?php
              if( $this->session->userdata('user_level') == '1' ){
            ?>
              <button type="button" class="btn btn-warning tx-13" data-reload-bot=""><i class="fas fa-sync"></i></button>
              <button type="button" class="btn btn-danger tx-13" data-pause-bot=""><i class="fas fa-pause"></i></button>
            <?php
              }
            ?>
            <button type="button" class="btn btn-secondary tx-13" data-bs-dismiss="modal">Yopish</button>
          </div>
        </div>
      </div>
    </div>