@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">History of "{{ $option->name }}"</h3>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="setting-history" class="table table-condensed table-striped" role="grid">
                            <thead>
                            <tr role="row">
                                <th class="col-xs-1">Nr</th>
                                <th class="col-xs-6">Value</th>
                                <th class="col-xs-5">Date</th>
                            </tr>
                            </thead>
                            <tbody>

                            <?php $i = 1;?>
                            @foreach ($history as $entry)
                                <tr role="row">
                                    <td>{{ $i }}</td>
                                    <td>{{ $entry->value  }}</td>
                                    <td>{{ $entry->date }}</td>
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

    <div class="row">
        <div class="col-xs-12">
            <a href="{{ URL::previous() }}" class="btn btn-default">Back</a>
        </div>
    </div>
@endsection

@section('view.scripts')
    <script src="{{ URL::asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('plugins/datatables/dataTables.bootstrap.min.js') }}"></script>

    <script>
        $("#setting-history").DataTable({
            "bFilter": true,
            "bSort": true,
            "pageLength": 50
        });
    </script>

@endsection
