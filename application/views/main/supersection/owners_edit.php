<div class="content content-fixed">
      <div class="container pd-x-0 pd-lg-x-10 pd-xl-x-0">
        <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-30">
          <div>
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb breadcrumb-style1 mg-b-10">
                <li class="breadcrumb-item">Super bo'lim</li>
                <li class="breadcrumb-item">Mijozlar</li>
                <li class="breadcrumb-item active" aria-current="page">Tahrirlash</li>
              </ol>
            </nav>
            <h4 class="mg-b-0 tx-spacing--1">Mijozlarni boshqarish</h4>
          </div>
          <div class="d-md-block">
            <a class="btn btn-sm pd-x-15 btn-primary btn-uppercase mg-l-5" href="<?php echo base_url('supersection/owners');?>"><i data-feather="chevron-left" class="wd-10 mg-r-5"></i> orqaga</a>
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
                  <div class="col-lg-6">
                    <div class="form-group">
                      <label for="formGroupExampleInput" class="d-block">Ism</label>
                      <input type="text" class="form-control" placeholder="Mijoz ismini kiriting..." name="name" value="<?php echo $name;?>">
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form-group">
                      <label for="formGroupExampleInput" class="d-block">Telegram ID</label>
                      <input type="text" class="form-control" placeholder="Teelgram indenfikatorini kiriting..." name="chat_id" value="<?php echo $chat_id;?>">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-6">
                    <div class="form-group">
                      <label>Holat</label>
                      <select class="form-select" name="status">
                        <option value="1" <?php if( $status == 1 ) echo "selected";?>>Faol</option>
                        <option value="0" <?php if( $status == 0 ) echo "selected";?>>Faol emas</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form-group">
                      <label>Draja</label>
                      <select class="form-select" name="level">
                        <option value="0" <?php if( $level == 0 ) echo "selected";?>>Mijoz</option>
                        <option value="2" <?php if( $level == 2 ) echo "selected";?>>Kuzatuvchi</option>
                        <option value="1" <?php if( $level == 1 ) echo "selected";?>>Superadmin</option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-12">
                    <div class="form-group">
                      <label>Botlar</label>
                      <select class="form-control select2" multiple="multiple" name="bots[]">
                        <?php
                          $bots_list = $this->db->get_where('bots', ['status' => '1']);
                          if ( $bots_list->num_rows() > 0 ) {
                            $selected_bots = explode(',', $bots);
                            foreach ($bots_list->result_array() as $bot) {
                              echo "<option value=\"{$bot['bot_id']}\"". ( in_array($bot['bot_id'], $selected_bots) ? "selected" : "" ) .">{$bot['name']}</option>";
                            }
                          }
                        ?>
                      </select>
                    </div>
                  </div>
                </div>
                <button class="btn btn-primary btn-block" type="submit">Saqlash</button>
              </form>
          </div><!-- col -->
        </div><!-- row -->
      </div><!-- container -->
    </div><!-- content -->