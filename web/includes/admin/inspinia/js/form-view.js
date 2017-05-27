$(document).ready(function(){
    $('#btn-new-review').on('click', function(){
        $('.content-box').toggle();
        if($('.content-box').show()){
            $(".content-view").html('');
            $(".content-box.ibox-title").html('');
        }
        var officer_id = $(this).attr('data_officer_id');
        var user_id = $(this).attr('data_user_id');
        //var $form = $(this).closest('form#new-review');
        //var form = $form;
        console.log(officer_id, user_id)
        $.ajax({
            type: "POST",
            url: "/"+officer_id+"/officer-information-review",
            data: {id: officer_id },
            success: function (responce) {
                responce = JSON.parse(responce);
                console.log(responce);
                $(".ibox-title.new").html(responce.title);
                $(".content-view").html(responce.form);
            }
        })
    });
    $('#btn-new-post').on('click', function(){
        $('.content-box').toggle();
        if($('.content-box').show()){
            $(".content-view").html('');
            $(".content-box.ibox-title").html('');
        }
        var officer_id = $(this).attr('data_officer_id');
        var user_id = $(this).attr('data_user_id');
        //console.log(officer_id, user_id);
        $.ajax({
            type: "POST",
            url: "/"+officer_id+"/officer-information-post",
            data: {id: officer_id },
            success: function (responce) {
                responce = JSON.parse(responce);
                console.log(responce);
                $(".ibox-title.new").html(responce.title);
                $(".content-view").html(responce.form);
            }
        })
    });
    $('#btn-review').on('click', function(){
        $('.content-box').toggle();
        if($('.content-box').show()){
            $(".content-view").html('');
            $(".content-box.ibox-title").html('');
        }
        var officer_id = $(this).attr('data_officer_id');
        var user_id = $(this).attr('data_user_id');
        $.ajax({
            type: "POST",
            url: "/"+officer_id+"/view-review",
            data: {id: officer_id },
            success: function (responce) {
                responce = JSON.parse(responce);
                console.log(responce);
                $(".ibox-title.new").html(responce.title);
                $(".content-view").html(responce.form);
            }
        })
    });
});
