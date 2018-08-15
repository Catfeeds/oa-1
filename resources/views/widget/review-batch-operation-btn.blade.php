<div class="row">

    <div class="col-sm-1 m-b-xs" style="width: 50px;">
        <input name="check" type="button" class="btn btn-primary btn-sm" onclick="$('input').iCheck('check');"
               value="{{ trans('att.全选') }}"/>
    </div>

    <div class="col-sm-1 m-b-xs" style="width: 50px;">
        <input name="check" type="button" class="btn btn-primary btn-sm" onclick="
        $('#text_box*:checkbox').each(function () {
            if ($(this).prop('checked')){
                $(this).iCheck('uncheck');
            }else{
                $(this).iCheck('check');
            }
        })" value="{{ trans('att.反选') }}"/>
    </div>
    <div class="col-sm-1 m-b-xs" style="width: 75px;">
        <input name="check" type="button" class="btn btn-primary btn-sm" onclick="$('input').iCheck('uncheck');"
               value="{{ trans('att.全部取消') }}"/>
    </div>
    @foreach($btn as $v)
        <div class="col-sm-1 m-b-xs" style="width: 55px;margin-right: 1.5em">
            <button class="btn {{ $v[2] }} btn-sm" id="{{ $v[0] }}" data-toggle="tooltip" title=""
                    data-original-title="{{ $v[1] }}"> {{ $v[1] }}
            </button>
        </div>
    @endforeach
</div>
