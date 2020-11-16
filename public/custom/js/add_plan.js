$(function () {
    $('#from,#to').datetimepicker({
        format: 'DD/MM/YYYY HH:mm',
        ignoreReadonly: true,
        minDate:new Date()
    });
    $(".input-group-append").click(function(){
        $("#now").val(moment().format('DD/MM/YYYY HH:mm'));
    });
    /*Add Plan Form Validation*/
    $('#addPlanForm').on('submit', function (e) {
        $("#now").val(moment().format('DD/MM/YYYY HH:mm'));
        $('.selCustomer').each(function () {
            $(this).rules("add", {
                required: true,
                messages: {
                    required: "Please select customer name",
                }
            });
        });
        /*$('.selPlot').each(function () {
            $(this).rules("add", {
                required: true,
                messages: {
                    required: "Please select plot no",
                }
            });
        });*/
        e.preventDefault();
    });
    $('#addPlanForm').validate({
        rules: {
            vessel_name: {
                required: true,
                maxlength: 50
            },
            origin_id: {
                required: true
            },
            date_to: {
                required: true,
                greaterThan: "#date_from",
                greaterThanNow: "#now"
            },
            date_from: {
                required: true,
                greaterThanNow: "#now"
            },
            cargo_id: {
                required: true
            }
        },
        messages: {
            vessel_name: {
                required: "Please enter vessel",
                maxlength: "Vessel name should not exceed 50 characters",
            },
            origin_id: {
                required: "Please select berth",
            },
            date_to: {
                required: "Please select to date",
            },
            date_from: {
                required: "Please select from date",
            },
            cargo_id: {
                required: "Please select cargo",
            }
        },
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
        },
        submitHandler: function (form) {
            $("#sectionLoader").show();
            setTimeout(function () {
                $.ajax({
                    type: ($("#plan_id").val() == '') ? 'POST' : 'PUT',
                    url: ($("#plan_id").val() == '') ? APP_URL + '/plan' : APP_URL + '/plan/'+$("#plan_id").val(),
                    data: $(form).serialize(),
                    async: false,
                    dataType: 'Json',
                    success: function (response) {
                        if (response.status == 'success') {
                            $("#sectionLoader").hide();
                            Swal.fire({
                                position: 'center',
                                icon: 'success',
                                title: "Success",
                                text: response.message,
                                showConfirmButton: false,
                                timer: 2000
                            })
                            window.location = APP_URL + '/plans';
                        } else {
                            $("#sectionLoader").hide();
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
                                timer: 2000
                            })
                        }
                    },
                    error: function (e) {
                        console.log(e);
                    }
                });
            }, 2000);    
        }
    });
    $("#vessel_name").keyup(function () {
        var keyword = $.trim($("#vessel_name").val());
        if (keyword != '') {
            $("#vessel_name").css("background", "#FFF url('" + APP_URL + "/custom/images/mini-loader.gif') no-repeat 165px");
            setTimeout(function () {
                $.ajax({
                    type: "GET",
                    url: APP_URL + "/vessel/sorted-list/"+ keyword,
                    async: false,
                    success: function (response) {
                        if (response.status == 'success') {
                            var content = '<ul id="vessel_lists">';
                            for (var i in response.result) {
                                content += '<li data-id="' + response.result[i].id + '">' + response.result[i].name + '</li>';
                            }
                            content += '</ul>';
                            $("#suggesstion-box").html(content);
                            $("#suggesstion-box").show();
                            $("#vessel_name").css("background", "#FFF");
                        } else {
                            $("#suggesstion-box").html('');
                            $("#suggesstion-box").hide();
                            $("#vessel_name").css("background", "#FFF");
                        }
                    }
                });
            }, 500);    
        } else {
            $("#suggesstion-box").html('');
            $("#suggesstion-box").hide();
            $("#vessel_name").css("background", "#FFF");
        }
    });
});
$(document).on('click', '.add_plan_details', function () {
    destroySelect2();
    var el = $(this);
    var cloneRow = $('.planDetailTr:last').clone(true);
    cloneRow.find('.selCustomer').val('');
    cloneRow.find('.selPlot').val('');
    cloneRow.find('.hdnPlanDetailId').val('');
    $(el).closest('.planDetailTr').after(cloneRow);
    reinitializeSelect2();
    resetPlanDetailFieldNames();
});
$(document).on('click', '.remove_plan_details', function () {
    if ($(".planDetailTr").length > 1) {
        var el = $(this);
        if (el.closest('tr').find('.hdnPlanDetailId').val() !== '') {
            Swal.fire({
                title: 'Are you sure?',
        text: "You want to delete this customer?",
        icon: 'warning',
        showCancelButton: true,
        
        cancelButtonText: '<i title="Cancel"  class="fas fa-3x fa-times-circle tooltips text-danger"></i>',
        confirmButtonText: '<i title="Delete Customer"class="fas fa-3x fa-check-circle tooltips text-success"></i>',
            }).then((result) => {
                if (result.isConfirmed) {
                    el.closest('tr').remove();
                    resetPlanDetailFieldNames();
                }
            });
        } else {
            el.closest('tr').remove();
            resetPlanDetailFieldNames();
        }
    } else {
        Swal.fire({
            position: 'center',
            icon: 'error',
            title: 'There should be atlease 1 customer and plot associated with this plan',
            showConfirmButton: false,
            timer: 2000
        });
    }
});
function resetPlanDetailFieldNames() {
    $.each($('.planDetailTr'), function (i) {
        var el = $(this);
        el.find('.selCustomer').attr('name', 'plan_details[' + i + '][consignee_id]');
        el.find('.selPlot').attr('name', 'plan_details[' + i + '][destination_id]');
        el.find('.hdnPlanDetailId').attr('name', 'plan_details[' + i + '][id]');
    });
}
function destroySelect2() {
    var $select = $('#planDetailTbl .select2').select2();
    $select.each(function (i, item) {
        $(item).select2("destroy");
    });
}
function reinitializeSelect2() {
    var $select = $('#planDetailTbl .select2').select2();
    $select.each(function (i, item) {
        $(item).select2();
    });
}

$(document).on('click', '#vessel_lists li',function(){
    $("#vessel_name").val($(this).text());
    $("#suggesstion-box").html('');
    $("#suggesstion-box").hide();
    $("#vessel_name").css("background", "#FFF");
    return false;
})