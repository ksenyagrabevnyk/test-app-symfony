;(function($){
    $.widget('gns.dublicator', {
        addButton: null,
        clones: [],
        index: -1,
        options: {
            'add-button-class': 'btn btn-default m-b',
            'add-button-text': 'Clone',
            'delete-button-class': 'btn btn-default m-b',
            'delete-button-text': 'Delete',
            'element-class': 'dublicator-original',
            'template-name': '-#instance#-#index#'
        },
        _create: function() {
            this._update();
        },
        _update: function() {
            this.createAddButton();

            this._removeEvents();
            this._addEvents();

            this.element.addClass(this.options['element-class']);
            this.element.after(this.addButton);
        },
        _destroy: function() {
            this.element.removeClass(this.options['element-class']);
            this._removeEvents();
        },
        _setOption: function( key, value ) {
            this.options[ key ] = value;
            this._update();
        },
        _removeEvents: function() {
            this.addButton.off('click', this.add.bind(this));
        },
        _addEvents: function() {
            this.addButton.on('click', this.add.bind(this));
        },
        add: function() {
            var that = this;
            var clone = this.element.clone().removeClass(this.options['element-class']);
            var deleteButton = $('<a/>', {'class': this.options['delete-button-class'], text: this.options['delete-button-text']});

            // store our clones
            this.clones.push(clone);
            ++this.index;

            // generate new name attribute
            this.processForm(clone);

            deleteButton.one('click', function() {
                that.clones.splice(that.clones.indexOf(clone), 1);
                clone.remove();
                deleteButton.remove();
            });

            // insert clone with delete button
            this.addButton.before([deleteButton, clone]);
        },
        processForm: function(clone) {
            var that = this,
                count = 0,
                index = this.clones.indexOf(clone);

            this.removeID(clone);
            clone.find('[name]').each(this.processName.bind(this));
        },
        processName: function(i, el) {
            var name = $(el).attr('name');

            name += this.options['template-name'];

            name = name.replace('#index#', this.index);

            if (this.options['template-name'].indexOf('#instance#') >= 0) {
                name = name.replace('#instance#', this.uuid);
            }

            $(el).attr('name', name);
        },
        removeID: function(clone) {
            clone.removeAttr('id');
        },
        createAddButton: function() {
            if (!this.addButton) {
                this.addButton = $('<a/>', {'class':  this.options['add-button-class'], text: this.options['add-button-text']});
            }
        }
    });
}(jQuery));