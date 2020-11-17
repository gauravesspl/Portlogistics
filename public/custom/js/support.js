// Ajax Call
function customAjaxCall(url,data,actionFunction,errorfunction,type)
{
    $.ajax(
    {
        url: url,
        data: data,
        type: type,
        dataType: "json",
        success: function(output)
        {
            if (output.redirect)
            {
                window.location.href = output.redirect;
            }
            else
            {
                actionFunction(output);
            }
        },
        error:function(error)
        {
            errorfunction(error);
        }
    });
}
