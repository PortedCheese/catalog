<div class="form-group">
    <div class="custom-control custom-checkbox">
        <input type="checkbox"
               class="custom-control-input"
               id="hasExchange"
               {{ (! count($errors->all()) && $config->data['hasExchange']) || old("data-hasExchange") ? "checked" : "" }}
               name="data-hasExchange">
        <label class="custom-control-label" for="hasExchange">Есть выгрузка</label>
    </div>
</div>

<h4>Корзина</h4>
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

<div class="form-group">
    <label for="oldCardLive">Сколько живут корзины</label>
    <input type="text"
           id="oldCardLive"
           name="data-oldCardLive"
           value="{{ old("data-oldCardLive", $config->data["oldCardLive"]) }}"
           class="form-control @error("data-oldCardLive") is-invalid @enderror">
    @error("data-oldCardLive")
        <div class="invalid-feedback" role="alert">
            {{ $message }}
        </div>
    @enderror
</div>

<div class="form-group">
    <label for="data-cartsAdminPager">Корзин на страницу в админке</label>
    <input type="number"
           min="1"
           id="data-cartsAdminPager"
           name="data-cartsAdminPager"
           value="{{ old("data-cartsAdminPager", $config->data['cartsAdminPager']) }}"
           class="form-control @error("data-cartsAdminPager") is-invalid @enderror">
    @error("data-cartsAdminPager")
    <div class="invalid-feedback" role="alert">
        {{ $message }}
    </div>
    @enderror
</div>

<h4>Товары</h4>
<div class="form-group">
    <div class="custom-control custom-checkbox">
        <input type="checkbox"
               class="custom-control-input"
               id="disablePriceSort"
               {{ (! count($errors->all()) && $config->data['disablePriceSort']) || old("data-disablePriceSort") ? "checked" : "" }}
               name="data-disablePriceSort">
        <label class="custom-control-label" for="disablePriceSort">Отключить сортировку по цене в каталоге</label>
    </div>
</div>

<div class="form-group">
    <label for="data-productsAdminPager">Товаров на страницу в админке</label>
    <input type="number"
           min="1"
           id="data-productsAdminPager"
           name="data-productsAdminPager"
           value="{{ old("data-productsAdminPager", $config->data['productsAdminPager']) }}"
           class="form-control @error("data-productsAdminPager") is-invalid @enderror">
    @error("data-productsAdminPager")
    <div class="invalid-feedback" role="alert">
        {{ $message }}
    </div>
    @enderror
</div>

<div class="form-group">
    <label for="data-productStatesAdminPager">Метки товаров на страницу в админке</label>
    <input type="number"
           min="1"
           id="data-productStatesAdminPager"
           name="data-productStatesAdminPager"
           value="{{ old("data-productStatesAdminPager", $config->data['productStatesAdminPager']) }}"
           class="form-control @error("data-productStatesAdminPager") is-invalid @enderror">
    @error("data-productStatesAdminPager")
    <div class="invalid-feedback" role="alert">
        {{ $message }}
    </div>
    @enderror
</div>

<div class="form-group">
    <label for="data-productsSitePager">Товаров на страницу на сайте</label>
    <input type="number"
           min="1"
           id="data-productsSitePager"
           name="data-productsSitePager"
           value="{{ old("data-productsSitePager", $config->data['productsSitePager']) }}"
           class="form-control @error("data-productsSitePager") is-invalid @enderror">
    @error("data-productsSitePager")
    <div class="invalid-feedback" role="alert">
        {{ $message }}
    </div>
    @enderror
</div>

<h4>Заказы</h4>

<div class="form-group">
    <label for="data-ordersAdminPager">Заказов на страницу в админке</label>
    <input type="number"
           min="1"
           id="data-ordersAdminPager"
           name="data-ordersAdminPager"
           value="{{ old("data-ordersAdminPager", $config->data['ordersAdminPager']) }}"
           class="form-control @error("data-ordersAdminPager") is-invalid @enderror">
    @error("data-ordersAdminPager")
        <div class="invalid-feedback" role="alert">
            {{ $message }}
        </div>
    @enderror
</div>

<div class="form-group">
    <label for="data-ordersProfilePager">Заказов на страницу в профиле</label>
    <input type="number"
           min="1"
           id="data-ordersProfilePager"
           name="data-ordersProfilePager"
           value="{{ old("data-ordersProfilePager", $config->data['ordersProfilePager']) }}"
           class="form-control @error("data-ordersProfilePager") is-invalid @enderror">
    @error("data-ordersProfilePager")
        <div class="invalid-feedback" role="alert">
            {{ $message }}
        </div>
    @enderror
</div>

<div class="form-group">
    <label for="data-orderNotificationEmail">Email для оповещения о заказах</label>
    <input type="email"
           required
           id="data-orderNotificationEmail"
           name="data-orderNotificationEmail"
           value="{{ old("data-orderNotificationEmail", $config->data["orderNotificationEmail"]) }}"
           class="form-control @error("data-orderNotificationEmail") is-invalid @enderror">
    @error("data-orderNotificationEmail")
        <div class="invalid-feedback" role="alert">
            {{ $message }}
        </div>
    @enderror
</div>
