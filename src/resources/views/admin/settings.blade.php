<div class="form-group">
    <div class="custom-control custom-checkbox">
        <input type="checkbox"
               class="custom-control-input"
               id="useCart"
               {{ (! count($errors->all()) && $config->data['useCart']) || old("data-useCart") ? "checked" : "" }}
               name="data-useCart">
        <label class="custom-control-label" for="useCart">Использовать корзину</label>
    </div>
</div>
