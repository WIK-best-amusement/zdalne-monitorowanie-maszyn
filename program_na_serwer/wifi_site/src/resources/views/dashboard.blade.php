@extends('layouts.app')

@section('content')
    @if (Session::get('dontHavePermission'))
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-danger">
                    <h4>Permissions!</h4>
                    <p>{{ Session::get('dontHavePermission') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-md-4">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Members</h3>
                </div>
                <div class="box-body no-padding">
                    <ul class="users-list clearfix">
                        <?php $i = 0;?>
                        @foreach($members as $member)
                            @if ($i == 8)
                                @break
                            @endif

                            <li>
                                <img alt="User Gravatar" class="user-image" src="{{ Gravatar::src($member->email, 64) }}">
                                <span class="users-list-name" href="#">{{ $member->name }}</span>
                            </li>
                            <?php $i++;?>
                        @endforeach
                    </ul>
                </div>
                <div class="box-footer text-center">
                    <a href="{{ url('team/list') }}" class="uppercase">View All Users</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Teams where You belong</h3>
                </div>
                <div class="box-body no-padding">
                    <ul class="users-list clearfix">
                        <?php $i = 0;?>
                        @foreach($userTeams as $team)
                            @if ($i == 8)
                                @break
                            @endif
                            <li>
                                <a href="{{ URL::route('change-user-team', $team->id) }}">
                                    <img alt="User Gravatar" class="user-image" src="{{ Gravatar::src($team->name, 64) }}">
                                    <span class="users-list-name" href="#">{{ $team->name }}</span>
                                </a>
                            </li>
                            <?php $i++;?>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Errors</h3>
                </div>
                <div class="box-body no-padding">
                    <canvas id="myChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="box box-danger">
                <div class="box-header">
                    <h3 class="box-title">Devices errors</h3>
                </div>
                <div class="box-body dataTables_wrapper form-inline dt-bootstrap">
                    <div class="row">
                        <div class="col-sm-12">
                            <table style="width: 100%" id="report" class="table table-bordered table-striped dataTable"
                                   role="grid">
                                <thead>
                                <tr>
                                    <th>Nr</th>
                                    <th>Date</th>
                                    <th>Device</th>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Read</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $i = 1;
                                foreach ($deviceProblems as $key => $row) {
                                    echo '<tr><td>' . $i . '</td>';
                                    echo '<td>' . $row->date . '</td>';
                                    echo '<td>' . $row->name . '</td>';
                                    echo '<td>' . $row->title . '</td>';
                                    echo '<td>' . $row->description . '</td>';
                                    echo '<td><a href="javascript:void(0)" data-deviceId="' . $row->device_id . '" data-id="' . $row->id . '" class="mark-as-read glyphicon glyphicon-ok"></a></td>';
                                    echo '</tr>';
                                    $i++;
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('view.scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.13.0/moment.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.css">
    <script>
        var ctx = document.getElementById('myChart').getContext('2d');
        var timeFormat = 'YYYY-MM-DD';

        function newDate(date) {
            return moment(date).toDate();
        }


        var config = {
            type: 'line',
            data: {
                labels: [
                    <?php
                    foreach($deviceProblemsByDay as $item) {
                        echo "newDate('$item->day'),";
                    }
                    ?>
                ],
                datasets: [{
                    label: 'Errors count',
                    fill: false,
                    data: [
                        <?php
                        foreach($deviceProblemsByDay as $item) {
                            echo $item->count.',';
                        }
                        ?>
                    ],
                }]
            },
            options: {
                title: {
                    text: 'Chart.js Time Scale'
                },
                scales: {
                    xAxes: [{
                        type: 'time',
                        time: {
                            parser: timeFormat,
                            tooltipFormat: 'll HH:mm'
                        },
                        scaleLabel: {
                            display: true,
                            labelString: 'Date'
                        }
                    }],
                    yAxes: [{
                        scaleLabel: {
                            display: true,
                            labelString: 'value'
                        }
                    }]
                },
            }
        };

        window.myLine = new Chart(ctx, config);
    </script>
    <script src="{{ URL::asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('plugins/datatables/dataTables.bootstrap.min.js') }}"></script>

    <script src="{{ URL::asset('plugins/datepicker/bootstrap-datepicker.js') }}"></script>
    <link rel="stylesheet" href="{{ URL::asset('plugins/datepicker/datepicker3.css') }}">

    <script>
        $(".mark-as-read").click(function () {
            var item = $(this);
            var id = item.attr('data-id');
            var deviceId = $(this).attr('data-deviceId');
            $.post('devices/problems/' + id + '/' + deviceId + '/read/json', {
                "_token": "{{ csrf_token() }}",
                data: {
                    id: id
                }
            }).done(function () {
                $('#report').DataTable().row( item.closest('tr') )
                    .remove()
                    .draw();
            }).fail(function () {

            });
        });

        $("#report").DataTable({
            "searching": false,
            "pageLength": 4,
            "order": [[ 1, "desc" ]],
            "bLengthChange": false,
        });
    </script>
@endsection
