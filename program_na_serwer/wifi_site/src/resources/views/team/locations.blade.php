@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">

                <div class="box-header with-border">
                    <h3 class="box-title">Add new location</h3>
                </div>
                <form class="form-horizontal" method="post" action="locations/add">
                    {{ csrf_field() }}
                    <div class="box-body">
                        <div class="form-group col-sm-12">
                            <div class="input-group">
                                <input class="form-control" id="locationName_2" name="locationName"
                                       placeholder="New location name" type="text">
                                <div class="input-group-btn">
                                    <button type="submit" class="btn btn-primary">Create</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Locations</h3>
                </div>

                <div class="box-body" id="no-more-tables">
                    <table id="device-details"
                           class="col-sm-12 device-details table table-bordered table-striped nowrap">
                        <thead class="cf">
                        <tr>
                            <th>Location</th>
                            <th>Tenantry</th>
                            <th>Staff</th>
                            <th>Technician</th>
                            <th>Settlement</th>
                            <th>Profits</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($locations as $location)
                            <tr role="row" data-id="{{ $location->id }}">
                                <td data-title="Location" class="name col-md-2">{{ $location->name }}</td>
                                <td data-title="Tenantry" class="tenantry col-md-2 min-height-50">
                                    @foreach($usersWithLocation as $user)
                                        @if ($user->location_id == $location->id && $user->role == \App\Http\Controllers\TeamController::TENANTRY)
                                            <span title="{{ $user->email }}" class="btn btn-default btn-block btn-flat"
                                                  data-userid="{{ $user->user_id }}">{{ $user->name }}</span>
                                        @endif
                                    @endforeach
                                </td>
                                <td data-title="Staff" class="staff col-md-2 min-height-50">
                                    @foreach($usersWithLocation as $user)
                                        @if ($user->location_id == $location->id && $user->role == \App\Http\Controllers\TeamController::STAFF)
                                            <span title="{{ $user->email }}" class="btn btn-default btn-block btn-flat"
                                                  data-userid="{{ $user->user_id }}">{{ $user->name }}</span>
                                        @endif
                                    @endforeach
                                </td>
                                <td data-title="Technician" class="technician col-md-2 min-height-50">
                                    @foreach($usersWithLocation as $user)
                                        @if ($user->location_id == $location->id && $user->role == \App\Http\Controllers\TeamController::TECHNICIAN)
                                            <span title="{{ $user->email }}" class="btn btn-default btn-block btn-flat"
                                                  data-userid="{{ $user->user_id }}">{{ $user->name }}</span>
                                        @endif
                                    @endforeach
                                </td>
                                <td data-title="Settlement" class="settlement col-md-2 min-height-50">
                                    @foreach($settlements as $user)
                                        @if ($user->location_id == $location->id)
                                            <span title="{{ $user->email }}" class="btn btn-default btn-block btn-flat"
                                                  data-userid="{{ $user->user_id }}">{{ $user->name }}</span>
                                        @endif
                                    @endforeach
                                </td>
                                <td data-title="Profits" class="profits col-md-2 min-height-50">
                                    @foreach($profits as $user)
                                        @if ($user->location_id == $location->id)
                                            <span title="{{ $user->email }}" class="btn btn-default btn-block btn-flat"
                                                  data-userid="{{ $user->user_id }}"
                                                  data-profit="{{ $user->profit }}">{{ $user->name }} {{ $user->profit }}%</span>
                                        @endif
                                    @endforeach
                                </td>
                                <td data-title="Action" class="col-md-2">
                                    <button class="btn btn-primary edit-location">Edit</button>
                                    <a href="locations/remove/{{ $location->id }}" class=" btn btn-primary delete-group">Delete</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal edit">
        <div class="modal-dialog">
            <div class="modal-content">
                <form class="form-horizontal group-update" id="location-edit-form" method="post" action="locations/edit">
                    {{ csrf_field() }}
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Location edit</h4>
                    </div>
                    <div class="modal-body">
                        <div class="box-body">
                            <input type="hidden" name="location-id" value="">
                            <div class="form-group">
                                <label for="locationName" class="col-sm-3 control-label">Location name</label>
                                <div class="col-sm-5">
                                    <input class="form-control" id="locationName" name="name" placeholder="New group name"
                                           type="text">
                                </div>
                            </div>

                            <hr/>

                            <div class="form-group">
                                <label for="tenantry" class="col-sm-3 control-label">Tenantry</label>
                                <div class="col-sm-8">
                                    <select id="tenantry" name="tenantry[]" class="roles tenantry" multiple="multiple"
                                            data-placeholder="Select user" style="width: 100%;">
                                        @foreach($members as $member)
                                            <option value="{{ $member->id }}">{{ $member->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="staff" class="col-sm-3 control-label">Staff</label>
                                <div class="col-sm-8">
                                    <select id="staff" name="staff[]" class="roles staff" multiple="multiple"
                                            data-placeholder="Select user" style="width: 100%;">
                                        @foreach($members as $member)
                                            <option value="{{ $member->id }}">{{ $member->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="technician" class="col-sm-3 control-label">Technician</label>
                                <div class="col-sm-8">
                                    <select id="technician" name="technician[]" class="roles technician" multiple="multiple"
                                            data-placeholder="Select user" style="width: 100%;">
                                        @foreach($members as $member)
                                            <option value="{{ $member->id }}">{{ $member->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <hr/>

                            <div class="form-group">
                                <label for="settlement" class="col-sm-3 control-label">Settlement</label>
                                <div class="col-sm-8">
                                    <select id="settlement" name="settlement[]" class="settlement" multiple="multiple"
                                            data-placeholder="Select user" style="width: 100%;">
                                        @foreach($members as $member)
                                            <option value="{{ $member->id }}">{{ $member->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <hr/>

                            <div id="main-profits">
                                <div class="form-group profits-container">
                                    <?php
                                        $id = rand(5, 15);
                                    ?>
                                    <label for="profit_distribution_{{$id}}" class="col-sm-3 control-label">Profits</label>
                                    <div class="col-sm-5">
                                        <select id="profit_distribution_{{$id}}" name="profit_distribution[]" class="profit_distribution"
                                                disabled="disabled"
                                                data-placeholder="Select user" style="width: 100%;">
                                            <option value="{{Auth::user()->id}}">{{Auth::user()->name}}</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-4 profit-value">
                                        <label for="profit_value_{{$id}}">Value</label>
                                        <input id="profit_value_{{$id}}" name="profit_value[]" type="number" class="form-control" min="0"
                                               max="100" data-value=""
                                               disabled/>
                                    </div>
                                </div>
                            </div>

                            <div id="profits"></div>

                            <div class="form-group profits-container hidden">
                                <?php
                                $id = rand(5, 15);
                                ?>
                                <label for="profit_distribution_{{$id}}" class="col-sm-3 control-label">Profits</label>
                                <div class="col-sm-5">
                                    <select id="profit_distribution_{{$id}}" name="profit_distribution[]" class="profit_distribution"
                                            data-placeholder="Select user" style="width: 100%;">
                                        <option></option>
                                        @foreach($members as $member)
                                            <option value="{{ $member->id }}">{{ $member->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-4 profit-value">
                                    <label for="profit_value_{{$id}}">Value</label>
                                    <input id="profit_value_{{$id}}" name="profit_value[]" type="number" class="form-control" min="0" max="100"
                                           step="1" disabled/>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                        <button type="submit" id="send-form" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal remove-group">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Are you sure you want to remove location ?</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary remove">Remove location</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('view.scripts')
    <script src="{{ URL::asset('plugins/select2/select2.full.min.js') }}"></script>
    <script>
        $(function () {
            let selectBoxes = $('select.roles').select2();
            let settlement = $('select.settlement').select2();
            $('#main-profits select').select2();

            selectBoxes.on("select2:select", function (e) {
                selectValueChange(e, 'select')

                if (e.target.name == 'tenantry[]') {
                    addRelatedSelection(settlement, e.params.data.id);
                    addRelatedSelection(profitDistribution, e.params.data.id);
                }
            });
            selectBoxes.on("select2:unselect", function (e) {
                selectValueChange(e, 'unselect')

                if (e.target.name == 'tenantry[]') {
                    removeRelatedSelection(settlement, e.params.data.id);
                    removeRelatedSelection(profitDistribution, e.params.data.id);
                }
            });

            function addRelatedSelection(select, id) {
                let tmp = [];
                select.select2('data').forEach(item => {
                    tmp.push(item.id);
                });

                tmp.push(id);

                select.val([...new Set(tmp)]);
                select.trigger('change');
            }

            function removeRelatedSelection(select, id) {
                let tmp = [];
                select.select2('data').forEach(item => {
                    tmp.push(item.id);
                });

                tmp = tmp.filter(item => {
                    return id != item;
                })

                select.val([...new Set(tmp)]);
                select.trigger('change');
            }

            function selectValueChange(e, action) {
                var params = e.params.data;
                var parentName = $(params.element.parentElement).attr('name');
                var selectedId = params.id;

                $("select.roles[name!='" + parentName + "']").each(function () {
                    if (action == 'select') {
                        $(this).find("option[value='" + selectedId + "']").attr('disabled', 'disabled');
                    } else {
                        $(this).find("option[value='" + selectedId + "']").removeAttr('disabled');
                    }
                });
            }

            $('.edit-location').click(async function () {
                let disabledProfitsOption = [];
                $("select.roles option").each(function () {
                    $(this).removeAttr('disabled');
                });

                await Promise.all(Array.from(document.querySelectorAll('.profits-container.cloned')).map(item => {
                    const selectEl = $(item).find('select');
                    selectEl.select2("destroy").remove();
                    $(item).remove();
                }));

                let parent = $(this).parents('tr');
                let name = parent.find('.name').html();
                let locationId = parent.data('id');
                let technician = getUserIds(parent, 'technician');
                let tenantry = getUserIds(parent, 'tenantry');
                let staff = getUserIds(parent, 'staff');
                let settlement = getUserIds(parent, 'settlement');
                let profits = getUserProfits(parent, 'profits');
                console.warn(profits)


                $('form.group-update input[name="location-id"]').val(locationId);
                $('form.group-update input[name="name"]').val(name);
                $("select.roles.technician").val(technician).trigger("change");
                $("select.roles.tenantry").val(tenantry).trigger("change");
                $("select.roles.staff").val(staff).trigger("change");
                $("select.settlement").val(settlement).trigger("change");

                let maxUsers = null;

                profits.forEach(item => {
                    const randID = randomInt();
                    if (item.userId == <?php echo Auth::user()->id;?>) {
                        $('#main-profits .profits-container').attr('id', randID);
                        $('#main-profits .profits-container select').attr('name', `profit_distribution[${randID}]`);
                        $('#main-profits .profits-container input').attr('name', `profit_value[${randID}]`);
                        changeProfitValue($('#main-profits .profits-container input'), item.profit);
                        return;
                    }

                    const el = cloneProfitElement(randID);
                    const select = el.find('select');
                    select.attr('name', `profit_distribution[${randID}]`)
                    select.val(item.userId);
                    disabledProfitsOption.push(item.userId);
                    maxUsers = $(select).find('option').length;
                    $.each($(select.find('option')), (index, optionElement) => {
                        profits.map((profitItem) => {
                            if ($(optionElement).val() != item.userId && $(optionElement).val() == profitItem.userId) {
                                $(optionElement).attr('disabled', 'disabled');
                            }
                        })
                    });
                    const input = el.find('input');
                    changeProfitValue(input, item.profit);
                    input.removeAttr('disabled');
                    input.attr('name', `profit_value[${randID}]`)
                });

                if (maxUsers === null || profits.length < maxUsers) {
                    const randID = randomInt();
                    const el = cloneProfitElement(randID);
                    $(el).find('select').attr('name', `profit_distribution[${randID}]`)
                    $(el).find('input').attr('name', `profit_value[${randID}]`)
                    maxUsers = $(el).find('option').length - 1;

                    $.each($(el).find('option'), (index, optionElement) => {
                        profits.map((profitItem) => {
                            if ($(optionElement).val() == profitItem.userId) {
                                $(optionElement).attr('disabled', 'disabled');
                            }
                        })
                    });

                }

                let profitDistribution = $('#profits select.profit_distribution').select2({
                    allowClear: true,
                    placeholder: "Select user",
                });

                $.each(profitDistribution, (index, el) => {
                    addOnSelectEvent($(el), maxUsers);
                    addOnUnSelectEvent($(el));
                });

                function addOnSelectEvent(el, maxUsers) {
                    let previousProfitValue = '';
                    el.on("select2:selecting", (e) => {
                        previousProfitValue = '';
                        if (e.target.value != '') {
                            previousProfitValue = e.target.value;
                        }
                    });

                    el.on("select2:select", (e) => {
                        const parentEl = $(el).closest('#profits .profits-container.cloned');
                        const parentId = parentEl.attr('id');

                        if (previousProfitValue != '') {
                            $.each($('#profits .profits-container[id!="' + parentId + '"] select option[value="' + previousProfitValue + '"]'), (index, el) => {
                                $(el).removeAttr('disabled');
                            });
                            const index = disabledProfitsOption.indexOf(previousProfitValue);
                            if (index > -1) {
                                disabledProfitsOption.splice(index, 1);
                            }
                        }
                        ;

                        $.each($('#profits .profits-container[id!="' + parentId + '"] select option[value="' + e.params.data.id + '"]'), (index, el) => {
                            $(el).attr('disabled', 'disabled');
                        });

                        disabledProfitsOption.push(e.params.data.id);

                        $(parentEl).find('input').removeAttr('disabled');
                        if ($('#profits .profits-container').length >= maxUsers) {
                            if (previousProfitValue == '') {
                                changeProfitValue($(parentEl).find('input'), 0);
                            }
                            return
                        }
                        ;

                        if (previousProfitValue == '') {
                            changeProfitValue($(parentEl).find('input'), 0);
                            $(parentEl).find('input').removeAttr('disabled');
                            const randID = randomInt();

                            const select = cloneProfitElement(randID);
                            const selectEl = select.find('select').select2({
                                allowClear: true,
                                placeholder: "Select user",
                            });
                            $(selectEl).attr('name', `profit_distribution[${randID}]`)

                            changeProfitValue($(select).find('input'), 0);
                            $(select).find('input').attr('name', `profit_value[${randID}]`)

                            $.each($(selectEl.find('option')), (index, el) => {
                                if (disabledProfitsOption.includes($(el).val())) {
                                    $(el).attr('disabled', 'disabled');
                                }
                            });
                            addOnSelectEvent(select, maxUsers);
                            addOnUnSelectEvent(select);

                            $(select).find('input').change((e) => {
                                onProfitChange(e);
                            });
                        }
                    });
                }

                function addOnUnSelectEvent(el) {
                    el.on("select2:unselect", (e) => {

                        let inputElement = $(e.target).closest('#profits .profits-container.cloned').find('input');

                        let mainInputEl = $('#main-profits .profits-container input');
                        let value = +mainInputEl.val() + +inputElement.val();

                        changeProfitValue(mainInputEl, value);

                        if ($('#profits .profits-container').length == 1) {
                            changeProfitValue(inputElement, null);
                            inputElement.attr('disabled', 'disabled');
                            return;
                        }

                        const index = disabledProfitsOption.indexOf(e.params.data.id);
                        if (index > -1) {
                            disabledProfitsOption.splice(index, 1);
                        }

                        let selected = 0;
                        $.each($('#profits .profits-container select'), (val, el) => {
                            if ($(el).val() != '') {
                                selected++;
                            }
                        });

                        if ($('#profits .profits-container').length >= maxUsers && (selected + 1) === maxUsers) {
                            $(e.target).val(null).trigger('change');

                            let parentEl = $(e.target).closest('#profits .profits-container.cloned');

                            let inputEl = $(parentEl).find('input');
                            changeProfitValue(inputEl, 0);
                            $(inputEl).attr('disabled', 'disabled');
                            $(inputEl).trigger('change');

                        } else {
                            const container = $(e.target).closest('#profits .profits-container');
                            setTimeout(() => {
                                $(e.target).select2("destroy").remove();
                                container.remove();
                            });
                        }

                        $.each($('#profits .profits-container select option[value="' + e.params.data.id + '"]'), (index, el) => {
                            $(el).removeAttr('disabled');
                        });
                    });
                };

                $('.modal.edit').modal();

                $("select.roles option:selected").each(function () {
                    var selectedId = $(this).val();
                    var parentName = $(this).parents('select').attr('name');
                    $("select.roles[name!='" + parentName + "']").each(function () {
                        $(this).find("option[value='" + selectedId + "']").attr('disabled', 'disabled');
                    });
                });

                function randomInt() {
                    return Math.floor(Math.random() * 99999998) + 1;
                }

                function cloneProfitElement(randID) {
                    return $('.profits-container.hidden').clone().addClass('cloned').attr('id', randID).removeClass('hidden').appendTo("#profits");
                }

                function onProfitChange(e) {
                    if (e.target.value > 100) {
                        e.target.value = 100;
                    }

                    if (e.target.value < 0) {
                        e.target.value = 0;
                    }

                    let diff = $(e.target).attr('data-value') - e.target.value;

                    let mainProfit = +$('#main-profits .profits-container input').val() + +diff;

                    if (mainProfit < 0) {
                        mainProfit = 0;
                    }

                    changeProfitValue($('#main-profits .profits-container input'), mainProfit);
                    $(e.target).attr('data-value', e.target.value);


                    let sumOfAllProfits = 0;
                    $('#profits .profits-container .profit-value input').map(function () {
                        sumOfAllProfits = sumOfAllProfits + +this.value;
                    });

                    if (sumOfAllProfits > 100) {
                        changeProfitValue($('#main-profits .profits-container input'), 0);
                        let rest = sumOfAllProfits - 100;
                        e.target.value = +e.target.value - +rest;
                        $(e.target).attr('data-value', e.target.value);
                    }
                }

                $('#profits .profit-value input').change((e) => {
                    onProfitChange(e);
                });
            });

            function getUserIds(parent, element) {
                let arr = [];
                parent.find(`.${element} span`).each(function (el) {
                    arr = arr.concat($(this).data('userid'));
                });
                arr = jQuery.unique(arr);
                return arr;
            }

            function getUserProfits(parent, element) {
                let arr = [];
                parent.find(`.${element} span`).each(function () {
                    arr.push({userId: $(this).data('userid'), profit: $(this).data('profit')});
                });
                return arr;
            }

            var removeLink = '';
            $('.delete-group').click(function (e) {
                e.preventDefault();
                $('.modal.remove-group').modal();
                removeLink = $(this).attr('href');
            });

            $('.modal.remove-group button.remove').click(function (e) {
                e.preventDefault();
                window.location = removeLink;
                removeLink = '';
            });

            $("#location-edit-form").submit(function (event) {
                event.preventDefault();
                $('#main-profits .profits-container select').removeAttr('disabled');
                $('#main-profits .profits-container input').removeAttr('disabled');
                this.submit();
            });

            function changeProfitValue(element, value) {
                element.val(value);
                element.attr('data-value', value);
            }
        });
    </script>
@endsection
