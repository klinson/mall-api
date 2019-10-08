<div class="form-group {!! !$errors->has($errorKey) ?: 'has-error' !!}">

    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{{$label}}</label>

    <div class="{{$viewClass['field']}} {{$class}}" id="{{$id}}" {!! $attributes !!}>

        @include('admin::form.error')

        <div class="checkbox">
            <label>
                <input class="area" id="all-area" type="checkbox" name="{{ $name }}[]" value="{{ $area['code'] }}" @if(in_array($area['code'] ,old($column, $value ?: []))) checked="checked" @endif>
                <b>{{ $area['name'] }}</b>
            </label>
        </div>
        @foreach($area['children'] as $province)
            <div style="border-top: 1px solid #eee;border-right: 1px solid #eee;border-left: 1px solid #eee;">
                <label>
                    <input class="area province" type="checkbox" name="{{ $name }}[]" value="{{ $province['code'] }}" data-pcode="{{$province['code']}}" @if(in_array($province['code'] ,old($column, $value ?: []))) checked="checked" @endif>
                    {{ $province['name'] }}
                </label>

                <div style="padding-left: 10px">
                    @foreach($province['children'] as $city)
                        <label class="checkbox-inline">
                            <input class="area city province-{{$province['code']}}" type="checkbox" name="{{ $name }}[]" data-pcode="{{$province['code']}}" value="{{ $city['code'] }}" @if(in_array($city['code'] ,old($column, $value ?: []))) checked="checked" @endif> {{ $city['name'] }}
                        </label>
                    @endforeach
                </div>
            </div>
        @endforeach

        @include('admin::form.help-block')

    </div>
</div>
