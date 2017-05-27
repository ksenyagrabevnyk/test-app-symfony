(function($){

    // make nice checkboxes
    function renderCheckboxes() {
        $('.i-checks').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green'
        });
    };

    // fill textarea with json menu
    function updateOutput (e) {
        var list = e.length ? e : $(e.target),
            output = list.data('output'),
            structure = list.nestable('serialize'),
            structureJSON = window.JSON.stringify(structure);

        if (!!output) {
            if (window.JSON) {
                output.val(structureJSON);
            } else {
                output.val('JSON browser support required for this demo.');
            }
        }
    };

    // render checkboxes
    renderCheckboxes();

    // foreach menu activate nestable list and create output field
    $('.nestable').each(function(i, el) {
        $(el).nestable().on('change', updateOutput);
        updateOutput($(el).data('output', $(el).parent().find('textarea')));
    });

    // delete nestable item from list
    $('.nestable').on('click', '.dd-action', function(e) {
        e.preventDefault();
        var $nestable = $(e.delegateTarget);
        var $btn = $(this);
        if ($btn.data('action') == 'remove') {
            $btn.closest('.dd-item').remove();
            $nestable.trigger('change');
        }
    });

    // autocomplite in search
    var menu_items = [
        {
            "id": 201,
            "label": "Test1",
            "value": "Test1"
        },
        {
            "id": 202,
            "label": "Test2",
            "value": "Test2"
        },
        {
            "id": 203,
            "label": "Test3",
            "value": "Test3"
        }
    ];
    var searchWidgetInstance = $('input[name=menu-item-search]').autocomplete({
        source: menu_items
    }).data('ui-autocomplete');
    searchWidgetInstance._renderMenu = function(ul, items) {
        var that = this,
            $tab,
            $resultContainer,
            $addButton;

        $tab = $(that.element).closest('.panel-body');
        $resultContainer = $tab.find('ul.todo-list');
        $addButton = $tab.find('button');

        // clear result container
        if (!!$resultContainer)
            $resultContainer.empty();

        //render items
        $.each(items, function(i, el) {
            that._renderItem(ul, el);
        });

        // make nice checkboxes
        renderCheckboxes();

        // toggle 'add' button visibility
        if (!!$addButton) {
            if (items.length > 0) {
                $addButton.removeClass('hide');
            } else {
                $addButton.addClass('hide');
            }
        }
    };
    searchWidgetInstance._renderItem = function(ul, item) {
        var resultContainer,
            input,
            label;

        resultContainer = $(this.element).closest('.panel-body').find('ul.todo-list');

        input = $('<input/>', {
            'type': 'checkbox',
            'name': 'menu-item-' + item.id,
            'value': item.id,
            'class': 'i-checks',
            'data-id': item.id,
            'data-text': item.value
        });
        label = $('<span/>', {
            'class': 'm-l-xs',
            'text': item.value
        });

        return $('<li/>').append(input).append(label).appendTo(resultContainer);
    };


    function MenuItemsFinder (tabSelector) {
        this.$tab = $(tabSelector);
        this.$addButton = this.$tab.find('button');

        this.Init();
    }

    MenuItemsFinder.prototype.Init = function() {
        this.AddEvents();
    };

    MenuItemsFinder.prototype.AddEvents = function() {
        this.$addButton.on('click', this.AddMenuItem.bind(this));
    };

    MenuItemsFinder.prototype.AddMenuItem = function(e) {
        this.$tab.find('input:checked').each(this.Iterate.bind(this));
    };

    MenuItemsFinder.prototype.Iterate = function(index, checkbox) {
        var $checkbox = $(checkbox),
            data = this.getCheckboxData($checkbox),
            tab = this.getCurrentNestableTab(),
            item = this.createNestableItem(data);

        if (!!data.id && !!data.text) {
            this.addNestableItem(tab, item);
        }

        // uncheck added items;
        $checkbox.iCheck('uncheck');
    };

    MenuItemsFinder.prototype.getCheckboxData = function($checkbox) {
        var result = {};

        if (!!$checkbox) {
            result = {
                id: $checkbox.data('id'),
                text: $checkbox.data('text')
            };
        }

        return result;
    };

    MenuItemsFinder.prototype.createNestableItem = function(oData) {
        var $container, $item, $deleteButton;

        $container = $('<li/>', {
            'class': 'dd-item',
            'data-id': oData.id
        });
        $item = $('<div/>', {
            'class': 'dd-handle clear',
            text: oData.id + ' - ' + oData.text
        });
        $deleteButton = $('<a/>', {
            'href': '#',
            'class': 'btn btn-danger btn-sm pull-right dd-action',
            'data-action': 'remove',
            'html': '<i class="fa fa-trash-o "></i>'
        });
        $deleteButton.appendTo($container);
        $item.appendTo($container);

        return $container;
    };

    MenuItemsFinder.prototype.getCurrentNestableTab = function() {
        return $('.menu-creator .tab-pane.active');
    };

    MenuItemsFinder.prototype.addNestableItem = function($tab, $item) {
        var $nestable;
        if (!!$tab) {
            $nestable = $tab.find('.nestable');
            $nestable.find('ol:first').append($item);
            // activate new menu item in nestable workarea
            $nestable.trigger('change');
        }
    };

    var all = new MenuItemsFinder('#tab-pages-all');
    var favourite = new MenuItemsFinder('#tab-pages-favourite');
    var search = new MenuItemsFinder('#tab-pages-search');
})(jQuery);