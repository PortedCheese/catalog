<div class="modal fade"
     id="orderInfo{{ $order->id }}"
     tabindex="-1"
     role="dialog"
     aria-labelledby="orderInfo{{ $order->id }}Label"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderInfo{{ $order->id }}Label">Информация</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @php($attr = \App\Order::getAttributesForRender())
                <div class="table-responsive">
                    <table class="table table-borderless">
                        <thead>
                        <tr>
                            <th>Аттрибут</th>
                            <th>Значение</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($userData as $key => $value)
                            <tr>
                                <td>{{ ! empty($attr[$key]) ? $attr[$key] : $key }}</td>
                                <td>{{ $value }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>