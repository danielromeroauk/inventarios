@if(!Session::has('cart'))
    Carrito vacío.
    <script type="text/javascript">
        window.location.replace("<?php echo url('articles'); ?>");
    </script>
@else
    <?php $cart = Session::get('cart'); ?>
    @if(empty($cart))
        Carrito sin items.
        <script type="text/javascript">
            window.location.replace("<?php echo url('articles'); ?>");
        </script>
    @endif
@endif
