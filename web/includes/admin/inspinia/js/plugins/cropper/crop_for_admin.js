var $image = $(".image-crop > img"),
    $previewImage = $(".img-preview");

$($image).cropper({
    preview: $previewImage,
    restore: true,
    responsive: true,
    zoomable: true,
    zoomOnWheel: false,
    wheelZoomRatio: 0,
    aspectRatio: 1/1,
    done: function(data) {
        //этой функцией можно забрать url изображения
        //$(".cropped-input").val($image.cropper("getDataURL"));
    }
});

var $inputImage = $("#inputImage");
$("#crop").click(function() {
    // window.open($image.cropper("getDataURL"));
    // var dataURL = $image.cropper("getDataURL");
    // dataURL.replace(/^data:image\/(png|jpg);base64,/, "");

    var data = {'image': $image.cropper("getDataURL")};
    // var inputImage = $('.photo-file').val('sdsdsd');
    //
    // console.log(data);
    $.ajax({
        type:"POST",
        url: "/admin/products/save_img",
        data: data,
        success: function(result){
            console.log(result);
            $('#adminbundle_products_crop_img_path').val(result.image_path);
            swal({
                title: "",
                text: "",
                timer: 1000,
                type: "success",
                showConfirmButton: false
            })
        }
    });
});


$("#deletePreview").click(function() {
    $previewImage.attr("src", '');
    $(".photo-hide").hide();
});

$inputImage.change(function() {
    var fileReader = new FileReader(),
        files = this.files,
        file;

    if (!files.length) {
        return;
    }

    file = files[0];

    if (/^image\/\w+$/.test(file.type)) {
        fileReader.readAsDataURL(file);
        fileReader.onload = function() {
            $inputImage.val("");
            $image.cropper("reset", true).cropper("replace", this.result);
            // set preview image
            $previewImage.attr("src", this.result);
            $(".photo-hide").show();
        };
    } else {
        showMessage("Please choose an image file.");
    }
});