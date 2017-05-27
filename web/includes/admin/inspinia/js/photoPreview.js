jQuery(function($) {
/// определение jQuery - начало


    //объявлем 2 переменных в которые будет помещён массив файлов после выбора в поле <input type="file">

    var otzivi_files;
    var otzivi_files_tmp = [];

    function prepareUpload(event) {
        /*
         одна переменная временная
         в неё заносится список полученные сразу после выбора файлов
         вторая содержит весь список включая и файлы выбранные ранее
         */
        // присвоение только что выбранных файлов
        otzivi_files = event.target.files;


        // обновление общего массива файлов
        $(otzivi_files).each(function () {
            otzivi_files_tmp = otzivi_files_tmp.concat(this);
        });

        // перерисовка списка после выбора дополнительных файлов
        $(function () {
            repaint_uplod_items();
        });
    }


    /*
     эта функция отслеживает изменение поля <input type="file">
     и добавляет в общий массив выбранные файлы посредством обратного вызова
     */
    $('input[type=file]').on('change', prepareUpload);

    function repaint_uplod_items() {
        /*
         этот код помещён в функцию
         так как:
         после удаления элемента, происходит изменение индексов в массиве otzivi_files_tmp
         у кнопки "удалить" остаются старые индексы
         в этом случае уже нельзя правильно удалить элементы кнопками
         из-за нарушения первоначальных индексов
         поэтому, после удаления элемента, следует перерисовать их список заново.
         */
        // выбираем контейнер для списка
        var fileList = $('.fileList');

        // очищаем от предыдущего кода
        $(fileList).html('');
        // формируем визуальную таблицу из массива файлов
        var table = '<table align="center" width = "100%" border = "0" cellspacing = "0" cellpadding = "0">';
        for (var i=0; i < otzivi_files_tmp.length; i++) {

            // устанавливаем ссылку на текущий объект

            var this_obj = otzivi_files_tmp[i];
            //console.log(otzivi_files_tmp[0]);
            // получаем ссылку на картинку для использования в img src
            try {

                /*
                 из-за бага, в сафари этот метод не работает
                 поэтому в случае неудачи, нужно пропустить этот шаг
                 ссылка на баг https://bugs.webkit.org/show_bug.cgi?id=101671
                 пример, аналогичного скрипта,
                 который тоже не работает в сафари http://blueimp.github.io/jQuery-File-Upload/
                 */

                img_src = window.URL.createObjectURL(this_obj);
            } catch (e) {
            }

            // формируем таблицу
            // js не поддерживает переносы строки, поэтому пришлось вот так её рисовать

            table += '<tr>';
            table += '<td width="120">';
            table += '<div class="image_thummb">';
            if ('undefined' != typeof img_src) table += '<img src="' + img_src + '" width="100" alt="" border="0" />';
            table += '</div><br>';
            table += '</td>';
            table += '<td>' + this_obj.name + '<br /> '+kilob(this_obj.size)+' </td>';
            table += '<td width="100"><a class="del_uplod_image btn btn-danger" href="#' + i + '">удалить</a></td>';
            table += '</tr>';
            if ('undefined' != typeof img_src) img_src.onload = function () {
                window.URL.revokeObjectURL(img_src);
            } // освобождаем память выделенную под картинку

        }
        table += '</table>';
        $(fileList).html(table); // выгружаем в контейнер созданную визуальную таблицу
        if (this_obj) {
            $('.btn-file').css('display', 'none');
        }
    }


    /*
     функция которая удаляет из массива элементы
     после этого наглядно показывает удаление элемента из списка fadeOut()
     и перерисовывает таблицу выбранных файлов
     использование $('body').on('click' ... обусловлено тем,
     что ссылки эти создаются динамически на странице, что делает невозможным использование обычного click()
     */
    $('body').on('click', 'a.del_uplod_image', function () {
        if (!confirm('удалить?'))return (false); // переспрашиваем, удалить ли файл из списка
        var to_del_id = $(this).attr('href').replace('#', '') * 1; // получаем индекс удаляемого файла
        if ('undefined' != typeof otzivi_files_tmp && otzivi_files_tmp.length) {
            // проверяем массив и элемент массива на существование
            if ('undefined' != typeof otzivi_files_tmp[to_del_id] && otzivi_files_tmp[to_del_id]) {
                otzivi_files_tmp.splice(to_del_id, 1); // удаляем элемент из массива
                // создаём визуализацию удаления. а затем перерисовываем таблицу обратной функцией
                // если этого не сделать то индексы у кнопок удалить не будут соответствовать индекса массива.
                $(this).parent().parent().fadeOut(function () {
                    repaint_uplod_items();
                    $('.btn-file').css('display', '');
                    $('input[type=file]').val('');
                });
            }
        }
        return (false);
    });
// функция округления байтов
    function kilob(val)
    {
        if (val<=1024) 					return(number_format(val,0, '.', ' ')+' б');
        if (val>=1023 && val<1048576) 	return(number_format(val/1024,0, '.', ' ')+' Кб');
        if (val>=1048576) 				return(number_format(val/1048576,1, '.', ' ')+' Мб');
    }


    // функция  number_format алаогичная php функции
    function number_format( number, decimals, dec_point, thousands_sep ) {
        // использование number_format(1234.5678, 2, '.', '');
        var i, j, kw, kd, km;
        if( isNaN(decimals = Math.abs(decimals)) ){
            decimals = 2;
        }
        if( dec_point == undefined )
        {
            dec_point = ",";
        }
        if( thousands_sep == undefined )
        {
            thousands_sep = ".";
        }

        i = parseInt(number = (+number || 0).toFixed(decimals)) + "";

        if( (j = i.length) > 3 ){
            j = j % 3;
        } else{
            j = 0;
        }
        km = (j ? i.substr(0, j) + thousands_sep : "");
        kw = i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands_sep);
        //kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).slice(2) : "");
        kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).replace(/-/, 0).slice(2) : "");
        return km + kw + kd;
    }

});
