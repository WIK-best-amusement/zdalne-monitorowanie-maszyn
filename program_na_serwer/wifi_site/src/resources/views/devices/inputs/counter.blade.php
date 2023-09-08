@php($length = strlen($detail->max))
@if($detail->min > $detail->max)
    @php($length = strlen($detail->min))
@endif

<input type="number"
       readonly
       id="option_{{ $detail->optionId }}"
       name="option[{{ $detail->optionId }}]"
       class="integer form-control text-center option-value counter"
       placeholder="{{ $detail->value }}"
       value="{{ (int)$detail->value }}">
<button type="button" value="reset" id="reset_{{ $detail->optionId }}" class="btn btn-primary"><span
            class="fa fa-refresh"></span></button>

<script type="text/javascript">
    $(document).ready(function () {
        $('#reset_{{ $detail->optionId }}').click(function () {
            $("#option_{{ $detail->optionId }}").val(0);
        });
    });
</script>
