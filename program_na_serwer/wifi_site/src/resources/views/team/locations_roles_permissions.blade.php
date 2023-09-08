@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-xs-12">


            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Options matrix</h3>
                </div>
                <div class="box-body">
                    <form class="form-horizontal" method="post">
                        {{ csrf_field() }}
                    <table class="table table-bordered">
                        <tbody>
                        <th></th>
                        @foreach($roles as $role)
                            <td>{{$role->role}}</td>
                        @endforeach
                        @foreach($optionsList as $option)
                            <tr>
                                <td>{{ $option->name }}</td>
                                @foreach($roles as $role)
                                    @php($optionName = $option->id.'_'. $role->id)
                                    <td><input type="checkbox" name="option[]" @if(array_key_exists($optionName, $canSeeOptions)) checked=checked @endif value="{{$optionName}}"></td>
                                @endforeach
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                        <button type="submit" value="Save">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
