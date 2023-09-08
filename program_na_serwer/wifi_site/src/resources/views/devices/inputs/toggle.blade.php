@if($detail->type == 'on|off')
    @php($option1 = 'On')
    @php($option2 = 'Off')
@else
    @php($option1 = 'Show')
    @php($option2 = 'Hide')
@endif
<div class="toggle-modern center-block">
        <div id="toggle{{ $detail->optionId }}"
             data-toggle-on="{{ $detail->value }}"
             data-option-reference="option_{{ $detail->optionId }}">
        </div>
</div>
<input type="hidden" id="option_{{ $detail->optionId }}" class="option-value"
       name="option[{{ $detail->optionId }}]"
       value="{{ $detail->value }}">

<script type="text/javascript">
    $(document).ready(function () {
        var onToggle = function (e, active) {
            var optionId = $(this).attr('data-option-reference');
            if (active) {
                $('#' + optionId).val(1);
            } else {
                $('#' + optionId).val(0);
            }
        };
        var toggle{{ $detail->optionId }} = $('#toggle{{ $detail->optionId }}').toggles({type: 'compact', 'text': {'on': '{{$option1}}', 'off': '{{$option2}}'}}).on('toggle', onToggle).data('toggles');
        $('#option_{{ $detail->optionId }}').change(function () {
            console.log('trigger', $(this).val());
            var value = parseInt($(this).val());
            var toggleValue = true;
            if (value == 0) {
                toggleValue = false;
            }
            toggle{{ $detail->optionId }}.toggle(toggleValue, true, false);
            console.log($(this).val());
        });
    });
</script>
