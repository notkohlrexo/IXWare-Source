$(document).ready(function() {

    // SideNav Button Initialization
    $(".button-collapse").sideNav();
    // SideNav Scrollbar Initialization
    var sideNavScrollbar = document.querySelector('.custom-scrollbar');
    var ps = new PerfectScrollbar(sideNavScrollbar);
  })

$("#alert-target").click(function () {
  toastr["error"]("You need to activate your account first to use all features!", "Account")
  });

  toastr.options = {
    "closeButton": false,
    "debug": false,
    "newestOnTop": true,
    "progressBar": true,
    "positionClass": "md-toast-top-full-width",
    "preventDuplicates": true,
    "showDuration": 400,
    "hideDuration": 1000,
    "timeOut": 5000,
    "extendedTimeOut": 1000,
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
  }

$("#alert-target1").click(function () {
     toastr["error"]("You need to activate your account first to use all features!", "Account")
     });
   
     toastr.options = {
       "closeButton": false,
       "debug": false,
       "newestOnTop": true,
       "progressBar": true,
       "positionClass": "md-toast-top-full-width",
       "preventDuplicates": true,
       "showDuration": 400,
       "hideDuration": 1000,
       "timeOut": 5000,
       "extendedTimeOut": 1000,
       "showEasing": "swing",
       "hideEasing": "linear",
       "showMethod": "fadeIn",
       "hideMethod": "fadeOut"
  }

$("#alert-target2").click(function () {
     toastr["error"]("You need to activate your account first to use all features!", "Account")
     });
   
     toastr.options = {
       "closeButton": false,
       "debug": false,
       "newestOnTop": true,
       "progressBar": true,
       "positionClass": "md-toast-top-full-width",
       "preventDuplicates": true,
       "showDuration": 400,
       "hideDuration": 1000,
       "timeOut": 5000,
       "extendedTimeOut": 1000,
       "showEasing": "swing",
       "hideEasing": "linear",
       "showMethod": "fadeIn",
       "hideMethod": "fadeOut"
     }

$("#alert-target3").click(function () {
          toastr["error"]("You need to activate your account first to use all features!", "Account")
          });
        
          toastr.options = {
            "closeButton": false,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "md-toast-top-full-width",
            "preventDuplicates": true,
            "showDuration": 400,
            "hideDuration": 1000,
            "timeOut": 5000,
            "extendedTimeOut": 1000,
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
          }
     

  $('#showPW').modal('show'); 

  $(document).ready(function(){

    $(document).on('click', '#getUser', function(e){
  
     e.preventDefault();
  
     var uid = $(this).data('id'); 
  
     $('#dynamic-content').html('');
     $('#modal-loader').show(); 
 
     $.ajax({
          url: 'userinfo.php',
          type: 'POST',
          data: 'id='+uid,
          dataType: 'html'
     })
     .done(function(data){
          console.log(data); 
          $('#dynamic-content').html('');
          $('#dynamic-content').html(data);
          $('#modal-loader').hide();
     })
     .fail(function(){
          $('#dynamic-content').html('<i class="glyphicon glyphicon-info-sign"></i> Something went wrong, Please try again...');
          $('#modal-loader').hide();
     });

    });
    $(document).on('click', '#getFiles', function(e){
  
      e.preventDefault();
   
      var uid = $(this).data('id');
   
      $('#dynamic-content').html('');
      $('#modal-loader').show();
  
      $.ajax({
           url: 'userfiles.php',
           type: 'POST',
           data: 'id='+uid,
           dataType: 'html'
      })
      .done(function(data){
           console.log(data); 
           $('#dynamic-content').html('');
           $('#dynamic-content').html(data);
           $('#modal-loader').hide();
      })
      .fail(function(){
           $('#dynamic-content').html('<i class="glyphicon glyphicon-info-sign"></i> Something went wrong, Please try again...');
           $('#modal-loader').hide();
      });
 
     });
     $(document).on('click', '#getTools', function(e){
  
      e.preventDefault();
   
      var uid = $(this).data('id');
   
      $('#dynamic-content').html('');
      $('#modal-loader').show();
  
      $.ajax({
           url: 'usertools.php',
           type: 'POST',
           data: 'id='+uid,
           dataType: 'html'
      })
      .done(function(data){
           console.log(data); 
           $('#dynamic-content').html('');
           $('#dynamic-content').html(data);
           $('#modal-loader').hide();
      })
      .fail(function(){
           $('#dynamic-content').html('<i class="glyphicon glyphicon-info-sign"></i> Something went wrong, Please try again...');
           $('#modal-loader').hide();
      });
 
     });
     $(document).on('click', '#showCookie', function(e){
  
      e.preventDefault();
   
      var uid = $(this).data('id');
   
      $('#dynamic-content').html('');
      $('#modal-loader').show();
  
      $.ajax({
           url: 'showcookie.php',
           type: 'POST',
           data: 'id='+uid,
           dataType: 'html'
      })
      .done(function(data){
           console.log(data); 
           $('#dynamic-content').html('');
           $('#dynamic-content').html(data);
           $('#modal-loader').hide();
      })
      .fail(function(){
           $('#dynamic-content').html('<i class="glyphicon glyphicon-info-sign"></i> Something went wrong, Please try again...');
           $('#modal-loader').hide();
      });
 
     });
     $(document).on('click', '#checkCookie', function(e){
  
          e.preventDefault();
       
          var uid = $(this).data('id');
       
          $('#dynamic-content').html('');
          $('#modal-loader').show();
      
          $.ajax({
               url: 'checkcookie.php',
               type: 'POST',
               data: 'id='+uid,
               dataType: 'html'
          })
          .done(function(data){
               console.log(data); 
               $('#dynamic-content').html('');
               $('#dynamic-content').html(data);
               $('#modal-loader').hide();
          })
          .fail(function(){
               $('#dynamic-content').html('<i class="glyphicon glyphicon-info-sign"></i> Something went wrong, Please try again...');
               $('#modal-loader').hide();
          });
     
         });
});

// Material Select Initialization
$(document).ready(function() {
$('.mdb-select').materialSelect();
});

function openChat(){
     $("#chatcard").slideToggle("slow");
     $("#openButton").slideToggle("slow");
}
 
function closeChat(){
     $("#chatcard").slideToggle("slow");
     $("#openButton").slideToggle("slow");
}

$(document).ready(function () {
     $('#table-dataTable-js').DataTable({"order": [[6,'desc']]});
     $('#table-dataTable-js-Shared').DataTable({"order": [[6,'desc']]});
     $('#table-dataTable-bots').DataTable({"order": [[3,'desc']]});
     $('#table-dataTable-bots-Shared').DataTable({"order": [[3,'desc']]});
     $('#table-dataTable-ps').DataTable({"order": [[4,'desc']]});
     $('#table-dataTable-ps-Shared').DataTable({"order": [[4,'desc']]});
     $('#table-dataTable-settings').DataTable({"order": [[4,'desc']]});
     $('#table-dataTable-psIps').DataTable({"order": [[4,'desc']]});
     $('#table-dataTable-profile').DataTable({"order": [[2,'desc']]});
     $('#table-dataTable-lActive').DataTable({"order": [[4,'desc']]});
     $('#table-dataTable-lHistory').DataTable({"order": [[3,'desc']]});
     $('#table-dataTable-users').DataTable({"order": [[6,'desc']]});
     $('#table-dataTable-licenses').DataTable();
     $('#table-dataTable-usedLicenses').DataTable({"order": [[4,'desc']]});
     $('#table-dataTable-alerts').DataTable({"order": [[3,'desc']]});
     $('#table-dataTable-sidenews').DataTable({"order": [[1,'desc']]});
     $('#table-dataTable-auditlog').DataTable({"order": [[4,'desc']]});
     $('#table-dataTable-adminlog').DataTable({"order": [[2,'desc']]});
     $('.dataTables_length').addClass('bs-select');
});

$('#load-button').click(function() {
     $('#load-button').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Loading...').addClass('disabled');
});