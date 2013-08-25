(function($){
    $(document).on('ready', iniciar);

    function iniciar() {
        var contexto = $('#Form');

        $('input[type=number]', contexto).popover({
            'title': 'Cantidad',
            'content': 'Use coma (,) como separador decimal.'
        });

        // $(':text').popover();*

        // alert($('select', articleForm).val());
    }

})(jQuery);
