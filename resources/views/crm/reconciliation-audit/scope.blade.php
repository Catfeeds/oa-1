<div class="form-group">
    @include('widget.select-single', ['name' => 'product_id', 'lists' => $products, 'selected' => $pid])
</div>

<div class="form-group">
    @include('widget.select-single', ['name' => 'scope[os]', 'lists' => \App\Http\Components\Helpers\CrmHelper::addEmptyToArray('请选择系统', \App\Models\Crm\Reconciliation::$sys), 'selected' => $scope->os])
</div>

<div class="form-group">
    <input type="text" name="scope[backstage_channel]" value="{{ $scope->backstage_channel }}" class="form-control col-xs-6"
           placeholder="{{ trans('crm.诗悦后台渠道') }}">
</div>

<div class="form-group">
    <input type="text" name="scope[unified_channel]" value="{{ $scope->unified_channel }}" class="form-control col-xs-6"
           placeholder="{{ trans('crm.统一渠道名称') }}">
</div>

<div class="form-group">
    <input type="text" name="scope[client]" value="{{ $scope->client }}" class="form-control col-xs-6"
           placeholder="{{ trans('crm.客户') }}">
</div>



