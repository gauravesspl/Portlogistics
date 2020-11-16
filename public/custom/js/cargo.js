$(function () {
    $('#dtcargo').DataTable({
        "responsive": true,
        "order": [],
    });

    $('#add_cargo').validate({

        rules: {
            name: {
                required: true,
                maxlength: 30,
            },
            instruction: {
                maxlength: 150,
            },
        },
        messages: {
            name: {
                required: "Please enter cargo name",
                maxlength: "Cargo name should not exceed 30 characters."
            },
            instruction: {
                maxlength: "Description should not exceed 150 characters."
            }
        },
        errorElement: "span",
        errorPlacement: function (error, element) {
            error.addClass("invalid-feedback");
            element.closest(".form-group").append(error);
        },
        highlight: function (element, errorClass, validClass) {
            $(element).addClass("is-invalid");
            $(element).removeClass("is-valid");
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass("is-invalid");
            $(element).addClass("is-valid");
        },

        submitHandler: function (form) {
            var type = '';
            if ($("#hidden_id").val() != "") {
                var URL = APP_URL + "/cargo/"+$("#hidden_id").val();
                type = "PUT";
            } else {
                var URL = APP_URL + "/cargo";
                type = "post";
            }
          
            $.ajax({
                type: type,
                url: URL,
                data: $(form).serialize(),
                success: function (response) {
                   
                    if (response.status == 'success') {
                        Swal.fire({
                            position: 'center',
                            icon: 'success',
                            title: response.message,
                            showConfirmButton: false,
                            timer: 1500});
                     window.location.reload();
                    } else {
                        var msg = '';
                        $.each(response.result, function (k, v) {
                            if (msg == '')
                                msg = v;
                            else
                                msg = msg + ', ' + v;
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

    //Delete the Cargo row data
    $('#dtcargo').on('click', '.delete', function () {

        var table = $('#dtcargo').DataTable();
        var rowData = table.row($(this).closest('tr')).data();

        var rowId = rowData[Object.keys(rowData)[2]];
        $("#cargo_id").val(rowId);
        Swal.fire({
            title: "Are you sure?",
            //text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: false,
            //confirmButtonColor: "#3085d6",
            showConfirmButton: false,
            html: `<p>You want to delete this Cargo?</p></br>
            <span  class=" tooltips" data-placement="bottom"  title="Delete Cargo" onClick="return deleteCargo();" style="cursor:pointer;"><i class="fas fa-3x fa-check-circle tooltips text-success"></i></span>
            <span  class=" tooltips" data-placement="bottom" title="Cancel" onClick="swal.close();" style="cursor:pointer;"><i class="fas fa-3x fa-times-circle tooltips text-danger"></i></span>`,
        });
    });
});

$("#add_cargo_btn").click(function () {
    $('.form-control').removeClass('is-invalid');
    $("#modal-default").modal("show");
    $("#add_cargo")[0].reset();
    $("#add_cargo").validate();
    var validator = $("#add_cargo").validate();
    validator.resetForm();
    $('.modal-title').text("Add Cargo");

});

function editCargo(id) {
    $('.form-control').removeClass('is-invalid');
    $('.modal-title').text("Edit Cargo");
    $("#modal-default").modal("show");
    var validator = $("#add_cargo").validate();
    validator.resetForm();
    $.ajax({
        url: APP_URL + "/cargo/"+id,
        dataType: "json",
        type: "GET",
        //data: {id: id},
        success: function (response) {
          
            if (response.status == 'success') {
                $("#name").val(response.result.name);
                $("#instruction").val(response.result.instruction);
                $("#hidden_id").val(response.result.id);
            } else {
                var msg = '';
                $.each(response.result, function (k, v) {
                    if (msg == '')
                        msg = v;
                    else
                        msg = msg + ', ' + v;
                });
                Swal.fire("Error!", "'" + msg + "'", "error");
            }
        },
        error: function () {
            //toastr.error('Unable to delete Contact Support');
        }
    });

    return false;
}
function deleteCargo() {
    $.ajax({
        url: APP_URL + "/cargo/"+ $("#cargo_id").val(),
        type: "DELETE",
       // data: {id: $("#cargo_id").val()},
        dataType: "json",
        success: function (response) {
            if (response.status == 'success')
            {
                Swal.fire("Deleted!", "Cargo has been deleted.", "success");
                window.location.reload();
            } else
            {
                var msg = '';
                $.each(response.result, function (k, v) {
                    if (msg == '')
                        msg = v;
                    else
                        msg = msg + ', ' + v;
                });
                Swal.fire("Error!", "'" + msg + "'", "error");
            }
        },
        error: function () {
           // toastr.error('Unable to delete Contact Support');
        }
    });
}



