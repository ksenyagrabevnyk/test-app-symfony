//$(document).ready(function(){
//    $('#btn-new-post').on('click', function(){
//        $('.content-box').toggle();
//        if($('.content-box').show()){
//            $(".content-view").html('');
//            $(".content-box.ibox-title").html('');
//        }
//        var officer_id = $(this).attr('data_officer_id');
//        var user_id = $(this).attr('data_user_id');
//        var $newpost = $(this).closest('form#new-post');
//        var newpost = $newpost;
//        console.log(officer_id, user_id)
//        $.post("/"+officer_id+"/officer-informations", {id: officer_id}, function(newpost){
//            if(newpost){
//                console.log(newpost);
//                $(".ibox-title.new").html('<strong>Написать новую статью</strong>');
//                var content_view = $('.content-view');
//                $(content_view).html(newpost);
//            };
//        });
//    });
//});