$(function () {
    $('.select2').select2({        
        tags: true,
    })
    
    $('.cmb_truck').on('change', function() {
        $('.cmb_truck ').each(function () {
            $('.cmb_truck').valid();       
        });
    });

    $('.truck_company').on('select2:select', function (e) {
	 	$(this).valid();
	});

    /*Add Plan Truck For(m Validation*/
    $('#addTruckForm').on('submit', function (e) {
        $('.planTruckDetailTr').each(function(){
             if($(this).find('.truck_company option:selected').text() == ""){
                 $('#-error').remove();
             }
        });     
        $('.cmb_truck ').each(function () {            
            $(this).rules("add", {
                required: true,
                messages: {
                    required: "Please select truck number",
                }
            });
        });
        $('.truck_company').each(function () {             
                $(this).rules("add", {
                required: true,
                messages: {
                    required: "Please select trucking company",
                }
            });
        });
        $('.truck_company').on('select2:select', function () {
	 	    $(this).valid();
	    });
        e.preventDefault();
    });

    $('#addTruckForm').validate({
        errorElement: 'div',
        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            element.closest('td').append(error);
        },
        highlight: function (element, errorClass, validClass) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
            $(element).parent().children("div").removeClass(errorClass);
        },
        submitHandler: function (form) {
            var chekStatus = true;
            $('.cmb_truck').each(function () {
                var data = $(this).is(':disabled');
                if (data == false) {
                    chekStatus = false;
                }
            });
            if (chekStatus == true) {
                Swal.fire({
                    position: 'center',
                    icon: 'error',
                    title: "Please add new truck number",
                    showConfirmButton: false,
                    timer: 1500
                })
            }else {
                $.ajax({
                    type: 'POST',
                    url: APP_URL + "/plan/truck",
                    data: $(form).serialize(),
                    async: false,
                    dataType: 'json',
                    success: function (response) {
                        if (response.status == 'success') {
                            Swal.fire({
                                position: 'center',
                                icon: 'success',
                                title: "Success",
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            })
                            window.location = APP_URL + '/plans';
                        } else {
                            var err = '';
                            if (typeof (response.result) == 'string') {
                                err = response.result;
                            } else if (typeof (response.result) == 'object') {
                                $.each(response.result, function (i, v) {
                                    err += v[0] + '<br/>';
                                });
                            }
                            Swal.fire({
                                position: 'center',
                                icon: 'error',
                                title: "Error!",
                                text: err,
                                showConfirmButton: false,
                                timer: 1500
                            })
                        }
                    },
                    error: function (e) {
                        console.log(e);
                    }
                });
            }
        }
    });
});

$(document).on('click', '.add_plan_details', function () {
    var el = $(this);
    if ($(el).closest('.planTruckDetailTr').find('.hdnPlanDetailId').val() != '') {
        $(el).closest('.planTruckDetailTr').find('.cmb_truck').prop("disabled", false);
    }
    destroySelect2();
    var cloneRow = $('.planTruckDetailTr:last').clone(true);
    cloneRow.find('.cmb_truck').val('');
    cloneRow.find('.trucking_company').html('');    
    cloneRow.find('.hdnPlanDetailId').val('');
    cloneRow.find('.truck_company_id').val('');
    cloneRow.find('#-error').html('');    
    cloneRow.find('.cmb_truck').prop("disabled", false);   
    $(el).closest('.planTruckDetailTr').after(cloneRow);
    reinitializeSelect2();
    if ($(el).closest('.planTruckDetailTr').find('.hdnPlanDetailId').val() != '') {
        $(el).closest('.planTruckDetailTr').find('.cmb_truck').prop("disabled", true);
    }
    resetPlanDetailFieldNames();
});

$(document).on('change', '.truck', function () {
    var el = $(this);
    var trucking_company_id = "";
    if (el.find('option:selected').val() != "") {
        trucking_company_id = el.find('option:selected').attr('trucking_company_id');
    }
    trucking_company_id = $(el).closest('.planTruckDetailTr').find('.truck_company').val(); 
    $(el).closest('.planTruckDetailTr').find('.truck_company_id').val(trucking_company_id);
});

$(document).on('change', '.cmb_truck', function () {
    var selection = '<select  style="font-size:0.8rem"  class="form-control select2 truck truck_company"  trucking_company_name=""><option value="">Select Trucking Company</option>';
    $.each(truck_company, (key, val) => {
        selection += '<option value="' + val.id + '">' + val.name + '</option>';
    });
    selection += '</select>';
    var el = $(this);
    var trucking_company_id = el.find('option:selected').attr('trucking_company_id');
    $(el).closest('.planTruckDetailTr').find('.truck_company_id').val(trucking_company_id);
    if (el.find('option:selected').val() != "") {
        var trucking_company_name = el.find('option:selected').attr('trucking_company_name');
        if (trucking_company_name) {
            $(el).closest('.planTruckDetailTr').find('.trucking_company').html(trucking_company_name);
        }
        else {
            $(el).closest('.planTruckDetailTr').find('.trucking_company').html(selection);
        }
        $(el).closest('.planTruckDetailTr').find('.hdn_truck_id').val(el.find('option:selected').val());
    } else {
        $(el).closest('.planTruckDetailTr').find('.trucking_company').html("");
        $(el).closest('.planTruckDetailTr').find('.hdn_truck_id').val("");
    }
});

$(document).on('click', '.remove_plan_details', function () {
    var el = $(this);
    if ($(".planTruckDetailTr").length > 1) {
        var getId = $(el).closest('.planTruckDetailTr').find('.hdn_truck_id').val();
        el = $(this);
        if (el.closest('tr').find('.hdnPlanDetailId').val() !== '') {
            Swal.fire({
                title: 'Are you sure?',
                text: "You want to delete this truck?",
                icon: 'warning',
                showCancelButton: true,
                cancelButtonText: '<i title="Cancel"  class="fas fa-3x fa-times-circle tooltips text-danger"></i>',
                confirmButtonText: '<i title="Delete Truck"class="fas fa-3x fa-check-circle tooltips text-success"></i>',
            }).then((result) => {
                if (result.isConfirmed) {
                    if (getId != "") {
                        $.ajax({
                            type: 'DELETE',
                            url: APP_URL + "/plan/truck/"+$("#plan_id").val()+"/"+getId,
                            async: false,
                            dataType: 'Json',
                            success: function (response) {
                                if (response.status == 'success') {
                                    Swal.fire({
                                        position: 'center',
                                        icon: 'success',
                                        title: response.message,
                                        showConfirmButton: false,
                                        timer: 1500
                                    })
                                    el.closest('tr').remove();
                                    resetPlanDetailFieldNames();
                                } else {
                                    Swal.fire({
                                        position: 'center',
                                        icon: 'error',
                                        title: response.message,
                                        showConfirmButton: false,
                                        timer: 1500
                                    })
                                }

                            },
                        });
                    } else {
                        el.closest('tr').remove();
                        resetPlanDetailFieldNames();
                    }
                }
            });
        } else {
            el.closest('tr').remove();
            resetPlanDetailFieldNames();
        }
    } else {
        Swal.fire('There should be atleast 1 truck associated with this plan');
    }
});

function resetPlanDetailFieldNames() {
    $.each($('.planTruckDetailTr'), function (i) {
        var el = $(this);
        el.find('.hdnPlanDetailId').attr('name', 'trucks[' + i + '][id]');
        el.find('.hdn_truck_id').attr('name', 'trucks[' + i + '][truck_id]');
        el.find('.cmb_truck').attr('name', 'trucks[' + i + '][truck_id]');
        el.find('.truck_company_id').attr('name', 'trucks[' + i + '][truck_company_id]');
    });
}
function destroySelect2() {
    var $select = $('#planTruckDetailTbl .select2').select2({
        tags: true
    });
    $select.each(function (i, item) {
        $(item).select2("destroy");
    });
}
function reinitializeSelect2() {
    var $select = $('#planTruckDetailTbl .select2').select2({
        tags: true
    });
    $select.each(function (i, item) {
        $(item).select2({
            tags: true
        });
    });
}



