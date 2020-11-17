$(function() {
    //listing datatable for trucks
    $('#trucks').DataTable({            
        "responsive": true,
        "order": [],
    });

    //form validation for add and delete
    $('#add_trucks').validate({
        rules: {
            truck_no: {
                required: true,
                maxlength: 10,
            },
            truck_company_id: {
                required: true,
            }        
        },
        messages: {
            truck_no: {
                required: "Please enter a Truck/Dumper No",
                maxlength: "Truck/Dumper No should not exceed 10 characters"
            },
            truck_company_id: {
                required: "Please selet a trucking company",
            }
          
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
                URL =  APP_URL+"/truck/"+$("#hidden_id").val();
                type = "PUT";
            }else{
                URL =  APP_URL+"/truck";
                type = "POST";
            }  
            $.ajax({
                type: type,
                url: URL,
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
                        Swal.fire("Error!", "'"+msg+"'", "error");
                    }
                }            
            });
        }    
    });
    
    //To open add modal
    $("#add_trucks_btn").click(function () {   
        $('.form-control').removeClass('is-invalid');  
        $("#modal-default").modal("show");
        $("#add_trucks")[0].reset();
        $( "#add_trucks" ).validate();
        var validator = $( "#add_trucks" ).validate();       
        validator.resetForm();
        $('.modal-title').text("Add Truck");     
    });

    //Delete the Location row data
    $('#trucks').on('click', '.delete', function () {
        var table = $('#trucks').DataTable();
        var rowData = table.row($(this).closest('tr')).data();
        var rowId = rowData[Object.keys(rowData)[2]];
        $("#truck_id").val(rowId);
        Swal.fire({
            title: "Are you sure?",
            icon: "warning",
            showCancelButton: false,
            showConfirmButton: false,
            html :`<p>You want to delete this Truck?</p></br>
            <span  class=" tooltips" data-placement="bottom"  title="Delete Truck" onClick="return deleteTruck();" style="cursor:pointer;"><i class="fas fa-3x fa-check-circle tooltips text-success"></i></span>
            <span  class=" tooltips" data-placement="bottom" title="Cancel" onClick="swal.close();" style="cursor:pointer;"><i class="fas fa-3x fa-times-circle tooltips text-danger"></i></span>`,
        });
    });
});

//to edit truck
function editTruck(id) {       
    $('.form-control').removeClass('is-invalid');
    $('.modal-title').text("Edit Truck");
    $("#modal-default").modal("show");
    var validator = $( "#add_trucks" ).validate();       
    validator.resetForm();
    var editTruckUrl = APP_URL+"/truck/"+id;
    var data = {};
    customAjaxCall(editTruckUrl,data,editTruckSuccess,editTruckFailure,'GET');
    function editTruckSuccess(response)
    {
        if(response.status == 'success'){
            $("#truck_no").val(response.result.truck_no);
            $("#truck_company_id").val(response.result.truck_company_id);
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
    }
    function editTruckFailure(response)
    {
        Swal.fire('Unable to get data Contact Support');        
    }
    return false;
 } 

 //to delete truck
 function deleteTruck(){
    var delTruckUrl = APP_URL+"/truck/"+$("#truck_id").val();
    var data = {};
    customAjaxCall(delTruckUrl,data,delTruckSuccess,delTruckFailure,'DELETE');
    function delTruckSuccess(response)
    {
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
    }
    function delTruckFailure(response)
    {
        toastr.error('Unable to delete Contact Support');        
    }
}
