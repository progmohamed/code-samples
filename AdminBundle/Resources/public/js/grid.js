jQuery(function($) {
    // $('a.deleteRow').click(function(e){
    //     e.preventDefault();
    //     var url = $(this).attr('url');
    //     bootbox.confirm("هل أنت متأكد؟", function(result) {
    //         if(result) {
    //             window.location.href = url;
    //         }
    //     });
    // });

    $('.gridForm').submit(function(e) {
        var url = $(this).attr('action');
        var idArray = [];
        var action = $(this).find('[name=action]').val();
        var idx = $(this).find('[name="idx[]"]').each(function() {
            if($(this).is(':checked')) {
                idArray.push($(this).val());
            }
        });
        if(0 == idArray.length) {
            e.preventDefault();
            bootbox.alert(" {{ 'admin.messages.select_some_items_first'|trans}} ");
            return false;
        }else{
            var ids = idArray.join(',');
            window.location.href = url + '?action='+action+'&ids=' + ids;
            return false;
        }
    });
});
