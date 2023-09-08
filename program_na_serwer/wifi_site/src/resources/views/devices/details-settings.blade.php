<div class="box">
    <form class="form-horizontal" id="options-update" method="post">
        <div id="accordion" class="margin-bottom">
            @php($counter = 0)
            @foreach($detailsGrouped as $name => $details)
                <div class="row no-margin" data-location-id="{{ $deviceId }}-{{ $details[0]->groupId }}"
                     data-group-number="{{$counter}}">
                    <div class="col-xs-12 no-padding">
                        <div class="box no-margin">
                            <div class="box-header group-title">
                                <span class="btn btn-box-tool"><span class="accordion-icon fa fa-plus"></span></span>
                                <h3 class="box-title">{{ $name }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row no-margin" data-elements-id="{{ $deviceId }}-{{ $details[0]->groupId }}">
                    <div class="no-padding col-md-12">
                        <div class="box no-border no-margin">
                            <div class="box-body no-padding ">
                                <div id="no-more-tables">
                                    <table class="col-sm-12 device-details table table-bordered table-striped nowrap"
                                           cellspacing="0" width="100%">
                                        <thead class="cf">
                                        <tr>
                                            <th class="nr hidden-xs">Nr</th>
                                            <th>Name</th>
                                            <th class="values">Value</th>
                                            <th class="values">Pending</th>
                                            <th class="values">Monitoring</th>
                                            <th class="date">Updated at</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $rowIndex = 1;?>
                                        @foreach ($details as $detail)
                                            <tr data-option="{{ $detail->optionId }}"
                                                data-default-value="{{ $detail->value }}">
                                                <td data-title="Nr" class="hidden-xs">{{ $rowIndex }}</td>
                                                <td data-title="Name" class="name">{{ $detail->name }}</td>
                                                <td data-title="Value / Pending" @if($detail->type == 'integer') @endif>
                                                    @if($detail->type == 'counter')
                                                        @include('devices.inputs.counter')
                                                    @endif

                                                    @if($detail->type == 'string')
                                                        @include('devices.inputs.string')
                                                    @endif

                                                    @if($detail->type == 'integer')
                                                        @include('devices.inputs.integer')
                                                    @endif

                                                    @if($detail->type == 'on|off' || $detail->type == 'hide|show')
                                                        @include('devices.inputs.toggle')
                                                    @endif

                                                    @if($detail->type == 'pin')
                                                        @include('devices.inputs.pin')
                                                    @endif

                                                    @if($detail->type == 'readonly')
                                                        <input disabled type="text" class="form-control text-center"
                                                               value="{{ $detail->value }}">
                                                    @endif

                                                    @if($detail->type == 'select')
                                                        @include('devices.inputs.select')
                                                    @endif

                                                <span data-title="Pending" class="pending_{{ $detail->optionId }} visible-xs-inline-block xs-pending">
                                                    @if($detail->type == 'select')
                                                        @php($pending = (isset($selectOptions[$detail->optionId][$detail->value_pending])) ? $selectOptions[$detail->optionId][$detail->value_pending] : '')
                                                    @else
                                                        @php($pending = $detail->value_pending)
                                                    @endif
                                                    <span class="badge bg-olive" title="Reset to default">{{ $pending }}</span>
                                                </span>

                                                </td>
                                                <td data-title="Pending" class="pending_{{ $detail->optionId }} hidden-xs">
                                                    @if($detail->type == 'select')
                                                        @php($pending = (isset($selectOptions[$detail->optionId][$detail->value_pending])) ? $selectOptions[$detail->optionId][$detail->value_pending] : '')
                                                    @else
                                                        @php($pending = $detail->value_pending)
                                                    @endif
                                                    <span class="badge bg-olive" title="Reset to default">{{ $pending }}</span>
                                                </td>
                                                <td data-title="Monitoring"><a href="{{ URL::route('settingsHistory', ['id' => $detail->setting_id, 'deviceId' => $deviceId]) }}"><span class="fa fa-history"></span></a></td>
                                                <td data-title="Updated at" id="updated_at_{{ $detail->optionId }}">{{ (new DateTime($detail->updated_at))->format('Y-m-d H:i:s') }}</td>
                                            </tr>
                                            <?php $rowIndex++;?>
                                        @endforeach
                                        </tbody>
                                        <tfoot>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @php($counter++)
            @endforeach
        </div>
    </form>
    <a href="#" class="reset-value text-muted hide" title="Reset to default"><span class="fa fa-times"></span></a>
    <div class="overlay hide">
        <span class="fa fa-refresh fa-spin"></span>
    </div>
</div>
