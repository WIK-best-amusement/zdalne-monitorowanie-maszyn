@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-md-12">
            @if (Session::get('submited-form') == true)
                <div class="callout callout-success">
                    <h4>Success!</h4>
                    <p>Users were added to team and invitations was sended.</p>
                </div>
            @endif

            @if (count($errors) > 0)
                <div class="callout callout-danger">
                    <h4>Wrong data</h4>
                    <p>There is a problem that we need to fix. Check user name and email with exclamation mark.</p>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">Insert user name and email to send invitation</h3>
                </div>
                <form action="{{ url('team/invite/send') }}" method="post" class="form-horizontal user-invite-form">
                    {{ csrf_field() }}
                    <div id="user-invite-container">
                        @for($i = 0; $i < $fieldsNumber; $i++)
                        <div class="box-body" id="user-invite-template">
                            <div class="form-group col-md-6">
                                <label for="inputName" class="col-sm-3 control-label">Full name</label>

                                <div class="col-sm-9 input-group">
                                    <input class="form-control" id="inputName" placeholder="Full name" type="name" name="name[]" value="{{ old('name.'.$i) }}">
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="inputEmail" class="col-sm-3 control-label">Email</label>

                                <div class="col-sm-9 input-group">
                                    <input class="form-control" id="inputEmail" placeholder="Email" type="email" name="email[]" value="{{ old('email.'.$i) }}">
                                    <span class="input-group-btn @if ($i == 0) hidden @endif invitation-rm-btn">
                                        <button type="button" class="btn btn-danger btn-flat rm-button">Remove</button>
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endfor
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary pull-right ">Send invitation</button>
                        <button type="submit" class="btn btn-primary pull-right margin-r-5 add-next-user">Add next user
                        </button>
                    </div>
                </form>
            </div>

        </div>

    </div>

@endsection

@section('view.scripts')
    <script>
        $('.add-next-user').click(function (event) {
            event.preventDefault();
            var el = $('#user-invite-template').clone();
            el.removeAttr('id');
            el.find('#inputName').val('');
            el.find('#inputEmail').val('');
            el.find('.invitation-rm-btn').removeClass('hidden');
            el.appendTo($('#user-invite-container'));
        });

        $('.user-invite-form').on('click', '.rm-button', function() {
           $(this).closest('.box-body').remove();
        });
    </script>
@endsection