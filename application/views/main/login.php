<!DOCTYPE html>
<html lang="en">
  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Openbudget BOT | Tizimga kirish</title>

    <link href="<?php echo base_url('lib/@fortawesome/fontawesome-free/css/all.min.css');?>" rel="stylesheet">
    <link href="<?php echo base_url('lib/remixicon/fonts/remixicon.css');?>" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo base_url('lib/growl/jquery.growl.css');?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/dashforge.css');?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/dashforge.auth.css');?>">
  </head>
  <body>

    <div class="content content-fixed content-auth-alt">
      <div class="container ht-100p tx-center">
        <div class="ht-100p d-flex flex-column align-items-center justify-content-center">
          <div class="wd-70p wd-sm-250 wd-lg-300 mg-b-15"><img src="<?php echo base_url('assets/img/img15.png');?>" class="img-fluid" alt=""></div>
          <h1 class="tx-color-01 tx-24 tx-sm-32 tx-lg-36 mg-xl-b-5">Tizimga kirish</h1>
          <p class="tx-color-03 mg-b-30">Iltimos, tizimdan to'laqonli foydalanish uchun<br /> telegram orqali avtorizatsiyadan o'ting.</p>
          <div class="d-flex mg-b-40">
            <button class="btn btn-brand-02 bd-0 mg-l-5 pd-sm-x-25" id="login">Telegram orqali kirish</button>
            <style type="text/css">
                iframe{
                    display: none;
                }
            </style>
            <script async src="https://telegram.org/js/telegram-widget.js?19" data-size="small" data-userpic="false" data-request-access="write"></script>
          </div>
        </div>
      </div><!-- container -->
    </div><!-- content -->

    <script src="<?php echo base_url('lib/jquery/jquery.min.js');?>"></script>
    <script src="<?php echo base_url('lib/bootstrap/js/bootstrap.bundle.min.js');?>"></script>
    <script src="<?php echo base_url('lib/growl/jquery.growl.js');?>"></script>
    <script src="<?php echo base_url('lib/feather-icons/feather.min.js');?>"></script>
    <script src="<?php echo base_url('lib/perfect-scrollbar/perfect-scrollbar.min.js');?>"></script>

    <script src="<?php echo base_url('assets/js/dashforge.js');?>"></script>

    <!-- append theme customizer -->
    <script src="<?php echo base_url('lib/js-cookie/js.cookie.js');?>"></script>
    <script>
      $(function(){
        'use script'

        window.darkMode = function(){
          $('.btn-white').addClass('btn-dark').removeClass('btn-white');
        }

        window.lightMode = function() {
          $('.btn-dark').addClass('btn-white').removeClass('btn-dark');
        }

        var hasMode = Cookies.get('df-mode');
        if(hasMode === 'dark') {
          darkMode();
        } else {
          lightMode();
        }
      })
    </script>

    <?php
    	$token = setting_item('bot_token');
    	$bot_id = explode(':', $token);
    	$bot_id = ( !empty($bot_id[0]) ) ? $bot_id[0] : '';
    ?>

    <script type="text/javascript">
		setTimeout(()=>{
            document.getElementById('login').addEventListener("click", () => {
              window.Telegram.Login.auth(
                    { bot_id: '<?php echo $bot_id;?>', request_access: true }, (data) => {
                        if (!data){
                        	$.growl.error({ title:"", message: "Tizimga kirishda xatolik!", location: "tc" });
                        }else{
                        	fetch("<?php echo base_url('login')?>", {
								method: "POST",
								headers: {
    								"Content-Type": "application/x-www-form-urlencoded"
  								},
							  	body: new URLSearchParams(data)
							}).then((response) => response.json()).then( (res) => { 
							   if (res.status == 'ok') {
							   		location.reload();
							   }else if (res.status == 'error') {
							   		$.growl.error({ title:"", message: res.message, location: "tc" });
							   }else{
							   		$.growl.error({ title:"", message: "Tizimga ulanishda xatolik!", location: "tc" });
							   }
							});
                        }
                    }
                );
            });
        }, 100);
    </script>
  </body>
</html>