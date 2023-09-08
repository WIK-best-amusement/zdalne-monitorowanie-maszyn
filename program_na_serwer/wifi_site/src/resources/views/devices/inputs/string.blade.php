<input maxlength="20"
       id="option_{{ $detail->optionId }}"
       name="option[{{ $detail->optionId }}]"
       type="text"
       class="form-control text-center option-value"
       placeholder="{{ $detail->value }}"
       value="{{ $detail->value }}">
