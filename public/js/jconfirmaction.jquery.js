/*
 * jQuery Plugin : jConfirmAction
 *
 * by Hidayat Sagita
 * http://www.webstuffshare.com
 * Licensed Under GPL version 2 license.
 *
 * Modificado por Daniel Guillermo Romero Gelvez para la jQuery 1.10.x
 *
 */
(function($){

	jQuery.fn.jConfirmAction = function (options) {

		// Some jConfirmAction options (limited to customize language) :
		// question : a text for your question.
		// yesAnswer : a text for Yes answer.
		// cancelAnswer : a text for Cancel/No answer.
		var theOptions = jQuery.extend ({
			question: "Estás seguro?",
			yesAnswer: "Guardar",
			cancelAnswer: "Cancelar"
		}, options);

		return this.each (function () {

			$(this).on('click', function(e) {

				e.preventDefault();
				thisHref	= $(this).attr('href');

				if($(this).next('.question').length <= 0)
					$(this).after('<div class="question">'+theOptions.question+'<br/> <span class="yes">'+theOptions.yesAnswer+'</span><span class="cancel">'+theOptions.cancelAnswer+'</span></div>');

				$(this).next('.question').animate({opacity: 1}, 300);

				$('.yes').on('click', function(){
					window.location = thisHref;
				});

				$('.cancel').on('click', function(){
					$(this).parents('.question').fadeOut(300, function() {
						$(this).remove();
					});
				});

			});

		});
	}

})(jQuery);