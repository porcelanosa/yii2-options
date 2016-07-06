var options = document.getElementById('options-list');
var selectedOptions = document.getElementById('selected-options');

var sortableOptions = Sortable.create(options,
    {
        group: {
            name: "options",
            pull: false,//'clone',
            put: false,
        },
        sort: false,
        disabled: false, // Disables the sortable if set to true.
    }
);


var sortableSelectedOptions = Sortable.create(selectedOptions,
    {
        group: {
            name: "selectedOptions",
            pull: 'clone',
            put: ['options'],
        },
        sort: true,
        //disabled: false, // Disables the sortable if set to true.
        onAdd: function (/**Event*/evt) {
            var itemEl = evt.item;  // dragged HTMLElement
            //var itemContent = $(itemEl).html();
            /*$(itemEl).html(itemContent + '&nbsp;<i class="js-remove">✖</i>');*/
            vmOptionsCatsList.saveCatsOptionsList(sortableSelectedOptions.toArray());
            itemEl.parentNode.removeChild(itemEl);
        },
        onSort: function (evt) {
            //vmOptionsCatsList.saveCatsOptionsList(sortableSelectedCats.toArray(), sortableSelectedOptions.toArray());
            //console.log(sortableSelectedOptions.toArray());
            
        },
        filter: '.js-remove',
        onFilter: function (evt) {
            var el = sortableSelectedOptions.closest(evt.item); // get dragged item
            el && el.parentNode.removeChild(el);
        }
    }
);
