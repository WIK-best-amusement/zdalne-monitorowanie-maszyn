@php($values = explode('|', $detail->values))
<select name="option[{{ $detail->optionId }}]"
        class="select-value select2 select2-hidden-accessible option-value"
        style="width: 100%;" tabindex="-1" aria-hidden="true">
    @foreach($values as $value)
        @php($option = explode('@', $value))
        <option value="{{ $option[1] }}" @if($detail->value == $option[1]) selected="selected"@endif>{{ $option[0] }}</option>
    @endforeach
</select>
