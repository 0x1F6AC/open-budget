<!DOCTYPE html>
<html lang="en">
  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Openbudget BOT | <?php echo $title;?></title>

    <link href="<?php echo base_url('lib/@fortawesome/fontawesome-free/css/all.min.css');?>" rel="stylesheet">
    <link href="<?php echo base_url('lib/remixicon/fonts/remixicon.css');?>" rel="stylesheet">
    <link href="<?php echo base_url('lib/jqvmap/jqvmap.min.css');?>" rel="stylesheet">

    <link href="<?php echo base_url('lib/select2/css/select2.min.css');?>" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/datatables/dataTables.bootstrap4.min.css');?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/datatables/buttons.bootstrap4.min.css');?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/datatables/responsive.bootstrap4.min.css');?>">

    <link rel="stylesheet" href="<?php echo base_url('lib/growl/jquery.growl.css');?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/dashforge.css?v=1.4');?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/dashforge.dashboard.css');?>">
    <link id="dfMode" rel="stylesheet" href="<?php echo base_url('assets/css/skin.cool.css');?>">
  </head>
  <body class="page-profile">

    <header class="navbar navbar-header navbar-header-fixed">
      <a href="" id="mainMenuOpen" class="burger-menu"><i data-feather="menu"></i></a>
      <div class="navbar-brand">
        <a href="<?php echo base_url();?>" class="df-logo">Openbudget&nbsp;<span>BOT</span></a>
      </div><!-- navbar-brand -->
      <div id="navbarMenu" class="navbar-menu-wrapper">
        <div class="navbar-menu-header">
          <a href="<?php echo base_url();?>" class="df-logo">Openbudget&nbsp;<span>BOT</span></a>
          <a id="mainMenuClose" href=""><i data-feather="x"></i></a>
        </div><!-- navbar-menu-header -->
        <ul class="nav navbar-menu">
          <li class="nav-label pd-l-20 pd-lg-l-25 d-lg-none">Menyu</li>
          <!-- <li class="nav-item">
            <a href="<?php echo base_url();?>" class="nav-link"><i data-feather="bar-chart-2"></i> Statistika</a>
          </li> -->
          <li class="nav-item">
            <a href="<?php echo base_url('votes');?>" class="nav-link"><i data-feather="check-circle"></i> Ovozlar</a>
          </li>
          <li class="nav-item">
            <a href="<?php echo base_url('payments');?>" class="nav-link"><i data-feather="credit-card"></i> To'lovlar</a>
          </li>
          <li class="nav-item">
            <a href="<?php echo base_url('referrers');?>" class="nav-link"><i data-feather="link"></i> Referallar</a>
          </li>
          <li class="nav-item">
            <a href="<?php echo base_url('users');?>" class="nav-link"><i data-feather="users"></i> Foydalanuvchilar</a>
          </li>
          <li class="nav-item">
            <a href="<?php echo base_url('notifications');?>" class="nav-link"><i data-feather="bell"></i> Bildirishnomalar</a>
          </li>
          <li class="nav-item">
            <a href="<?php echo base_url('bots');?>" class="nav-link"><i data-feather="box"></i> Telegram botlar</a>
          </li>
          <?php
            if( $this->session->userdata('user_level') == '1' ){
          ?>
          <li class="nav-item with-sub">
            <a href="" class="nav-link"><i data-feather="coffee"></i> Super bo'lim</a>
            <ul class="navbar-menu-sub">
              <li class="nav-sub-item"><a href="<?php echo base_url('supersection/owners');?>" class="nav-sub-link"><i data-feather="codesandbox"></i>Mijozlar</a></li>
              <li class="nav-sub-item"><a href="<?php echo base_url('supersection/messages');?>" class="nav-sub-link"><i data-feather="file-text"></i>Xabarlar</a></li>
             <li class="nav-sub-item"><a href="<?php echo base_url('supersection/settings');?>" class="nav-sub-link"><i data-feather="settings"></i>Sozlamalar</a></li>
            </ul>
          </li>
          <?php
            }
          ?>
        </ul>
      </div><!-- navbar-menu-wrapper -->
      <div class="navbar-right">
        <div class="dropdown dropdown-profile">
          <a href="" role="button" class="dropdown-link" data-bs-toggle="dropdown" data-bs-display="static">
            <div class="avatar avatar-sm"><img src="<?php echo $this->session->userdata('telegram_photo_url');?>" class="rounded-circle" alt=""></div>
          </a><!-- dropdown-link -->
          <div class="dropdown-menu dropdown-menu-end tx-13">
            <div class="avatar avatar-lg mg-b-15"><img src="<?php echo $this->session->userdata('telegram_photo_url');?>" class="rounded-circle" alt=""></div>
            <h6 class="tx-semibold mg-b-5"><?php echo $this->session->userdata('telegram_first_name');?> <?php echo $this->session->userdata('telegram_last_name');?></h6>
            <p class="mg-b-25 tx-12 tx-color-03">@<?php echo $this->session->userdata('telegram_username');?></p>
            <div class="dropdown-divider"></div>
            <a href="<?php echo base_url('logout');?>" class="dropdown-item"><i data-feather="log-out"></i>Chiqish</a>
          </div><!-- dropdown-menu -->
        </div><!-- dropdown -->
      </div><!-- navbar-right -->
    </header><!-- navbar -->

    <?php
      if ( !isset( $content_data ) ) {
        $content_data = [];
      }
      $this->load->view('main/'.$content, $content_data);
    ?>

    <footer class="footer fixed-bottom">
      <div>
        <span>&copy; <?php echo date('Y')?> - Openbudget BOT v2.1.0. </span>
      </div>
      <div>
        <nav class="nav">
          <a href="https://t.me/obudjetuz" target="_blank" class="nav-link">Telegram</a>
        </nav>
      </div>
    </footer>

    <script type="text/javascript">
      var base_url = "<?php echo base_url();?>";
    </script>

    <script src="<?php echo base_url('lib/jquery/jquery.min.js');?>"></script>
    <script src="<?php echo base_url('lib/bootstrap/js/bootstrap.bundle.min.js');?>"></script>
    <script src="<?php echo base_url('lib/feather-icons/feather.min.js');?>"></script>
    <script src="<?php echo base_url('lib/ionicons/ionicons/ionicons.esm.js');?>" type="module"></script>
    <script src="<?php echo base_url('lib/perfect-scrollbar/perfect-scrollbar.min.js');?>"></script>
    <script src="<?php echo base_url('lib/growl/jquery.growl.js');?>"></script>

    <script type="text/javascript" src="<?php echo base_url('assets/datatables/jquery.dataTables.min.js');?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/datatables/dataTables.bootstrap4.min.js');?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/datatables/dataTables.buttons.min.js');?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/datatables/buttons.bootstrap4.min.js');?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/datatables/jszip.min.js');?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/datatables/pdfmake.min.js');?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/datatables/vfs_fonts.js');?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/datatables/buttons.html5.min.js');?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/datatables/buttons.print.min.js');?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/datatables/buttons.colVis.min.js');?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/datatables/dataTables.responsive.min.js');?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/datatables/responsive.bootstrap4.min.js');?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/js/dtable.js');?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/js/tableToExcel.js');?>"></script>

    <script src="<?php echo base_url('lib/select2/js/select2.min.js');?>"></script>

    <script src="<?php echo base_url('lib/js-cookie/js.cookie.js');?>"></script>
    <script src="<?php echo base_url('assets/js/dashforge.js?v=1.7');?>"></script>

    <script type="text/javascript">
      $('#example2').DataTable({
          responsive: true,
          language: {
            searchPlaceholder: 'Search...',
            sSearch: '',
            lengthMenu: '_MENU_ items/page',
          }
        });
    </script>
  </body>
</html>