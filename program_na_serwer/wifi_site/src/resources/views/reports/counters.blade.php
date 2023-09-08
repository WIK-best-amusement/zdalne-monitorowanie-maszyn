@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <form>
                                <label for="period">Group by:</label>
                                <select name="period" class="form-control input-sm inline" style="width: 100px">
                                    <option value="day" <?php echo $period == 'day' ? 'selected' : ''?>>Days</option>
                                    <option value="week" <?php echo $period == 'week' ? 'selected' : ''?>>Week</option>
                                    <option value="month" <?php echo $period == 'month' ? 'selected' : ''?>>Month
                                    </option>
                                </select>
                                <div class="input-daterange input-group" id="datepicker"
                                                                style="display: inline-block">
                                    <label for="start">Period start</label>
                                    <input type="text" value="{{$from}}" class="input-sm form-control" name="start"
                                           style="display: inline-block; width: 100px; float: none;"/>
                                    <label for="end">to</label>
                                    <input type="text" value="{{$to}}" class="input-sm form-control" id="end" name="end"
                                           style="display: inline-block; width: 100px;float: none"/>
                                </div>
                                <button style="display: inline-block" class="btn btn-primary">Search</button>
                            </form>
                            <div class="dataTables_wrapper form-inline dt-bootstrap">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <table id="error-report"
                                               class="table table-bordered table-striped dataTable" role="grid">
                                            <?php
                                            $columnSummary = [];
                                            echo '<thead>';
                                            echo '<tr id="header-row">';
                                            echo '<th>Period</th>';
                                            foreach ($devices as $device) {
                                                echo '<th>' . ($device->name ? $device->name : $device->serial_number) . '</th>';
                                                $columnSummary[$device->device_id] = 0;
                                            }
                                            echo '<th>Summary</th>';
                                            echo '</tr></thead><tbody>';

                                            $rowSummary = [];
                                            foreach ($reportData as $key => $row) {
                                                echo '<tr><td>' . $key . '</td>';
                                                $rowSummary[$key] = 0;
                                                foreach ($devices as $device) {
                                                    $value = '-';
                                                    if (isset($row[$device->device_id])) {
                                                        $value = intval($row[$device->device_id]);
                                                        $columnSummary[$device->device_id] += $value;
                                                        $rowSummary[$key] += $value;
                                                    }
                                                    echo '<td>' . $value . '</td>';
                                                }

                                                if (count($row) == 0) {
                                                    $rowSummary[$key] = '-';
                                                }
                                                echo '<td>' . $rowSummary[$key] . '</td>';
                                                echo '</tr>';
                                            }

                                            echo '</tbody><tfoot><tr class="summary-row">';
                                            echo '<th>Sum</th>';
                                            foreach ($devices as $device) {
                                                echo '<th>' . $columnSummary[$device->device_id] . '</th>';
                                            }
                                            echo '<th>' . array_sum($rowSummary) . '</th>';
                                            echo '</tr></tfoot>';

                                            ?>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endsection

            @section('view.scripts')
                <script src="{{ URL::asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
                <script src="{{ URL::asset('plugins/datatables/dataTables.bootstrap.min.js') }}"></script>

                <script src="{{ URL::asset('plugins/datepicker/bootstrap-datepicker.js') }}"></script>
                <link rel="stylesheet" href="{{ URL::asset('plugins/datepicker/datepicker3.css') }}">

                <script>

                    var table = $("#error-report").DataTable({
                        searching: false,
                        pageLength: 100,
                        order: [[0, "desc"]],
                        scrollX: true,
                        scrollCollapse: true,
                    });

                    $('.summary-row').clone().insertAfter($('#header-row'));

                    $('#datepicker').datepicker({
                        format: "yyyy-mm-dd",
                        todayBtn: "linked",
                        clearBtn: true,
                        autoclose: true,
                        todayHighlight: true,
                        calendarWeeks: true,
                    });
                </script>
@endsection
