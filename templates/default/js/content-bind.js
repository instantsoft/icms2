var icms = icms || {};

icms.contentBind = (function ($) {

    var $form;
    var mode, values = {}, valuesCount = 0, authors = 'own';

    $form = $('#content_bind_form');
    mode = $form.data('mode');

    if (mode == 'childs' || mode == 'unbind'){
        initModal();
    }

	this.start = function(options){

        if (mode != 'childs' && mode != 'unbind'){
            icms.modal.openAjax(options.url, {}, function(){
                initModal(options);
            }, options.modal_title);
        }

		return false;

	};

	function initModal(options){

		$form = $('#content_bind_form');

		var $input = $form.find('#item-find-input');
		var $field = $form.find('#item-find-field');
		var $button = $form.find('.find .button');
		var $submitButton = $form.find('.buttons .button-submit');
		var $resultPane = $form.find('.result-pane');
		var $loading = $form.find('.spinner');

		values = {}, valuesCount = 0;
        mode = $form.data('mode');
        authors = $form.find('.pills-menu .active a').data('mode');

		$input.keyup(function(e){
			if (e.keyCode == 13){
				findItems();
			}
		});

		$button.click(function(e){
			e.preventDefault();
			findItems();
		});

		$form.find('.pills-menu a').click(function(e){
			e.preventDefault();
			var $link = $(this);
			authors = $link.data('mode');
			$form.find('.pills-menu .active').removeClass('active');
			$link.parent('li').addClass('active');
			findItems();
		});

		$submitButton.click(function(e){

			e.preventDefault();

            if (mode == 'childs' || mode == 'unbind'){
                var $postForm = $form.find('form');
                $postForm.find('input.selected_ids').val(Object.keys(values).join(','));
                $postForm.submit();
                return;
            }

            if ('callback' in options){
                options.callback(values);
            }

			icms.modal.close();

		});

		findItems();

		function findItems(){

			$loading.addClass('show');

			var text = $input.val();

            text = text ? text.trim() : '';

			var query = {
				field: $field.val(),
				authors: authors,
				text: text,
				mode: mode
			};

			$.post($form.data('filter-url'), query, function(result){

				$resultPane.find('ul').remove();
				$resultPane.prepend(result);

				$resultPane.find('ul li .button').click(function(){

					var $item = $(this).parents('li');

					var id = $item.data('id');
					values[id] = $item.find('.title a').text();
					valuesCount++;

					$item.fadeOut(300, function(){
						$item.remove();
					});

					$submitButton.val($submitButton.data('title') + ' (' + valuesCount + ')');

					$form.find('.buttons').css({visibility: 'visible'});

				});

				$resultPane.find('ul li').each(function(){
					var $item = $(this);
					var id = $item.data('id');
					if (id in values) {
						$item.remove();
					}
				});

				$loading.removeClass('show');

			});

		}

    }

	return this;

}).call(icms.contentBind || {}, jQuery);
