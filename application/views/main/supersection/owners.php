<div class="content content-fixed">
      <div class="container pd-x-0 pd-lg-x-10 pd-xl-x-0">
        <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-30">
          <div>
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb breadcrumb-style1 mg-b-10">
                <li class="breadcrumb-item">Super bo'lim</li>
                <li class="breadcrumb-item active" aria-current="page">Mijozlar</li>
              </ol>
            </nav>
            <h4 class="mg-b-0 tx-spacing--1">Mijozlarni boshqarish</h4>
          </div>
          <div class="d-md-block">
            <a class="btn btn-sm pd-x-15 btn-primary btn-uppercase mg-l-5" href="<?php echo base_url('supersection/owners/add');?>"><i data-feather="plus" class="wd-10 mg-r-5"></i> Qo'shish</a>
          </div>
        </div>

        <div class="row g-2 mg-b-3">
          <div class="col-lg-12">
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
                          'url' => base_url('ajax/supersection/owners'),
                          'cache' => false
                      ],
                      'columns' => [
                          ['title' => 'Ism', 'data' => 'name'],
                          ['title' => 'Telegram id', 'data' => 'chat_id'],
                          ['title' => 'So\'nggi tashrif', 'data' => 'lastlogged'],
                          ['title' => 'Holat', 'data' => 'status'],
                          ['title' => 'Daraja', 'data' => 'level'],
                          ['title' => 'Harakat', 'data' => 'action']
                      ],
                      'columnDefs' => [
                          ['className' => 'text-center', 'targets' => [1, 2, 3, 4, 5]],
                          ['orderable' => false, 'targets' => [5]]
                      ],
                      'order' => [
                          [0, "desc"]
                      ]
                  ];
                  echo dtable_gen('news', $attr, $setings);
              ?>
          </div><!-- col -->
        </div><!-- row -->
      </div><!-- container -->
    </div><!-- content -->