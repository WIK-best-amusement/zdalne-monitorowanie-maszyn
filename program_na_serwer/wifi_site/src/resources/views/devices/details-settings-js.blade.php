<script src="{{ URL::asset('plugins/ionslider/ion.rangeSlider.min.js') }}"></script>

<script>
    $(document).on('click', '.badge', function () {
        var parent = $(this).parents('tr[data-option]');
        $(this).text('');
        $('<input type="hidden" class="reset-value" name="reset[' + parent.attr('data-option') + ']" value="true">').appendTo(parent);
    });

    $(document).ready(function () {

        $('div[data-location-id]').each(function () {
            var groupElement = this;
            var nextElement = $(groupElement).next('div[data-elements-id]');
            if (nextElement.find('table tbody tr').length == 0) {
                $(groupElement).hide();
                $(nextElement).hide();
            }
        });

        /*
         Accordion settings
         */
        var openedGroup = parseInt($('div[data-location-id=\'' + Cookies.get("expanded-group") + '\']').attr('data-group-number'));
        $("#accordion").accordion({
            active: openedGroup,
            heightStyle: "content",
            collapsible: true,
            create: function (event, ui) {
                $('div.row[aria-expanded=true]').find('.accordion-icon').removeClass('fa-plus').addClass('fa-minus');
            },
            activate: function (event, ui) {
                Cookies.remove("expanded-group");
                $('.accordion-icon').removeClass('fa-minus').removeClass('fa-plus').addClass('fa-plus');
                var expandedElement = $('div.row[aria-expanded=true]');
                if (expandedElement != undefined) {
                    expandedElement.find('.accordion-icon').removeClass('fa-plus').addClass('fa-minus');
                    Cookies.set("expanded-group", $(expandedElement).attr('data-location-id'));
                }
            }
        });

        /*
         Websocket connection
         */
        var conn = new WebSocket('wss://ws.online.wik.pl/ListenForUpdate');
        conn.onopen = function (e) {
            conn.send(JSON.stringify(<?php echo json_encode(['deviceId' => $deviceId])?>));
        };
        conn.onmessage = function (e) {
            var data = JSON.parse(e.data);

            if (data.hasOwnProperty('optionId') && data.hasOwnProperty('value')) {
                var selector = $('tr td input#option_' + data.optionId);
                selector.val(data.value);
                selector.closest('tr[data-default-value]').attr('data-default-value', data.value);
                selector.closest('tr[data-default-value]').val(data.value);
                $('tr td.pending_' + data.optionId + ' span').text('');
                $('tr span.pending_' + data.optionId + ' span').text('');

                if (data.hasOwnProperty('updatedAt') && data.updatedAt !== '') {
                    $('tr td#updated_at_' + data.optionId).text(data.updatedAt);
                }

                selector.trigger("change");
            }
        };
    });
</script>
