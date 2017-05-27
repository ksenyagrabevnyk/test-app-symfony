$(document).ready(function() {
    $('body').on('click', 'a.del-im-aj', function () {
    //$('.del-im-aj').click(function() {
       var entity_id = $(this).attr("data_id");
        var url = $(this).attr("data_url");
        $.post("/admin/"+url+"/image-ajax-delete", {id: entity_id}, function(data){
            if ( data == 'ok'){
                var fileList = $('.fileList');
                $(fileList).html('');
            }
        });
    });
});