<div class="content content-fixed">
      <div class="container pd-x-0 pd-lg-x-10 pd-xl-x-0">
        <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-30">
          <div>
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb breadcrumb-style1 mg-b-10">
                <li class="breadcrumb-item">Telegram botlar</li>
                <li class="breadcrumb-item active" aria-current="page">Tahrirlash</li>
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
              <form action="" method="POST">
                <div class="row">
                  <div class="col-lg-12">
                    <div class="form-group">
                      <label for="formGroupExampleInput" class="d-block">Tashabbus</label>
                      <input type="text" class="form-control" placeholder="Tashabbus havolasini kiriting..." name="board" value="<?php echo $board;?>" <?php if( $this->session->userdata('user_level') != '1' ) echo "disabled";?>>
                    </div>
                  </div>
                </div>
                <hr>
                <div class="row">
                  <div class="col-lg-3">
                    <div class="form-group">
                      <label for="formGroupExampleInput" class="d-block">Ovozlar limiti</label>
                      <input type="number" min="0" class="form-control" placeholder="" name="voice_limit" value="<?php echo $voice_limit;?>">
                    </div>
                  </div>
                  <div class="col-lg-3">
                    <div class="form-group">
                      <label for="formGroupExampleInput" class="d-block">Ovoz summasi</label>
                      <input type="number" min="0" class="form-control" placeholder="" name="voice_price" value="<?php echo $voice_price;?>">
                    </div>
                  </div>
                  <div class="col-lg-3">
                    <div class="form-group">
                      <label for="formGroupExampleInput" class="d-block">Referal summasi</label>
                      <input type="number" min="0" class="form-control" placeholder="" name="ref_price" value="<?php echo $ref_price;?>">
                    </div>
                  </div>
                  <div class="col-lg-3">
                    <div class="form-group">
                      <label for="formGroupExampleInput" class="d-block">Minimal to'lov</label>
                      <input type="number" min="0" class="form-control" placeholder="" name="min_payment" value="<?php echo $min_payment;?>">
                    </div>
                  </div>
                </div>
                <hr>
                <div class="row">
                  <div class="col-lg-3">
                    <div class="form-group">
                      <label>Referal turi</label>
                      <select class="form-select" name="ref_mode">
                        <option value="0" <?php if( $ref_mode == '0' ) echo "selected";?>>Passiv</option>
                        <option value="1" <?php if( $ref_mode == '1' ) echo "selected";?>>Aktiv</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-lg-3">
                    <div class="form-group">
                      <label>Majburiy obuna</label>
                      <select class="form-select" name="mandatory_subscription">
                        <option value="0" <?php if( $mandatory_subscription == '0' ) echo "selected";?>>Yo'q</option>
                        <option value="1" <?php if( $mandatory_subscription == '1' ) echo "selected";?>>Ha</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-lg-3">
                    <div class="form-group">
                      <label for="formGroupExampleInput" class="d-block">Majburiy obuna havolasi</label>
                      <input type="text" class="form-control" placeholder="Kanal yoki guruh havolasini kiriting..." name="mandatory_link" value="<?php echo $mandatory_link;?>">
                    </div>
                  </div>
                  <div class="col-lg-3">
                    <div class="form-group">
                      <label for="formGroupExampleInput" class="d-block">Kanal ID raqami</label>
                      <input type="text" class="form-control" placeholder="Kanal yoki guruh id raqamini kiriting..." name="mandatory_chatid" value="<?php echo $mandatory_chatid;?>">
                    </div>
                  </div>
                </div>
                <button class="btn btn-primary btn-block" type="submit">Saqlash</button>
              </form>
          </div><!-- col -->
        </div><!-- row -->
      </div><!-- container -->
    </div><!-- content -->