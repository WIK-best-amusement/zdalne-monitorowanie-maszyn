@php($length = strlen($detail->max))
@if($detail->min > $detail->max)
    @php($length = strlen($detail->min))
@endif

<input type="number"
       step="1"
       min="{{ $detail->min }}"
       max="{{ $detail->max }}"
       id="option_{{ $detail->optionId }}"
       name="option[{{ $detail->optionId }}]"
       class="integer form-control text-center option-value"
       placeholder="{{ $detail->value }}"
       value="{{ (int)$detail->value }}">

<script type="text/javascript">
    $(document).ready(function () {
        $('#option_{{ $detail->optionId }}').on('keydown', function (e) {
            if (e.which == 44 || e.which == 46) {
                return false;
            }
        });
        $('#option_{{ $detail->optionId }}').on('blur', function () {
            var optionValue = parseInt($(this).val().replace(",", "").replace(".", ""));
            var optionMax = parseInt($(this).attr('max'));
            var optionMin = parseInt($(this).attr('min'));

            if (optionValue > optionMax) {
                optionValue = optionMax;
            }

            if (optionValue < optionMin || optionValue == '' || isNaN(optionValue)) {
                optionValue = optionMin;
            }

            $(this).val(optionValue);
        });
    });
</script>
