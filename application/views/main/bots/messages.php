<div class="content content-fixed">
      <div class="container pd-x-0 pd-lg-x-10 pd-xl-x-0">
        <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-30">
          <div>
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb breadcrumb-style1 mg-b-10">
                <li class="breadcrumb-item">Telegram botlar</li>
                <li class="breadcrumb-item active" aria-current="page">Xabarlarni sozlash</li>
              </ol>
            </nav>
            <h4 class="mg-b-0 tx-spacing--1">Telegram botlarni boshqarish</h4>
          </div>
          <div class="d-md-block">
            <a class="btn btn-sm pd-x-15 btn-primary btn-uppercase mg-l-5" href="<?php echo base_url('bots');?>"><i data-feather="chevron-left" class="wd-10 mg-r-5"></i> orqaga</a>
          </div>
        </div>

        <div class="row g-2">
          <?php
            if (!empty( $errors )) {
          ?>
          <div class="col-lg-12">
            <div class="alert alert-danger" role="alert"><?php echo $errors;?></div>
          </div>
          <?php
            }
          ?>
          <div class="col-lg-12">
              <form action="" method="POST" class="mg-b-30">
                <div class="row">
                  <?php
                    foreach (config_item('message_keys') as $key => $value) {
                  ?>
                  <div class="col-lg-4">
                    <div class="form-group">
                      <div class="input-group">
                        <?php
                          if (!empty( $value )) {
                            $popover = "";
                            foreach ($value as $k => $v) {
                              $popover .= "<em>{{$k}}</em> - $v<br/>";
                            }
                        ?>
                          <span class="input-group-text bg-warning cursor-pointer" data-bs-container="body" data-bs-toggle="popover" data-bs-placement="top" data-html="true" data-bs-content="<?php echo $popover;?>"><i class="fas fa-lightbulb"></i></span>
                        <?php
                          }
                        ?>
                        <textarea class="form-control no-resize" rows="3" name="<?php echo $key;?>" <?php if($this->session->userdata('user_level') != '1' && in_array($key, ['about_button_message'])) echo "readonly";?>><?php echo bot_message($key, $bot_id);?></textarea>
                      </div>
                    </div>
                  </div>
                  <?php
                    }
                  ?>
                </div>
                <button class="btn btn-primary btn-block" type="submit">Saqlash</button>
              </form>
          </div><!-- col -->
        </div><!-- row -->
      </div><!-- container -->
    </div><!-- content -->