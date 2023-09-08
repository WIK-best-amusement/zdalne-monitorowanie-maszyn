@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">All users that can access devices</h3>
                </div>
                <div class="box-body">
                    <div class="dataTables_wrapper form-inline dt-bootstrap">

                        <div class="row">
                            <div class="col-sm-12">
                                <table id="user-list" class="table table-bordered table-striped dataTable" role="grid">
                                    <thead>
                                    <tr role="row">
                                        <th>Nr</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Created at</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $i=1;?>
                                    @foreach ($members as $member)
                                        <tr role="row">
                                            <td>{{ $i }}</td>
                                            <td>{{ $member->name }}</td>
                                            <td>{{ $member->email }}</td>
                                            <td>{{ (new DateTime($member->created_at))->format('Y-m-d') }}</td>
                                            <td class="col-md-2">
                                                <a {{ Auth::user()->id == $member->id ? 'disabled="disabled"' : 'href=/team/user/remove/'.$member->id.'' }} class=" btn btn-primary remove-user">Delete</a>
                                            </td>
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
        </div>
    </div>

    <div class="modal remove-user">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Are you sure you want to remove user from team ?</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary remove">Remove user</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('view.scripts')
    <script src="{{ URL::asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('plugins/datatables/dataTables.bootstrap.min.js') }}"></script>

    <script>
        $("#user-list").DataTable();

        let removeLink = '';
        $('a.remove-user').click(function (e) {
            e.preventDefault();
            $('.modal.remove-user').modal();
            removeLink = $(this).attr('href');
        });

        $('.modal.remove-user button.remove').click(function (e) {
            e.preventDefault();
            window.location = removeLink;
            removeLink = '';
        });
    </script>

@endsection
