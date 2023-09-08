@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">@if($locationId == null)
                            Full list of devices
                        @else
                            Devices in "{{$location->name}}"
                        @endif
                    </h3>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                    <table id="devices-list" class="table table-condensed table-striped" role="grid">
                        <thead>
                        <tr role="row">
                            <th>Nr</th>
                            <th>Name</th>
                            <th>S/N</th>
                            <th>RSSI</th>
                            @if($locationId == null)
                            <th>Location</th>
                            @endif
                            <th class="hidden-xs">Last seen</th>
                            <th class="no-sort">Details</th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php $i = 1;?>
                        @foreach ($devices as $device)
                            <tr role="row">
                                <td>{{ $i }}</td>
                                <td>{{ $device->name }}</td>
                                <td>{{ $device->serial_number }}</td>
                                <td>{{ $device->rssi }}</td>
                                @if($locationId == null)
                                <td>
                                    {{ $device->location_name }}
                                </td>
                                @endif
                                <td class="hidden-xs">{{ (new DateTime($device->last_seen))->format('Y-m-d H:i:s') }}</td>
                                <td class="no-sort"><a href="{{ URL::route('devicesShowDetails', ['id' => $device->id]) }}"
                                       class="btn btn-block btn-primary">Details</a></td>
                            </tr>
                            <?php $i++;?>
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
@endsection

@section('view.scripts')
    <script src="{{ URL::asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('plugins/datatables/dataTables.bootstrap.min.js') }}"></script>

    <script>
        $("#devices-list").DataTable({
            "searching": true,
            "bFilter": false,
            "columnDefs": [ {
                "targets"  : 'no-sort',
                "orderable": false,
            }],
            "pageLength": 100
        });
    </script>

@endsection
