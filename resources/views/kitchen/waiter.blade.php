@extends('layouts.layout')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/kitchen_view.css') }}">
    <link rel="stylesheet" href="{{ asset('css/labels.css') }}">
    <link rel="stylesheet" href="{{ asset('css/quickpanel.css') }}">
    <link rel="stylesheet" href="{{ asset('css/modal.css') }}">
    <!-- <link rel="stylesheet" href="{{ asset('css/soft-ui-dashboard.css') }}"> -->
    <script src="{{ asset('vendor') }}/interact/interact.min.js"></script>
    <script src="{{ asset('vendor') }}/moment-timezone.min.js"></script>
    <style>
        .card {
            box-shadow: 0 20px 27px 0 rgb(0 0 0 / 5%);
        }
        .card {
            position: relative;
            display: flex;
            flex-direction: column;
            min-width: 0;
            word-wrap: break-word;
            background-color: #fff;
            background-clip: border-box;
            border: 0 solid rgba(0, 0, 0, 0.125);
            border-radius: 1rem;
        }
        .card .card-body {
            font-family: "Open Sans";
            padding: 1.5rem;
        }
        .card-body {
            flex: 1 1 auto;
            padding: 1rem 1rem;
        }
        .content {
            background-color: #EFEFEF !important;
        }
        .end-0 {
            right: 0% !important;
        }
        .mt-2 {
            margin-top: 0.5rem !important;
        }
        .nav.nav-pills {
            background: #f8f9fa;
            border-radius: 0.75rem;
            position: relative;
        }
        .bg-transparent {
            background-color: transparent !important;
        }
        .p-1 {
            padding: 0.25rem !important;
        }
        .nav {
            display: flex;
            flex-wrap: wrap;
            padding-left: 0;
            margin-bottom: 0;
            list-style: none;
        }
        .nav.nav-pills .nav-item {
            z-index: 3;
        }
        .nav-fill>.nav-link, .nav-fill .nav-item {
            flex: 1 1 auto;
            text-align: center;
        }
        .nav.nav-pills .nav-link.active {
            animation: 0.2s ease;
        }
        .nav.nav-pills .nav-link {
            z-index: 3;
            color: #252f40;
            border-radius: 0.5rem;
            background-color: inherit;
        }
        .nav-fill .nav-item .nav-link, .nav-justified .nav-item .nav-link {
            width: 100%;
        }
        .nav-pills .nav-link {
            background: none;
            border: 0;
            border-radius: 0.75rem;
        }
        button:not(:disabled), [type="button"]:not(:disabled), [type="reset"]:not(:disabled), [type="submit"]:not(:disabled) {
            cursor: pointer;
        }
        .py-1 {
            padding-top: 0.25rem !important;
            padding-bottom: 0.25rem !important;
        }
        .px-0 {
            padding-right: 0 !important;
            padding-left: 0 !important;
        }
        .mb-0 {
            margin-bottom: 0 !important;
        }
        .nav-link {
            display: block;
            padding: 0.5rem 1rem;
            transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out;
        }
        button, [type="button"], [type="reset"], [type="submit"] {
            -webkit-appearance: button;
        }
        html * {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        b, strong {
            font-weight: 700;
        }
        *, *::before, *::after {
            box-sizing: border-box;
        }
        .moving-tab {
            z-index: 1 !important;
        }
        .position-absolute {
            position: absolute !important;
        }
        .moving-tab .nav-link.active {
            color: #fff;
            font-weight: 600;
            box-shadow: 0px 1px 5px 1px #ddd;
            animation: 0.2s ease;
            background: #fff;
        }
    </style> 

    <script type="module">
        function order(itemid){
            var element=$('#'+itemid);
            var table_id=null;
            if(element.attr('data-id')){
                table_id=element.attr('data-id');
            }

            var link = document.createElement('a');
            link.href = `/add_order/area_${table_id}`;
            link.click();
        }

        function showFloorplan(itemid){
            const arr = itemid.split('-');
            const selectedTab = "area-" + arr[1];

            $(".tab-pane").each(function (index, element) {
                if($(this).hasClass("active")) {
                    $(this).removeClass('show active');
                }

                if(selectedTab == $(this).attr('id')) {
                    $(this).addClass('show active');
                }
            });
        }

        function calculate_duration(created_date) {

            var today = moment();
            var date_obj = new Date(created_date);
            var moment_obj = moment.unix(date_obj);

            var duration = moment.duration(today.diff(moment_obj));
            var minutes = Math.abs(Math.round(duration.as("minutes")));
            minutes = isNaN(minutes) ? "-" : minutes;
            return minutes;
        }

        function go_to_order(order_status_constant, order_detail_link, order_edit_link) {
            window.location.href = order_status_constant == "CLOSED" ? order_edit_link : order_detail_link;
        }
        $(document).ready(function(){
            $(".search_running_list").change(function(){
                var search_running_list = $(this).val();
                console.log(search_running_list);
            });
        });
        window.onload = function() {
            var txt = '<span class=""><i class="fa fa-circle-notch fa-spin"></i>Loading..</span>'
            $('#running_order_content').append(txt);
            $('#digital_order_content').append(txt);
            
            var formData = new FormData();
            formData.append("access_token", window.settings.access_token);

            axios
            .post("/api/get_running_booking_list", formData)
            .then((response) => {
                if (response.data) {
                    $('#booking_list_content').empty();
                    $('#booking_list_btn').css("animation", "pulse 2s infinite");
                    if(response.data.booking_list.length > 0) {
                        response.data.booking_list.forEach(item => {
                            const start_date = new Date(item.start_date);
                            const end_date = new Date(item.end_date);
                            const today_str = new Date().toLocaleString("en-US", {timeZone: "Europe/Malta"});
                            const today = new Date(today_str.split(',')[0]);
                            today.setDate(today.getDate() + 1);
                            if(end_date > new Date(today_str) && start_date >= new Date(today_str.split(',')[0]) && start_date < today) {
                                const two_before = new Date(start_date.getTime() - 2 * 60 * 60 * 1000);
                                var txt = '';
                                if(two_before <= new Date(today_str) && start_date > new Date(today_str)) {
                                    txt = '<div class="list-item mb-3" style="animation: pulse 2s infinite;"><div class="form-row"><div class="form-group col-md-4 mb-0"><label for="type">Name</label><p class="mb-0">' + item.name + '</p></div><div class="form-group col-md-4 mb-0"><label for="table">Start Date</label><p class="mb-0">' + item.start_date + '</p></div><div class="form-group col-md-4 mb-0"><label for="table">Description</label><p class="mb-0">' + item.description + '</p></div></div></div>';
                                } else {
                                    txt = '<div class="list-item mb-3"><div class="form-row"><div class="form-group col-md-4 mb-0"><label for="type">Name</label><p class="mb-0">' + item.name + '</p></div><div class="form-group col-md-4 mb-0"><label for="table">Start Date</label><p class="mb-0">' + item.start_date + '</p></div><div class="form-group col-md-4 mb-0"><label for="table">Description</label><p class="mb-0">' + item.description + '</p></div></div></div>';
                                }
                                $('#booking_list_content').append(txt);
                            }
                        })
                    } else {
                        $('#booking_list_btn').css("animation", "none");
                    }
                }
            })
            .catch((error) => {
                console.log(error);
            });

            axios
            .post("/api/get_running_order_list_waiter", formData)
            .then((response) => {
                if (response.data.status_code == 200) {
                    var running_order_list = [];
                    var response_data = response.data.data.data;
                    for(let item of response_data){
                        if(item.status.constant == "CLOSED")
                            continue;
                        running_order_list.push(item);
                    }
                    $('#running_order_content').empty();
                    if(running_order_list.length > 0) {
                        var txt = '<input type="text" class="form-control form-control-custom mb-3 search_running_list" placeholder="Search by order, customer, table.." autocomplete="off" />';
                        $('#running_order_btn').css("animation", "pulse 2s infinite");
                        running_order_list.forEach((item) => {
                            const duration = calculate_duration(item.create_at_utc);
                            let kitchen_status = '';
                            if(item.kitchen_status != null) {
                                kitchen_status = '<span class="' + item.kitchen_status.color + '">' + item.kitchen_status.label + '</span>';
                            }
                            let table = "-";
                            if(item.table != ""){
                                table = item.table;
                            }
                            let waiter_data = "-";
                            if(item.waiter_data != null) {
                                waiter_data = item.waiter_data.fullname + " (" + item.waiter_data.user_code + ")";
                            }
                            txt = txt+'<div class="list-item mb-3 running_order_item" id="' + item.status.constant + ',' + 'item.detail_link' + ',' + item.edit_link + '"><div class="d-flex justify-content-between mb-2"><div class="mr-auto"><span class="timer-circle bg-light"><span class="timer-dot mr-1"></span>' + duration + ' Minute</span><span class="ml-2"><label for="order">Order</label> #' + item.order_number + '</span></div><div class="ml-auto">' + kitchen_status + '</div></div><div class="form-row"><div class="form-group col-md-4 mb-0"><label for="type">Type</label><p class="mb-0">' + item.order_type + '</p></div><div class="form-group col-md-4 mb-0"><label for="table">Table</label><p class="mb-0">' + table + '</p></div><div class="form-group col-md-4 mb-0"><label for="table">Waiter</label><p class="mb-0">' + waiter_data + '</p></div></div></div>';
                            
                        });
                        $('#running_order_content').append(txt);
                    } else {
                        $('#running_order_btn').css("animation", "none");
                    }
                }
            })
            .catch((error) => {
                console.log(error);
            });

            axios
            .post("/api/get_digital_menu_orders_list_waiter", formData)
            .then((response) => {
                if (response.data.status_code == 200) {
                    var digital_menu_order_list = response.data.data.data;
                    $('#digital_order_content').empty();
                    $('#digital_order_btn').css("animation", "pulse 2s infinite");
                    if(digital_menu_order_list.length > 0) {
                        digital_menu_order_list.forEach((item) => {
                            const duration = calculate_duration(item.create_at_utc);
                            let kitchen_status = '';
                            if(item.kitchen_status != null) {
                                kitchen_status = '<span class="' + item.kitchen_status.color + '">' + item.kitchen_status.label + '</span>';
                            }
                            let table = "-";
                            if(item.table != ""){
                                table = item.table;
                            }
                            let waiter_data = "-";
                            if(item.waiter_data != null) {
                                waiter_data = item.waiter_data.fullname + " (" + item.waiter_data.user_code + ")";
                            }
                            var txt = '<div class="list-item mb-3 digital_order_item" id="' + item.status.constant + ',' + 'item.detail_link' + ',' + item.edit_link + '"><div class="d-flex justify-content-between mb-2"><div class="mr-auto"><span class="timer-circle bg-light"><span class="timer-dot mr-1"></span>' + duration + ' Minute</span><span class="ml-2"><label for="order">Order</label> #' + item.order_number + '</span></div><div class="ml-auto">' + kitchen_status + '</div></div><div class="form-row"><div class="form-group col-md-4 mb-0"><label for="type">Type</label><p class="mb-0">' + item.order_type + '</p></div><div class="form-group col-md-4 mb-0"><label for="table">Table</label><p class="mb-0">' + table + '</p></div><div class="form-group col-md-4 mb-0"><label for="table">Waiter</label><p class="mb-0">' + waiter_data + '</p></div></div></div>';
                            $('#digital_order_content').append(txt);
                        });
                    } else {
                        $('#digital_order_btn').css("animation", "none");
                    }
                }
            })
            .catch((error) => {
                console.log(error);
            });
        };

        setInterval(function () {
            var txt = '<span class=""><i class="fa fa-circle-notch fa-spin"></i>Loading..</span>'
            $('#running_order_content').append(txt);
            $('#digital_order_content').append(txt);
            var formData = new FormData();
            formData.append("access_token", window.settings.access_token);

            axios
            .post("/api/get_running_booking_list", formData)
            .then((response) => {
                if (response.data) {
                    $('#booking_list_content').empty();
                    $('#booking_list_btn').css("animation", "pulse 2s infinite");
                    if(response.data.booking_list.length > 0) {
                        response.data.booking_list.forEach(item => {
                            const start_date = new Date(item.start_date);
                            const end_date = new Date(item.end_date);
                            const today_str = new Date().toLocaleString("en-US", {timeZone: "Europe/Malta"});
                            const today = new Date(today_str.split(',')[0]);
                            today.setDate(today.getDate() + 1);
                            if(end_date > new Date(today_str) && start_date >= new Date(today_str.split(',')[0]) && start_date < today) {
                                const two_before = new Date(start_date.getTime() - 2 * 60 * 60 * 1000);
                                var txt = '';
                                if(two_before <= new Date(today_str) && start_date > new Date(today_str)) {
                                    txt = '<div class="list-item mb-3" style="animation: pulse 2s infinite;"><div class="form-row"><div class="form-group col-md-4 mb-0"><label for="type">Name</label><p class="mb-0">' + item.name + '</p></div><div class="form-group col-md-4 mb-0"><label for="table">Start Date</label><p class="mb-0">' + item.start_date + '</p></div><div class="form-group col-md-4 mb-0"><label for="table">Description</label><p class="mb-0">' + item.description + '</p></div></div></div>';
                                } else {
                                    txt = '<div class="list-item mb-3"><div class="form-row"><div class="form-group col-md-4 mb-0"><label for="type">Name</label><p class="mb-0">' + item.name + '</p></div><div class="form-group col-md-4 mb-0"><label for="table">Start Date</label><p class="mb-0">' + item.start_date + '</p></div><div class="form-group col-md-4 mb-0"><label for="table">Description</label><p class="mb-0">' + item.description + '</p></div></div></div>';
                                }
                                $('#booking_list_content').append(txt);
                            }
                        })
                    } else {
                        $('#booking_list_btn').css("animation", "none");
                    }
                }
            })
            .catch((error) => {
                console.log(error);
            });

            axios
            .post("/api/get_running_order_list_waiter", formData)
            .then((response) => {
                if (response.data.status_code == 200) {
                    var running_order_list = [];
                    var response_data = response.data.data.data;
                    for(let item of response_data){
                        if(item.status.constant == "CLOSED")
                            continue;
                        running_order_list.push(item);
                    }
                    $('#running_order_content').empty();
                    if(running_order_list.length > 0) {
                        var txt = '<input type="text" class="form-control form-control-custom mb-3 search_running_list" placeholder="Search by order, customer, table.." autocomplete="off" />';
                        $('#running_order_btn').css("animation", "pulse 2s infinite");
                        running_order_list.forEach((item) => {
                            const duration = calculate_duration(item.create_at_utc);
                            let kitchen_status = '';
                            if(item.kitchen_status != null) {
                                kitchen_status = '<span class="' + item.kitchen_status.color + '">' + item.kitchen_status.label + '</span>';
                            }
                            let table = "-";
                            if(item.table != ""){
                                table = item.table;
                            }
                            let waiter_data = "-";
                            if(item.waiter_data != null) {
                                waiter_data = item.waiter_data.fullname + " (" + item.waiter_data.user_code + ")";
                            }
                            txt = txt+'<div class="list-item mb-3 running_order_item" id="' + item.status.constant + ',' + 'item.detail_link' + ',' + item.edit_link + '"><div class="d-flex justify-content-between mb-2"><div class="mr-auto"><span class="timer-circle bg-light"><span class="timer-dot mr-1"></span>' + duration + ' Minute</span><span class="ml-2"><label for="order">Order</label> #' + item.order_number + '</span></div><div class="ml-auto">' + kitchen_status + '</div></div><div class="form-row"><div class="form-group col-md-4 mb-0"><label for="type">Type</label><p class="mb-0">' + item.order_type + '</p></div><div class="form-group col-md-4 mb-0"><label for="table">Table</label><p class="mb-0">' + table + '</p></div><div class="form-group col-md-4 mb-0"><label for="table">Waiter</label><p class="mb-0">' + waiter_data + '</p></div></div></div>';
                            
                        });
                        $('#running_order_content').append(txt);
                    } else {
                        $('#running_order_btn').css("animation", "none");
                    }
                }
            })
            .catch((error) => {
                console.log(error);
            });
            axios
            .post("/api/get_digital_menu_orders_list_waiter", formData)
            .then((response) => {
                if (response.data.status_code == 200) {
                    var digital_menu_order_list = response.data.data.data;
                    $('#digital_order_content').empty();
                    $('#digital_order_btn').css("animation", "pulse 2s infinite");
                    if(digital_menu_order_list.length > 0) {
                        digital_menu_order_list.forEach((item) => {
                            const duration = calculate_duration(item.create_at_utc);
                            let kitchen_status = '';
                            if(item.kitchen_status != null) {
                                kitchen_status = '<span class="' + item.kitchen_status.color + '">' + item.kitchen_status.label + '</span>';
                            }
                            let table = "-";
                            if(item.table != ""){
                                table = item.table;
                            }
                            let waiter_data = "-";
                            if(item.waiter_data != null) {
                                waiter_data = item.waiter_data.fullname + " (" + item.waiter_data.user_code + ")";
                            }
                            var txt = '<div class="list-item mb-3 digital_order_item" id="' + item.status.constant + ',' + 'item.detail_link' + ',' + item.edit_link + '"><div class="d-flex justify-content-between mb-2"><div class="mr-auto"><span class="timer-circle bg-light"><span class="timer-dot mr-1"></span>' + duration + ' Minute</span><span class="ml-2"><label for="order">Order</label> #' + item.order_number + '</span></div><div class="ml-auto">' + kitchen_status + '</div></div><div class="form-row"><div class="form-group col-md-4 mb-0"><label for="type">Type</label><p class="mb-0">' + item.order_type + '</p></div><div class="form-group col-md-4 mb-0"><label for="table">Table</label><p class="mb-0">' + table + '</p></div><div class="form-group col-md-4 mb-0"><label for="table">Waiter</label><p class="mb-0">' + waiter_data + '</p></div></div></div>';
                            $('#digital_order_content').append(txt);
                        });
                    } else {
                        $('#digital_order_btn').css("animation", "none");
                    }
                }
            })
            .catch((error) => {
                console.log(error);
            });
        }, 60000);

        interact('.resize-drag')
        .on('tap', function (event) {
            order(event.currentTarget.id);
            event.preventDefault();
        });

        interact('.nav-link')
        .on('tap', function (event) {
            showFloorplan(event.currentTarget.id);
            event.preventDefault();
        });

        interact('.booking_list_button')
        .on('tap', function (event) {
            $('#bookingListModal').show();
            event.preventDefault();
        });

        interact('.panel-close-booking-list')
        .on('tap', function (event) {
            $('#bookingListModal').hide();
            event.preventDefault();
        });

        interact('.digital_order_button')
        .on('tap', function (event) {
            $('#digitalOrderModal').show();
            event.preventDefault();
        });

        interact('.panel-close-digital-order')
        .on('tap', function (event) {
            $('#digitalOrderModal').hide();
            event.preventDefault();
        });

        interact('.running_order_button')
        .on('tap', function (event) {
            $('#runningOrderModal').show();
            event.preventDefault();
        });

        interact('.panel-close-running-order')
        .on('tap', function (event) {
            $('#runningOrderModal').hide();
            event.preventDefault();
        });

        interact('.running_order_item')
        .on('tap', function (event) {
            const id = event.currentTarget.id;
            go_to_order(id.split(",")[0], id.split(",")[2], id.split(",")[2])
            event.preventDefault();
        });
        interact('.digital_order_item')
        .on('tap', function (event) {
            const id = event.currentTarget.id;
            go_to_order(id.split(",")[0], id.split(",")[2], id.split(",")[2])
            event.preventDefault();
        });       
        
    </script>
@endpush
@section('content')
    <div class="d-flex flex-nowrap horizontal-scroll hide-horizontal-scroll">
        <button class="btn btn-primary btn-light ml-3 booking_list_button" id="booking_list_btn">
            Booking List
        </button>
        <button class="btn btn-primary btn-light ml-3 digital_order_button" id="digital_order_btn">
            Digital Menu Orders
        </button>
        <button class="btn btn-primary btn-light ml-3 running_order_button" id="running_order_btn">
            Running Orders
        </button>
    </div>

    <div class="nav-wrapper position-relative end-0 mt-2" id="floorAreas">
        <ul class="nav nav-pills nav-fill p-1 bg-transparent " role="tablist">
          @foreach ($items as $key => $area)
            <li class="nav-item" style="padding-top:8px" >
              <a style="height: 50px;"  class="nav-link mb-0 px-0 py-1 {{$key==0?"active":""}}" id="area-{{ $area->id }}-tab"  data-bs-toggle="tab" data-bs-target="#area-{{ $area->id }}" type="button" role="tab" aria-controls="area-{{ $area->id }}" aria-selected="{{$key==0?"tru":"false"}}"><strong>{{ $area->name }}</strong></a>
            </li>
          @endforeach
        </ul>
    </div>
    <div class="container-fluid py-2" id="floorTabs">
        <div class="row">
            <div class="col-12 col-xl-12">
                <div class="card h-100">
                    <div class="card-body p-3">
                        <div class="nav-wrapper position-relative end-0">
                            <div class="tab-content" id="floorTabsContent">
                                @foreach ($items as $key => $area)
                                    <div class="tab-pane fade  {{$key==0?"show active":""}}" id="area-{{ $area->id }}" role="tabpanel" aria-labelledby="area-{{ $area->id }}-tab">
                                        <div class="card card-frame" style="text-align: center; justify-content: center; align-items: center;" >
                                            <div class="card-body ">
                                                <div class="canva" id="canvaHolder">
                    
                                                    @foreach ($area->tables as $table)
                                                        <?php
                                                    
                                                            $whString="";
                                                            if($table->w||$table->h){
                                                                $whString="width: ".$table->w."px; height: ".$table->h."px;";
                                                            }
                                                            $order_flag = false;
                                                            foreach ($order_tables as $key => $val){
                                                                if($val === $table->table_number) {
                                                                    $order_flag = true;
                                                                    break;
                                                                }
                                                            }
                                                        ?>
                                                        <div 
                                                        id="drag-{{$table->id}}" 
                                                        data-id="{{$table->id}}" 
                                                        data-x="{{$table->x}}"
                                                        data-y="{{$table->y}}"
                                                        data-name="{{$table->table_number}}"
                                                        data-rounded="{{$table->rounded?$table->rounded:"no"}}"
                                                        data-size="{{$table->no_of_occupants}}"
                                                        class="resize-drag {{ $table->rounded=="yes"?"circle":""}}" style="transform: translate({{$table->x}}px, {{$table->y}}px); {{$whString}};  background-color: {{$order_flag? "#F63A39":"#17c1e8"}};">
                                                            <p> {{$table->table_number}} </p>
                                                            <span>{{$table->no_of_occupants}}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="bookingListModal" style="display: none">
        <div class="p-0 bg-white hidden quickpanel col-md-4 col-xl-3">
            <div class="border-bottom p-0">
                <div class="d-flex justify-content-between p-3">
                    <div class="mr-auto text-subtitle">
                        Booking List
                    </div>
                    <button type="button" aria-label="Close" class="close panel-close panel-close-booking-list bg-light ml-auto">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
            </div>

            <div class="p-0">
                <div class="d-flex flex-column p-3 mb-1" id="booking_list_content">
                    
                </div>
            </div>
        </div>
        <div class="modal-mask modal"></div>
    </div>

    <div id="digitalOrderModal" style="display: none">
        <div class="p-0 bg-white hidden quickpanel col-md-4 col-xl-3">
            <div class="border-bottom p-0">
                <div class="d-flex justify-content-between p-3">
                    <div class="mr-auto text-subtitle">
                        Digital Menu Orders
                    </div>
                    <button type="button" aria-label="Close" class="close panel-close panel-close-digital-order bg-light ml-auto">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
            </div>

            <div class="p-0">
                <div class="d-flex flex-column p-3 mb-1" id="digital_order_content">
                    
                </div>
            </div>
        </div>
        <div class="modal-mask modal"></div>
    </div>

    <div id="runningOrderModal" style="display: none">
        <div class="p-0 bg-white hidden quickpanel col-md-4 col-xl-3">
            <div class="border-bottom p-0">
                <div class="d-flex justify-content-between p-3">
                    <div class="mr-auto text-subtitle">
                        Running Orders
                    </div>
                    <button type="button" aria-label="Close" class="close panel-close panel-close-running-order bg-light ml-auto">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
            </div>

            <div class="p-0">
                <div class="d-flex flex-column p-3 mb-1" id="running_order_content">
                    
                </div>
            </div>
        </div>
        <div class="modal-mask modal"></div>
    </div>
@endsection