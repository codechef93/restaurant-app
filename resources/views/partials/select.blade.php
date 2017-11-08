<div id="form-group-{{ $id }}" class="form-group {{ $errors->has($id) ? ' has-danger' : '' }}  @isset($class) {{$class}} @endisset">

    @isset($separator)
    <br />
    <h4 class="display-4 mb-0">{{ __($separator) }}</h4>
    <hr />
@endisset

    <label class="form-control-label">{{ __($name) }}</label><br />

    <select @isset($disabled) {{ "disabled" }} @endisset  class="form-control form-control-alternative   @isset($classselect) {{$classselect}} @endisset"  name="{{ $id }}" id="{{  $id }}">
        <option disabled selected value> {{ __('Select')." ".__($name)}} </option>
        @foreach ($data as $key => $item)

            @if (is_array(__($item)))
                <option value="{{ $key }}">{{ $item }}</option>
            @else
                @if (old($id)&&old($id).""==$key."")
                    <option  selected value="{{ $key }}">{{ __($item) }}</option>
                @elseif (isset($value)&&trim(strtoupper($value.""))==trim(strtoupper($key."")))
                    <option  selected value="{{ $key }}">{{ __($item) }}</option>
                @elseif (app('request')->input($id)&&strtoupper(app('request')->input($id)."")==strtoupper($key.""))
                    <option  selected value="{{ $key }}">{{ __($item) }}</option>
                @else
                    <option value="{{ $key }}">{{ __($item) }}</option>
                @endif
            @endif
            
        @endforeach
    </select>


    @isset($additionalInfo)
        <small class="text-muted"><strong>{{ __($additionalInfo) }}</strong></small>
    @endisset
    @if ($errors->has($id))
        <span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first($id) }}</strong>
        </span>
    @endif
</div>
