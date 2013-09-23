@if(!Session::has('cart'))
    <div class="alert alert-warning">
        No hay carrito.
    </div>
    <script type="text/javascript">
        window.location.replace("<?php echo url('articles'); ?>");
    </script>
@else
    <?php $cart = Session::get('cart'); ?>
    @if(empty($cart))
        <div class="alert alert-warning">
            Carrito sin items.
        </div>
        <script type="text/javascript">
            window.location.replace("<?php echo url('articles'); ?>");
        </script>
    @endif
@endif
