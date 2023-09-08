@extends('layouts.app')
@section('content')
    <div class="wait-for-load">
        <div class="update-failed callout callout-danger" style="display:none;">
            <h4>Value was not updated</h4>
            <p>There was problem with update. Please try once again.</p>
        </div>

        <div class="update-success callout callout-success" style="display:none;">
            <h4>Success!</h4>
            <p>New settings were saved.</p>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <button id="save-all" class="btn btn-primary pull-right">Save settings</button>
            </div>
        </div>

        {{--Main settings--}}
        <div class="row">
            <div class="col-xs-12">
                @include('devices.details-main-settings')
            </div>
        </div>

        @if (count($detailsGrouped) == 0)
            <div class="no-print">
                <div class="callout callout-warning">
                    <h4>Empty data</h4>
                    For selected device there is no data
                </div>
            </div>
        @else
            @include('devices.details-settings')
        @endif

        <div class="row">
            <div class="col-xs-12">
                <a href="{{ URL::previous() }}" class="btn btn-default">Back</a>
            </div>
        </div>
        @endsection

        @section('view.css')
            <link rel="stylesheet" href="{{ URL::asset('plugins/ionslider/ion.rangeSlider.css') }}">
            <link rel="stylesheet" href="{{ URL::asset('plugins/ionslider/ion.rangeSlider.skinNice.css') }}">
            <link rel="stylesheet" href="{{ URL::asset('plugins/number-polyfill/number-polyfill.css') }}">
            <link rel="stylesheet" href="{{ URL::asset('css/toggles-full.css') }}">
        @endsection

        @section('view.scripts')
            <script src="{{ URL::asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
            <script src="{{ URL::asset('plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
            <script src="{{ URL::asset('plugins/number-polyfill/number-polyfill.min.js') }}"></script>
            <script src="{{ URL::asset('plugins/select2/select2.full.min.js') }}"></script>

            <script type="text/javascript">
                $(document).ready(function () {
                    /*
                     Inititalize select2
                     */
                    $("#device-location").select2({placeholder: "Please select a location", allowClear: true});
                    $(".select-value").select2({minimumResultsForSearch: Infinity});
                    /*
                     Save All settings at once
                     */
                    $('#save-all').click(function () {
                        var changedOptions = [];
                        $('.overlay').removeClass('hide');
                        $('#save-all').attr('disabled', true);
                        var form = document.getElementById("options-update").elements;
                        for(var i = 0; i < form.length; i++){
                            var defaultValue = $(form[i]).parents('tr').attr('data-default-value');
                            if (form[i].type != 'button' && defaultValue) {
                                if (form[i].type === 'number') {
                                    defaultValue = parseInt(defaultValue);
                                    var value = parseInt(form[i].value);
                                } else {
                                    var value = form[i].value;
                                }
                                if (defaultValue != value) {
                                    var name = $(form[i]).attr('name');
                                    changedOptions.push(encodeURIComponent(name) + '=' + encodeURIComponent(value));
                                }
                            }
                        }
                        $.post('{{ URL::route('updateDeviceDetails', ['response' => 'json']) }}', {
                            "_token": "{{ csrf_token() }}",
                            data: {
                                device: $('#device-update').serialize(),
                                options: changedOptions.join('&')
                            },
                            'deviceId': $('#device-id').val()
                        }).done(function (json) {
                            $('.reset-value').remove();
                            $('.update-success').show();
                            setTimeout(function () {
                                $(".update-success").hide('blind', {}, 1000);
                            }, 4000);
                            $('.overlay').addClass('hide');
                            $('#save-all').attr('disabled', false);
                            $.each(json.data.updatedSettings, function (idx, data) {
                                var optionValueElement = $('tr td input#option_' + data.pending);
                                var defaultValue = optionValueElement.parents('tr').attr('data-default-value');
                                optionValueElement.val(defaultValue);
                                $('tr td.pending_' + data.pending).html('<span class="badge bg-olive">' + data.value + '</span>');
                                $('tr span.pending_' + data.pending).html('<span class="badge bg-olive">' + data.value + '</span>');
                            });
                        }).fail(function () {
                            $('.reset-value').remove();
                            $('.update-failed').show();
                            $('.overlay').addClass('hide');
                            $('#save-all').attr('disabled', false);
                            setTimeout(function () {
                                $(".update-failed").hide('blind', {}, 500);
                            }, 4000);
                        })
                    });
                    $('.wait-for-load').removeClass('wait-for-load');
                });
            </script>
            @include('devices.details-settings-js')
    </div>
@endsection
