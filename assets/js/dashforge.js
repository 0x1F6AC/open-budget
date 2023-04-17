
$(function(){
  'use strict'

  feather.replace();

  ////////// NAVBAR //////////

  // Initialize PerfectScrollbar of navbar menu for mobile only
  if(window.matchMedia('(max-width: 991px)').matches) {
    const psNavbar = new PerfectScrollbar('#navbarMenu', {
      suppressScrollX: true
    });
  }

  // Showing sub-menu of active menu on navbar when mobile
  function showNavbarActiveSub() {
    if(window.matchMedia('(max-width: 991px)').matches) {
      $('#navbarMenu .active').addClass('show');
    } else {
      $('#navbarMenu .active').removeClass('show');
    }
  }

  showNavbarActiveSub()
  $(window).resize(function(){
    showNavbarActiveSub()
  })

  // Initialize backdrop for overlay purpose
  $('body').append('<div class="backdrop"></div>');


  // Showing sub menu of navbar menu while hiding other siblings
  $('.navbar-menu .with-sub .nav-link').on('click', function(e){
    e.preventDefault();
    $(this).parent().toggleClass('show');
    $(this).parent().siblings().removeClass('show');

    if(window.matchMedia('(max-width: 991px)').matches) {
      psNavbar.update();
    }
  })

  // Closing dropdown menu of navbar menu
  $(document).on('click touchstart', function(e){
    e.stopPropagation();

    // closing nav sub menu of header when clicking outside of it
    if(window.matchMedia('(min-width: 992px)').matches) {
      var navTarg = $(e.target).closest('.navbar-menu .nav-item').length;
      if(!navTarg) {
        $('.navbar-header .nav-item').removeClass('show');
      }
    }
  })

  $('#mainMenuClose').on('click', function(e){
    e.preventDefault();
    $('body').removeClass('navbar-nav-show');
  });

  $('#sidebarMenuOpen').on('click', function(e){
    e.preventDefault();
    $('body').addClass('sidebar-show');
  })

  // Navbar Search
  $('#navbarSearch').on('click', function(e){
    e.preventDefault();
    $('.navbar-search').addClass('visible');
    $('.backdrop').addClass('show');
  })

  $('#navbarSearchClose').on('click', function(e){
    e.preventDefault();
    $('.navbar-search').removeClass('visible');
    $('.backdrop').removeClass('show');
  })



  ////////// SIDEBAR //////////

  // Initialize PerfectScrollbar for sidebar menu
  if($('#sidebarMenu').length) {
    const psSidebar = new PerfectScrollbar('#sidebarMenu', {
      suppressScrollX: true
    });


    // Showing sub menu in sidebar
    $('.sidebar-nav .with-sub').on('click', function(e){
      e.preventDefault();
      $(this).parent().toggleClass('show');

      psSidebar.update();
    })
  }


  $('#mainMenuOpen').on('click touchstart', function(e){
    e.preventDefault();
    $('body').addClass('navbar-nav-show');
  })

  $('#sidebarMenuClose').on('click', function(e){
    e.preventDefault();
    $('body').removeClass('sidebar-show');
  })

  $('.select2').select2({
    placeholder: 'Tanlash',
    searchInputPlaceholder: 'Izlash...',
    language: {
      noResults: function () {
        return 'Ma\'lumotlar topilmadi!';
      }
    }
  });

  $('.notifications-select2').select2({
    placeholder: 'Tanlash',
    dropdownParent: $('.modal'),
    searchInputPlaceholder: 'Izlash...',
    language: {
      noResults: function () {
        return 'Ma\'lumotlar topilmadi!';
      }
    }
  });

  $('.notifications-trash-select2').select2({
    placeholder: 'Tanlash',
    dropdownParent: $('#trash_modal'),
    searchInputPlaceholder: 'Izlash...',
    language: {
      noResults: function () {
        return 'Ma\'lumotlar topilmadi!';
      }
    }
  });

  $('.export-select2').select2({
    placeholder: 'Tanlash',
    dropdownParent: $('#export_modal'),
    searchInputPlaceholder: 'Izlash...',
    language: {
      noResults: function () {
        return 'Ma\'lumotlar topilmadi!';
      }
    }
  });

  $(document).on('click', '[data-location]', function(e){
    let url = $(this).attr( 'data-location' );
    var confirmation = $(this).attr('data-confirm');
    if (typeof confirmation !== 'undefined' && confirmation !== false) {
      if(confirm("Siz chindan ham ushbu harakatni bajarmoqchimisiz?")) {
        window.location.href = url;
      }
    }else{
      window.location.href = url;
    }
    
  });

  $(document).on('click', '[data-stats]', function(e){
    let id = $(this).attr( 'data-stats' );
    $('[data-reload-bot]').attr('data-reload-bot', id);
    $('[data-pause-bot]').attr('data-pause-bot', id);
    fetch(base_url + "ajax/bots/stats/" + id).then((response) => response.json()).then( (res) => { 
      if (res.status == 'ok') {
        if ( res.bot_status.toString() == '1' ) {
          $('[data-pause-bot]').html('<i class="fas fa-pause"></i>');
          $('[data-pause-bot]').removeClass('btn-danger').removeClass('btn-info').addClass('btn-danger');
        }else{
          $('[data-pause-bot]').html('<i class="fas fa-play"></i>');
          $('[data-pause-bot]').removeClass('btn-danger').removeClass('btn-info').addClass('btn-info');
        }
        $('#botstats .modal-body').html( res.data );
        $('#botstats').modal('show');
      }else if (res.status == 'error') {
        $.growl.error({ title:"", message: res.message});
      }else{
        $.growl.error({ title:"", message: "Tizimga ulanishda xatolik!" });
      }
    });
    e.preventDefault();
  });

  $(document).on('click', '[data-reload-bot]', function(e){
    let id = $(this).attr( 'data-reload-bot' );
    fetch(base_url + "ajax/bots/reload/" + id).then((response) => response.json()).then( (res) => { 
       if (res.status == 'ok') {
          $dtables['bots'].ajax.reload(null, false);
          $.growl.notice({ title:"", message: res.message});
       }else if (res.status == 'error') {
          $.growl.error({ title:"", message: res.message});
       }else{
          $.growl.error({ title:"", message: "Tizimga ulanishda xatolik!"});
       }
    });
    e.preventDefault();
  });

  $(document).on('click', '[data-pause-bot]', function(e){
    let id = $(this).attr( 'data-pause-bot' );
    fetch(base_url + "ajax/bots/pause/" + id).then((response) => response.json()).then( (res) => { 
       if (res.status == 'ok') {
          $dtables['bots'].ajax.reload(null, false);
          if ( res.bot_status.toString() == '1' ) {
            $('[data-pause-bot]').html('<i class="fas fa-pause"></i>');
            $('[data-pause-bot]').removeClass('btn-danger').removeClass('btn-info').addClass('btn-danger');
            $.growl.notice({ title:"", message: res.message});
          }else{
            $('[data-pause-bot]').html('<i class="fas fa-play"></i>');
            $('[data-pause-bot]').removeClass('btn-danger').removeClass('btn-info').addClass('btn-info');
            $.growl.warning({ title:"", message: res.message});
          }
       }else if (res.status == 'error') {
          $.growl.error({ title:"", message: res.message });
       }else{
          $.growl.error({ title:"", message: "Tizimga ulanishda xatolik!" });
       }
    });
    e.preventDefault();
  });

  $(document).on('click', '[data-user-stats]', function(e){
    let id = $(this).attr( 'data-user-stats' );
    fetch(base_url + "ajax/users/stats/" + id).then((response) => response.json()).then( (res) => { 
      if (res.status == 'ok') {
        $('#userstats .modal-dialog').removeClass( 'modal-lg' );
        $('#userstats .modal-title').html( 'Statistika' );
        $('#userstats .modal-body').html( res.data );
        $('#userstats').modal('show');
      }else if (res.status == 'error') {
        $.growl.error({ title:"", message: res.message});
      }else{
        $.growl.error({ title:"", message: "Tizimga ulanishda xatolik!" });
      }
    });
    e.preventDefault();
  });

  $(document).on('click', '[data-user-referrers]', function(e){
    let id = $(this).attr( 'data-user-referrers' );
    fetch(base_url + "ajax/users/referrers/" + id).then((response) => response.json()).then( (res) => { 
      if (res.status == 'ok') {
        $('#userstats .modal-title').html( 'Referallar' );
        $('#userstats .modal-dialog').removeClass( 'modal-lg' );
        $('#userstats .modal-body').html( res.data );
        $('#userstats').modal('show');
      }else if (res.status == 'error') {
        $.growl.error({ title:"", message: res.message});
      }else{
        $.growl.error({ title:"", message: "Tizimga ulanishda xatolik!" });
      }
    });
    e.preventDefault();
  });

  $(document).on('click', '[data-user-votes]', function(e){
    let id = $(this).attr( 'data-user-votes' );
    fetch(base_url + "ajax/users/votes/" + id).then((response) => response.json()).then( (res) => { 
      if (res.status == 'ok') {
        $('#userstats .modal-dialog').removeClass( 'modal-lg' ).addClass('modal-lg');
        $('#userstats .modal-title').html( 'Ovozlar' );
        $('#userstats .modal-body').html( res.data );
        $('#userstats').modal('show');
      }else if (res.status == 'error') {
        $.growl.error({ title:"", message: res.message});
      }else{
        $.growl.error({ title:"", message: "Tizimga ulanishda xatolik!" });
      }
    });
    e.preventDefault();
  });

  $(document).on('click', '[data-payment]', function(e){
    let id = $(this).attr( 'data-payment' );
    fetch(base_url + "ajax/payments/data/" + id).then((response) => response.json()).then( (res) => { 
      if (res.status == 'ok') {
        $('#paymentdata .modal-body').html( res.data );
        $('#paymentdata').modal('show');
      }else if (res.status == 'error') {
        $.growl.error({ title:"", message: res.message});
      }else{
        $.growl.error({ title:"", message: "Tizimga ulanishda xatolik!" });
      }
    });
    e.preventDefault();
  });

  $(document).on('click', '[data-succes-payment]', function(e){
    let id = $(this).attr( 'data-succes-payment' );
    if ( confirm( 'Siz chindan ham ushbu harakatni bajarmoqchimisiz?' ) ) {
      fetch(base_url + "ajax/payments/status/" + id).then((response) => response.json()).then( (res) => { 
         if (res.status == 'ok') {
            $dtables['payments'].ajax.reload(null, false);
            $.growl.notice({ title:"", message: res.message });
            /*if ( res.payment_status.toString() == '1' ) {
              $('[data-succes-payment]').html('<i class="fas fa-times"></i> To\'lovni bekor qilish');
              $('[data-succes-payment]').removeClass('btn-danger').removeClass('btn-info').addClass('btn-danger');
            }else{
              $('[data-succes-payment]').html('<i class="fas fa-check"></i> To\'lov qilindi');
              $('[data-succes-payment]').removeClass('btn-danger').removeClass('btn-info').addClass('btn-info');
            }*/
         }else if (res.status == 'error') {
            $.growl.error({ title:"", message: res.message });
         }else{
            $.growl.error({ title:"", message: "Tizimga ulanishda xatolik!" });
         }
      });
    }
    e.preventDefault();
  });

  $(document).on('click', '[data-message-payment]', function(e){
    let id = $(this).attr( 'data-message-payment' );
    if ( confirm( 'Siz chindan ham foydalanuvchiga to\'lov haqida xabarnoma jo\'natmoqchimisiz?' ) ) {
      fetch(base_url + "ajax/payments/message/" + id).then((response) => response.json()).then( (res) => { 
         if (res.status == 'ok') {
            $.growl.notice({ title:"", message: res.message });
         }else if (res.status == 'error') {
            $.growl.error({ title:"", message: res.message });
         }else{
            $.growl.error({ title:"", message: "Tizimga ulanishda xatolik!" });
         }
      });
    }
    e.preventDefault();
  });

  $(document).on('click', '[data-reload-payments]', function(e){
    $dtables['payments'].ajax.reload(null, false);
    e.preventDefault();
  });

  $(document).on('submit', '.send-notifications', function(e){
    e.preventDefault();
    let data = $(this).serialize();

    fetch(base_url + "ajax/notifications/send/", {
      method: "POST",
      headers: {
          "Content-Type": "application/x-www-form-urlencoded"
      },
      body: new URLSearchParams(data)
    }).then((response) => response.json()).then( (res) => { 
        if (res.status == 'ok') {
          $dtables['notifications'].ajax.reload(null, false);
          $('.send-notifications')[0].reset();
          $('.notifications-select2').val(null).trigger("change");
          $('#send_modal').modal('hide');
          $.growl.notice({ title:"", message: res.message });
        }else if (res.status == 'error') {
          $.growl.error({ title:"", message: res.message });
        }else{
          $.growl.error({ title:"", message: "Tizimga ulanishda xatolik!" });
        }
    });

    e.preventDefault();
  });
  
  $(document).on('click', '.send-notifications-button', function(e){
    $('.send-notifications').submit();
    e.preventDefault();
  });

  $(document).on('submit', '.trash-notifications', function(e){
    e.preventDefault();
    let data = $(this).serialize();

    if ( confirm( "Siz chindan ham ushbu harakatni bajarmoqchimisiz?" ) ) {
      fetch(base_url + "ajax/notifications/trash/", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: new URLSearchParams(data)
      }).then((response) => response.json()).then( (res) => { 
          if (res.status == 'ok') {
            $dtables['notifications'].ajax.reload(null, false);
            $('.trash-notifications')[0].reset();
            $('.notifications-trash-select2').val(null).trigger("change");
            $('#trash_modal').modal('hide');
            $.growl.notice({ title:"", message: res.message });
          }else if (res.status == 'error') {
            $.growl.error({ title:"", message: res.message });
          }else{
            $.growl.error({ title:"", message: "Tizimga ulanishda xatolik!" });
          }
      });
    }

    e.preventDefault();
  });

  $(document).on('click', '.trash-notifications-button', function(e){
    $('.trash-notifications').submit();
    e.preventDefault();
  });

  $(document).on('submit', '.export-votes', function(e){
    e.preventDefault();
    let data = $(this).serialize();

     $.growl.notice({ title:"", message: "Eksport jarayoni boshlandi!" });

    fetch(base_url + "ajax/export/votes/",{
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded"
        },
        body: new URLSearchParams(data)
    }).then((response) => {
      var content_type = response.headers.get("content-type");
      if(content_type.startsWith('application/json')){
        return response.json().then( (res) => {
          $.growl.error({ title:"", message: res.message });
        });
      }else if(content_type.startsWith('text/html')){
        return response.text().then(( text ) => {
          $('#export_data').html(text);
          TableToExcel.convert(document.querySelector("#export_data"), {
            name: document.location.host+'_'+Math.round(new Date().getTime()/1000)+'.xlsx',
            sheet: {
              name: "Ovozlar"
            }
          });
          $('.export-votes')[0].reset();
          $('.export-select2').val(null).trigger("change");
          $('#export_modal').modal('hide');
          setTimeout( () => { $('#export_data').html(''); }, 3000);
          $.growl.notice({ title:"", message: "Ma'lumotlar muvaffaqiyatli eksport qilindi!" });
        }).catch(() => {
          $.growl.error({ title:"", message: "Tizimga ulanishda xatolik!" });
        });
      }
    });

    e.preventDefault();
  });

  $(document).on('click', '.export-votes-button', function(e){
    $('.export-votes').submit();
    e.preventDefault();
  });

  function copyToClipboard(element) {
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val($(element).text()).select();
    document.execCommand("copy");
    $temp.remove();
    $.growl.warning({ title:"", message: $(element).text() + " - buferga nusxalandi!" });
  }

  $(document).on('click', '.copy-me', function(e){
    copyToClipboard( $( this ) );
    e.preventDefault();
  });

  var updateTables = function( table ) {
    $dtables[table].ajax.reload(null, false);
    setTimeout(() => { updateTables(table) }, 10000);
  }

  if( $dtables.hasOwnProperty("users") ){
    updateTables('users');
  }

  if( $dtables.hasOwnProperty("referrers") ){
    updateTables('referrers');
  }

  $('[data-bs-toggle="popover"]').popover({html : true});
  $('[data-bs-toggle="tooltip"]').tooltip();

  if ( $dtables.hasOwnProperty( 'notifications' ) ) {
    updateTables('notifications');
    $(document).ajaxComplete(function(event,xhr,options){
      $('.tooltip').remove();
      $('[data-bs-toggle="tooltip"]').tooltip();
    });
  }

  if ( $dtables.hasOwnProperty( 'votes' ) ) {
    updateTables('votes');
    $(document).ajaxComplete(function(event,xhr,options){
    $('[data-bs-toggle="tooltip"]').tooltip();
        
    });
  }

  // hide sidebar when clicking outside of it
  $(document).on('click touchstart', function(e){
    e.stopPropagation();

    // closing of sidebar menu when clicking outside of it
    if(!$(e.target).closest('.burger-menu').length) {
      var sb = $(e.target).closest('.sidebar').length;
      var nb = $(e.target).closest('.navbar-menu-wrapper').length;
      if(!sb && !nb) {
        if($('body').hasClass('navbar-nav-show')) {
          $('body').removeClass('navbar-nav-show');
        } else {
          $('body').removeClass('sidebar-show');
        }
      }
    }
  });

})
