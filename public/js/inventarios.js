(function($){

    $(document).on('ready', iniciar);

    function iniciar() {
        var contexto = $('#Form');

        $('input[type=number]', contexto).popover({
            'title': 'Cantidad',
            'content': 'Use coma (,) como separador decimal.'
        });

        countdown('contador');

    }

})(jQuery);
