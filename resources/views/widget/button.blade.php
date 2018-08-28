<div class="row">
    <div class="col-sm-1 m-b-xs" style="width: 50px;">
        <input name="check" type="button" class="btn btn-default btn-sm"
               onclick="$('input').iCheck('check');"
               value="{{ trans('crm.全选') }}"/>
    </div>

    <div class="col-sm-1 m-b-xs" style="width: 50px;">
        <input name="check" type="button" class="btn btn-default btn-sm" onclick="
        $('input:checkbox').each(function () {
            if ($(this).prop('checked')){
                $(this).iCheck('uncheck');
            }else{
                $(this).iCheck('check');
            }
        })" value="{{ trans('crm.反选') }}"/>
    </div>
    <div class="col-sm-1 m-b-xs" style="width: 75px;">
        <input name="check" type="button" class="btn btn-default btn-sm"
               onclick="$('input').iCheck('uncheck');"
               value="{{ trans('crm.全部取消') }}"/>
    </div>
    @if(isset($btn))
        @foreach($btn as $v)
            <div class="col-sm-1 m-b-xs" style="width: 75px;">
                <button class="btn {{ $v[2] }} btn-sm" id="{{ $v[0] }}" data-toggle="tooltip" title=""
                        data-original-title="{{ $v[1] }}"> {{ $v[1] }}
                </button>
            </div>
        @endforeach
    @endif
</div>
