$(function() {
  $('#truck_company').DataTable({            
    "responsive": true,
    "order": [],
  });

  //Delete the Trucking Comapany row data
  $('#truck_company').on('click', '.delete', function () {
    var table = $('#truck_company').DataTable();
    var rowData = table.row($(this).closest('tr')).data();
    var rowId = rowData[Object.keys(rowData)[5]];
    $("#truckComp_id").val(rowId);
    Swal.fire({
      title: "Are you sure?",
      icon: "warning",
      showCancelButton: false,
      showConfirmButton: false,
      html :`<p>You want to delete this Trucking Company?</p></br>
        <span  class=" tooltips" data-placement="bottom"  title="Delete Trucking Company" onClick="return deleteTruckCompany();" style="cursor:pointer;"><i class="fas fa-3x fa-check-circle tooltips text-success"></i></span>
        <span  class=" tooltips" data-placement="bottom" title="Cancel" onClick="swal.close();" style="cursor:pointer;"><i class="fas fa-3x fa-times-circle tooltips text-danger"></i></span>`,
    });
  });

  $.validator.addMethod("custom_number", function(value, element) {
  return this.optional(element) || value === "NA" ||
        value.match(/^[0-9,\+-]+$/);
  }, "Please enter a valid number");

  $('#add_truck_company').validate({
    rules: {
      name: {
        required: true,
        maxlength: 100,
      },
      email: {
        required: true,
         maxlength: 60,
      },
      mobile_no: {
        required: true,
        maxlength: 15,
        custom_number: true,
      },
      contact_name: {
        maxlength: 35,
      },
      contact_mobile_no: {
        maxlength: 15,
        custom_number: true,
      },
    },
    messages: {
      name: {
        required: "Please enter trucking company",
        maxlength: "Trucking Company name should not exceed 100 characters."
      },
      email: {
        required: "Please enter an email address.",
        maxlength: "Email cannot exceed 60 characters."
      }, 
      mobile_no: {
        required: "Please enter phone number",
        maxlength: "Phone No should not exceed 15 characters.",
      },
      contact_name: {
        maxlength: "Contact Person should not exceed 35 characters."
      },
      contact_mobile_no: {
        maxlength: "Contact Phone No should not exceed 15 characters."
      },
    },    
    errorElement: 'span',
    errorPlacement: function (error, element) {
      error.addClass('invalid-feedback');
      element.closest('.form-group').append(error);
    },
    highlight: function (element, errorClass, validClass) {
      $(element).addClass('is-invalid');
    },
    unhighlight: function (element, errorClass, validClass) {
      $(element).removeClass('is-invalid');
    },
    submitHandler: function(form) {
      var URL = '';
      var type = '';
      if($("#hidden_id").val() != ""){
        URL =  APP_URL+"/truck-company/"+$("#hidden_id").val();
        type = "PUT";
      }else{
        URL =  APP_URL+"/truck-company";
        type = "POST";
      }         
      $.ajax({           
        url: URL,
        type: type,
        data: $(form).serialize(),
        success: function(response) {
          if(response.status_code == 200){
            Swal.fire({
              position: 'center',
              icon: 'success',
              title: response.message,
              showConfirmButton: false,
              timer: 1500
            });
            window.location.reload();
          }else{
            var msg='';
            $.each(response.result, function (k,v)  {
              if(msg == '')
                msg = v;
              else
                msg = msg+', '+v;
            });
            Swal.fire({
              position: 'center',
              icon: 'error',
              title: msg,
              showConfirmButton: false,
              showCancelButton: true,
              cancelButtonText: '<i class="fas fa-3x fa-check-circle tooltips text-success"></i>',
              timer: 1500
            });
          }
        }            
      });
    }    
  });
});

$("#add_truck_company_btn").click(function () {  
       $('.form-control').removeClass('is-invalid');
       $("#modal-default").modal("show");
       $("#add_truck_company")[0].reset();
       $( "#add_truck_company" ).validate();
       var validator = $( "#add_truck_company" ).validate();       
       validator.resetForm();
       $('.modal-title').text("Add Trucking Company");     
});

function editTruckCompany(id){
  $('.form-control').removeClass('is-invalid');
    $('.modal-title').text("Edit Trucking Company");
    $("#modal-default").modal("show");
    var validator = $( "#add_truck_company" ).validate();       
    validator.resetForm();
    $.ajax({
      url: APP_URL+"/truck-company/"+id,
      type: "GET",
      dataType: "json",
      success: function(response) {
        if(response.status == 'success'){
          $("#name").val(response.result.name);
          $("#email").val(response.result.email);
          $("#mobile_no").val(response.result.mobile_no);
          $("#contact_name").val(response.result.contact_name);
          $("#contact_mobile_no").val(response.result.contact_mobile_no);
          $("#hidden_id").val(response.result.id);
        }else{
          var msg='';
          $.each(response.result, function (k,v)  {
            if(msg == '')
              msg = v;
            else
              msg = msg+', '+v;
          });
          Swal.fire("Error!", "'"+msg+"'", "error");
        }
      },
      error: function() {
        Swal.fire('Unable to get data Contact Support');
      }
    });
    return false;
}

function deleteTruckCompany(){
  $.ajax({
    url: APP_URL+"/truck-company/"+$("#truckComp_id").val(),
    type: "DELETE",
    dataType: "json",
    success: function(response) {
      if(response.status == 'success')
      {
        Swal.fire("Deleted!", response.message, "success");
        window.location.reload();
      }
      else
      {
        var msg='';
        $.each(response.result, function (k,v)  {
          if(msg == '')
            msg = v;
          else
            msg = msg+', '+v;
          });
          Swal.fire("Error!", "'"+msg+"'", "error");
        }
    },
    error: function() {}
  });
}

    
  
