'use strict';

require('imports?this=>window,define=>false,jQuery=jquery!cropper');
require('jquery-form');
require('imports?jQuery=jquery!jquery-file-upload');
require('imports?this=>window,define=>false!bootstrap-datepicker');
require('imports?jQuery=jquery!bootstrap-datepicker/dist/locales/bootstrap-datepicker.ru.min.js');
require('cropper/dist/cropper.css');
require('imports?jQuery=jquery!chosen-jquery/lib/chosen.jquery.js');
// require('codemirror');
// require('summernote');
// $('textarea.form-control').summernote({});

// let showMessage = require('sweetalert');
require('sweetalert/dist/sweetalert.css');
require('../scss/full_entity_admin.scss');


// Chosen Plugin for select
const chosenConfig = {
    '.chosen-select': {},
    '.chosen-select-deselect': {
        allow_single_deselect: true
    },
    '.chosen-select-no-single': {
        disable_search_threshold: 10
    },
    '.chosen-select-no-results': {
        no_results_text: 'Oops, nothing found!'
    },
    '.chosen-select-width': {
        width: "95%"
    }
};

$(document).ready(function() {
    Object.keys(chosenConfig).forEach(function(selector) {
        $(selector).chosen($.extend({}, chosenConfig[selector], {
            placeholder_text: "Выбрать..."
        }));
    });
    // $('.input-group.date').datepicker({
    //     todayBtn: "linked",
    //     keyboardNavigation: false,
    //     forceParse: false,
    //     autoclose: true
    // });
    var $image = $(".image-crop > img"),
        $previewImage = $("#previewPictureBlock").find("img");
    $($image).cropper({
        preview: ".img-preview",
        restore: true,
        responsive: true,
        zoomable: true,
        zoomOnWheel: false,
        wheelZoomRatio: 0,
        done: function(data) {
            //этой функцией можно забрать url изображения
            //$(".cropped-input").val($image.cropper("getDataURL"));
        }
    });

    $("#zoomIn").click(function() {
        $image.cropper("zoom", 0.1);
    });

    $("#zoomOut").click(function() {
        $image.cropper("zoom", -0.1);
    });

    $("#rotateLeft").click(function() {
        $image.cropper("rotate", -45);
    });

    $("#rotateRight").click(function() {
        $image.cropper("rotate", 45);
    });
    $(".aspect-ratio").click(function() {
        var aspectRatio = $(this).data("type");
        $image.cropper("setAspectRatio", parseFloat(aspectRatio));
    });
    $("#deletePreview").click(function() {
        $previewImage.attr("src", "/includes/admin/img/no_image.jpg");
        $(".photo-file").val('');
    });

    $("#change").click(function() {
        $previewImage.attr("src", $image.cropper('getCroppedCanvas').toDataURL('image/png'));
        $(".photo-file").val($image.cropper('getCroppedCanvas').toDataURL('image/png'));
    });
    var $inputImage = $("#inputImage");

    var $userMainInputImage = $('#inputMainImage');
    var $userCroppedInputImage = $('#inputCroppedImage');
    var $inputPreviewImage = $('#filepreview');


    $("#deleteMainImage").click(function() {
        $('#mainPreviewPictureBlock>img').attr("src", "/includes/admin/img/no_image.jpg");
        $("#mainImage").val('');
    });

    $("#deleteCroppedImage").click(function() {
        $('#mainPreviewPictureBlock>img').attr("src", "/includes/admin/img/no_image.jpg");
        $("#croppedImage").val('');
    });


    if (window.FileReader) {

        $userMainInputImage.change(function() {
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
                    $userMainInputImage.val("");
                    $("#mainImage").val(this.result);
                    // set preview image
                    $('#mainPreviewPictureBlock>img').attr("src", this.result);
                };
            } else {
                showMessage("Please choose an image file.");
            }
        });



        $userCroppedInputImage.change(function() {
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
                    $userCroppedInputImage.val("");
                    $("#croppedImage").val(this.result);
                    // set preview image
                    $('#croppedPreviewPictureBlock>img').attr("src", this.result);
                };
            } else {
                showMessage("Please choose an image file.");
            }
        });




        $inputPreviewImage.change(function() {
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
                    $userCroppedInputImage.val("");
                    $(".previewInput").val(this.result);
                    // set preview image
                    $('#previewCustomPicture>img').attr("src", this.result);
                    $('#previewCustom').show();
                };
            } else {
                showMessage("Please choose an image file.");
            }
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
                    $(".photo-file").val(this.result);
                    // set preview image
                    $previewImage.attr("src", this.result);
                    $(".photo-hide").show();
                };
            } else {
                showMessage("Please choose an image file.");
            }
        });

        $('.attachments').on('change', '.attachPhoto', function() {
            var container = $(this).parents('.imgattach');
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
                    container.find(".photo-file").val(this.result);
                    // set preview image
                    container.find('img').attr("src", this.result)
                };
            } else {
                showMessage("Please choose an image file.");
            }
        });
    } else {
        $inputImage.addClass("hide");
    }
    $("#download").click(function() {
        window.open($image.cropper("getDataURL"));
    });

    // $("#deleteupload").uploadFile({
    //     url: "upload.php",
    //     dragDrop: true,
    //     fileName: "myfile",
    //     returnType: "json",
    //     showDelete: true,
    //     showDownload: true,
    //     showPreview: true,
    //     previewHeight: "100px",
    //     previewWidth: "100px",
    //     deleteCallback: function(data, pd) {
    //         for (var i = 0; i < data.length; i++) {
    //             $.post("delete.php", {
    //                     op: "delete",
    //                     name: data[i]
    //                 },
    //                 function(resp, textStatus, jqXHR) {
    //                     //Show Message
    //                     alert("File Deleted");
    //                 });
    //         }
    //         pd.statusbar.hide(); //You choice.
    //
    //     },
    //     downloadCallback: function(filename, pd) {
    //         location.href = "download.php?filename=" + filename;
    //     }
    // });

    var $selctorMeta = $('.meta');
    arrayCollection($selctorMeta);
    var $selctorCity = $('.city');
    arrayCollection($selctorCity);
    $('#adminbundle_series_citiesId').remove();

    function arrayCollection($collectionHolder) {
        // Get the data-prototype explained earlier
        var prototype = $collectionHolder.data('prototype');

        if (prototype != undefined) {
            $collectionHolder.data('index', $collectionHolder.find(':input').length);
            var index = $collectionHolder.data('index');

            var newForm = prototype.replace(/__name__/g, index);

            // increase the index with one for the next item
            $collectionHolder.data('index', index + 1);

            // Display the form in the page in an li, before the "Add a tag" link li
            $collectionHolder.append(newForm);
            $(".chosen-select").chosen({
                placeholder_text: "Выбрать..."
            });
        }
    }

    $('.gns-datepicker').datepicker({
        keyboardNavigation: false,
        forceParse: false,
        autoclose: true,
        language: 'ru',
        format: "dd-mm-yyyy"
    });

    formBuilder.init();

    var formType = formBuilder.checkType('.bottleType');
    if (formBuilder.checkNew('.bottleType')) {
        if (formType == 'video') {
            $('.previewImageRow').hide();
            $('.previewRow').show();
            $('.previewCustom').hide();
        } else {
            $('.previewRow').hide();
            $('.previewImageRow').show();
            $('.previewCustom').show();
        }
    }


    $('.bottleType').on('change', function() {
        formType = formBuilder.checkType(this);
        $('.previewInput').val('');
        if (formType == 'video') {
            $('.previewImageRow').hide();
            $('.previewRow').show();
            $('.previewCustom').hide();
        } else {
            $('.previewCustom').show();
            $('.previewRow').hide();
            $('.previewImageRow').show();
        }
    })



    $('#adminbundle_users_role').change(function() {
        if ($(this).val() != '') {
            if ($('#adminbundle_users_role option:selected').text() == 'Ведущий') {
                $('#adminbundle_users_isHomePage').closest('.form-group.row').removeClass('hidden');
            } else {
                $('#adminbundle_users_isHomePage').closest('.form-group.row').addClass('hidden');
            }
            $('input[type=password]').parent().parent().removeClass('hidden');
        } else {
            $('input[type=password]').parent().parent().addClass('hidden');
        }
    })

});
$("body").on('click', '.deletePreview', function() {
    var container = $(this).parents('.imgattach');
    container.find(".photo-file").val('');
    // set preview image
    container.find('img').attr("src", "/includes/admin/img/no_image.jpg")
});

$("body").on('click', '.deleteRow', function() {
    var container = $(this).parents('.attachments-block').remove();
});


var formBuilder = (function(doc) {
    var attachments = $('.attachments');
    var $addTagLink = $('<div class="form-group col-md-12"><div class="col-md-4"><a href="#" class="add_tag_link">Добавить вложение</a></div></div>');


    function checkNew() {
        return attachments.data('new') == 1 ? true : false
    }

    function isNewGlobal(selector) {
        return $(selector).data('new') == 1 ? true : false
    }

    function checkType(selector) {
        if (!selector) {
            selector = '.entityType';
        }
        var type = $(selector).val();
        return type;
    }

    function addSelectorEvent() {
        $('.entityType').on('change', function() {
            var type = $(this).val();
            attachments.html('');
            $('.add_tag_link').parents('.form-group').remove()
            prepareForm(type);
        })
    }


    function prepareForm(type) {

        var prototype = '';
        switch (type) {
            case 'video':
                prototype = attachments.data('videoPrototype');
                break;
            case 'image':
                prototype = attachments.data('imagePrototype');
                // creareAttachment(attachments, $newLinkLi);
                break;
            case 'gallery':
                prototype = attachments.data('galleryPrototype');
                attachments.after($addTagLink);
                break;

        }
        creareAttachment(prototype, type);


    }

    function addLinkEvent() {
        $('body').on('click', '.add_tag_link', function(e) {
            e.preventDefault();
            // add a new tag form (see next code block)
            formBuilder.addTagForm(attachments.data('galleryPrototype'), checkType());
        });
    }


    function creareAttachment(includeform, type) {
        // Get the data-prototype explained earlier
        attachments.append(includeform);
    }

    return {
        addTagForm: creareAttachment,
        init: function() {
            if (checkNew()) {
                prepareForm(checkType());
            }
            addLinkEvent();
            addSelectorEvent();
        },
        checkType: checkType,
        checkNew: isNewGlobal
    }
})(document);