(function(){

    jQuery(function($) {
        $("#accordion").accordion({
            collapsible: true,
            heightStyle: "content",
            animate: 250,
            header: ".accordion-header"
        }).sortable({
            axis: "y",
            handle: ".accordion-header",
            stop: function(event, ui) {
                ui.item.children(".accordion-header").triggerHandler("focusout");
            }
        });

        var category = {
            'for-sale': {
                text: 'For Sale',
                type: 'folder'
            },
            'vehicles': {
                text: 'Vehicles',
                type: 'item'
            },
            'rentals': {
                text: 'Rentals',
                type: 'item'
            },
            'real-estate': {
                text: 'Real Estate',
                type: 'item'
            },
            'pets': {
                text: 'Pets',
                type: 'item'
            },
            'tickets': {
                text: 'Tickets',
                type: 'item'
            }
        }

        var dataSource1 = function(options, callback) {
            var $data = null
            if (!("text" in options) && !("type" in options)) {
                $data = category; 
                callback({
                    data: $data
                });
                return;
            } else if ("type" in options && options.type == "folder") {
                if ("additionalParameters" in options && "children" in options.additionalParameters)
                    $data = options.additionalParameters.children || {};
                else
                    $data = {} 
            }

            callback({
                data: $data
            })
        }

        $('#toggle-result-page .btn').on('click', function(e) {
            var target = $(this).find('input[type=radio]');
            var which = parseInt(target.val());
            $('.search-page').parent().addClass('hide');
            $('#search-page-' + which).parent().removeClass('hide');
        });
    });
})()