<div class="form-group">
    @include('widget.select-single', ['name' => 'scope[billing]', 'lists' => $billing, 'selected' => $scope->billing])
</div>

<div class="form-group">
    @include('widget.select-single', ['name' => 'scope[currency]', 'lists' => $currency, 'selected' => $scope->currency])
</div>

