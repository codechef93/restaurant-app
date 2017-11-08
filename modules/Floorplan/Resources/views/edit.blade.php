@extends('layouts.layout')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/kitchen_view.css') }}">
    <link rel="stylesheet" href="{{ asset('css/labels.css') }}">
    <link rel="stylesheet" href="{{ asset('css/argon.css') }}">
    <script src="{{ asset('vendor') }}/interact/interact.min.js"></script>
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
    <script>
  var area_id="{{$restoarea->id}}";
  var table_id=null;
  var edit_id=null;

  function deleteTable(itemid){
    var element=$('#'+itemid);
    element.hide();
    element.attr('data-deleted','yes')
  }

  function editItem(itemid){
    reset();
    var element=$('#'+itemid);
    $('#table_name').val(element.attr('data-name'));
    $('#table_size').val(element.attr('data-size'));
    if(element.attr('data-rounded')=="yes"){
      $('#table_round').prop('checked', true);
    }else{
      $('#table_round').prop('checked', false);
    }
    
    edit_id=itemid;

    if(element.attr('data-id')){
      table_id=element.attr('data-id');
    }else{
      table_id=true; //Since it is edit
    }
    

    jQuery.noConflict(); 
    $('#tableModal').modal('toggle');
  }
  function order(itemid){
    reset();
    var element=$('#'+itemid);
    if(element.attr('data-id')){
      table_id=element.attr('data-id');
    }

    var link = document.createElement('a');
    link.href = `/add_order/area_${table_id}`;
    link.click();
}

  function reset(){
    $('#table_name').val("");
    $('#table_size').val("");
    $('#table_round').prop('checked', false);
    table_id=null;
    edit_id=null;
  }

  function saveTable(){
    if(table_id){
      updateTable();
    }else{
      addTable();
    }
  }

  function addTable(){
    $( ".canva" ).append( '<div class="resize-drag '+($('#table_round').prop('checked')?"circle":"")+'" id="'+Math.floor((Math.random() * 10000) + 1)+'" data-rounded="'+($('#table_round').prop('checked')?"yes":"no")+'" data-name="'+$('#table_name').val()+'" data-size="'+$('#table_size').val()+'"  data-x="0" data-y="0" class="resize-drag"><p>'+$('#table_name').val()+"</p><span>"+$('#table_size').val()+"</span></div>" );
    reset();
  }

  function updateTable(){
    var element=$('#'+edit_id);
    element.attr('data-name',$('#table_name').val());
    element.attr('data-size',$('#table_size').val())
    $('span:first', element).html($('#table_size').val());
    $('p:first', element).html($('#table_name').val());

    if($('#table_round').prop('checked')){
      element.attr('data-rounded',"yes");
      element.addClass('circle');
    }else{
      element.attr('data-rounded',"no");
      element.removeClass('circle');
    }

    reset();
  }

  function saveFloor(){
    var items=[];
    $.each( $('.canva'), function(i, element) {
      $('div', element).each(function(it,item) {
        var element=$('#'+item.id);
        var item={
          "table_id":element.attr('data-id'),
          "x":element.attr('data-x'),
          "y":element.attr('data-y'),
          "w":element.width()+44,
          "h":element.height()+44,
          "table_number":element.attr('data-name'),
          "deleted":element.attr('data-deleted'),
          "rounded":element.attr('data-rounded'),
          "no_of_occupants":element.attr('data-size')
        }
        items.push(item);
        
      });
    })

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type:'post',
        data: {"items":items},
        url: "{{ route('floorplan.save',$restoarea->id) }}",
        success:function(response){
            if(response.status){
              alert("{{ __('Floor plan is saved.') }}");
            }else{
              alert(response.message);
            }
        },
        error: function (response) {
          alert("{{ __('Error on save') }}");
        }
    })

  }
</script>
<script type="module">
    
    interact('.resize-drag')    
    .on('tap', function (event) {
        if(!event.ctrlKey){return;}
        order(event.currentTarget.id);
        event.preventDefault()
    })
    .on('doubletap', function (event) {
      editItem(event.currentTarget.id);
      event.preventDefault()
    })
    .on('hold', function (event) {
      if(confirm("{{ __('Delete this table') }}")){
        deleteTable(event.currentTarget.id);
      }

      event.preventDefault()

    })
    .resizable({
    // resize from all edges and corners
    edges: { left: false, right: true, bottom: true, top: false },

    listeners: {
      move (event) {

        var target = event.target
        var x = (parseFloat(target.getAttribute('data-x')) || 0)
        var y = (parseFloat(target.getAttribute('data-y')) || 0)

        // update the element's style
        target.style.width = event.rect.width + 'px'
        target.style.height = event.rect.height + 'px'

        // translate when resizing from top or left edges
        x += event.deltaRect.left
        y += event.deltaRect.top

        target.style.webkitTransform = target.style.transform ='translate(' + x + 'px,' + y + 'px)'

        target.setAttribute('data-x', x);
        target.setAttribute('data-y', y);
      }
    },
    modifiers: [
      // keep the edges inside the parent
      interact.modifiers.restrictEdges({
        outer: 'parent'
      }),

      // minimum size
      interact.modifiers.restrictSize({
        min: { width: 100, height: 50 }
      })
    ],

    inertia: true
  })
  .draggable({
    listeners: { move: dragMoveListener },
    inertia: true,
    modifiers: [
      interact.modifiers.restrictRect({
        restriction: 'parent',
        endOnly: true
      })
    ]
  })

  function dragMoveListener (event) {
        var target = event.target
        // keep the dragged position in the data-x/data-y attributes
        var x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx
        var y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy

        // translate the element
        target.style.webkitTransform =
            target.style.transform =
            'translate(' + x + 'px, ' + y + 'px)'

        // update the posiion attributes
        target.setAttribute('data-x', x)
        target.setAttribute('data-y', y)

    }

    // this function is used later in the resizing and gesture demos
    window.dragMoveListener = dragMoveListener



    </script> 
@endpush

@section('head')
    <!-- Import Interact --->
    <script src="{{ asset('vendor') }}/interact/interact.min.js"></script>
@endsection

@section('content')

<div class="header bg-gradient-info pb-6 pt-5 pt-md-8">
    <div class="container-fluid">
    </div>
</div>

<div class="container-fluid mt--7">
  <div class="row">
    <div class="col">
      <div class="card bg-secondary shadow">
        <div class="card-header border-0">
          <div class="row align-items-center">
            <div class="col-8">
              <h3 class="mb-0">{{ __($title) }}</h3>
            </div>
            <div class="col-4 text-right">
              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#tableModal">
                <i class="ni ni-fat-add"></i></span> {{ __('Add new table') }}
              </button>

              <a href="javascript:saveFloor()" class="btn  btn-success" data-dismiss="modal"><span class="btn-inner--icon"><i class="ni ni-check-bold"></i></span> {{ __('Save') }}</a>  
            </div>
          </div>
        </div>

        <div class="container-fluid py-2" id="floorTabs">
          <div class="row">
            <div class="col-12 col-xl-12">
                <div class="card h-100">
                  <div class="card-body p-3">
                    <div class="nav-wrapper position-relative end-0">
                      <div class="tab-content" id="floorTabsContent">
                        <div class="card card-frame" style="text-align: center; justify-content: center; align-items: center;" >
                          <div class="card-body ">
                            <div class="canva" id="canvaHolder">
                              @foreach ($restoarea->tables as $table)
                                <?php
                              
                                $whString="";
                                if($table->w||$table->h){
                                    $whString="width: ".$table->w."px; height: ".$table->h."px;";
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
                                class="resize-drag {{ $table->rounded=="yes"?"circle":""}}" style="transform: translate({{$table->x}}px, {{$table->y}}px); {{$whString}}" >
                                    <p> {{$table->table_number}} </p>
                                    <span>{{$table->no_of_occupants}}</span>
                                </div>
                              @endforeach
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>

    <!-- Modal -->
    <div class="modal fade" id="tableModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{{__('Manage Table')}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="form-group">
            <label for="exampleFormControlInput1">{{ __('Table name') }}</label>
            <input type="text" class="form-control" id="table_name" name="table_name" placeholder="{{ __('Table name') }}">
            </div>
            <div class="form-group">
            <label for="exampleFormControlInput1">{{ __('Table size') }}</label>
            <input type="number" class="form-control" id="table_size" name="table_size" placeholder="4">
            </div>
            <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="table_round" name="table_round">
            <label class="custom-control-label" for="table_round">{{ __('Round table') }}</label>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close')}}</button>
            <button type="button" data-dismiss="modal" onclick="javascript:saveTable()"  class="btn btn-success"><span class="text-white">{{ __('Save')}}</span></button>
        </div>
        </div>
    </div>
    </div>
@endsection