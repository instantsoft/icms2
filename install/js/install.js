function nextStep() {

    if (current_step === false) {
        current_step = 0;
    } else {
        current_step = current_step + 1;
        showLoadingIndicator();
    }

    let url = 'index.php';
    let data = {step: current_step};
    let type = 'json';
    let page = $('#body > .page');

    page.removeClass('animated fadeIn');

    var callback = function (result) {

        if ("html" in result) {
            page.html(result.html);
            page.addClass('animated fadeIn');
        }

        $('#steps li').removeClass('active');
        $('#steps li').eq(current_step).addClass('active');

        hideLoadingIndicator();
    };

    $.post(url, data, callback, type);
}

function formToJSON( form ){
    var o = {};
    var a = form.serializeArray();
    $.each(a, function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};

function submitStep(url){

    var form_data = formToJSON($('form'));

    form_data.step = current_step;
    form_data.submit = 1;

    url = url || 'index.php';

    showLoadingIndicator();

    $.post(url, form_data, function(result){

        hideLoadingIndicator();

        if (result.error === false){

            nextStep();

            if (result.warning){
                Swal.fire({
                  type: 'info',
                  text: result.warning
                });
            }

            return;
        }

        Swal.fire({
          type: 'error',
          title: LANG_ERROR,
          text: result.message,
          footer: LANG_MANUAL
        });

    }, 'json');
}

function showLoadingIndicator(){
    $('#body > .page').prepend('<div class="loading-overlay"><div class="sk-circle"><div class="sk-circle1 sk-child"></div><div class="sk-circle2 sk-child"></div><div class="sk-circle3 sk-child"></div><div class="sk-circle4 sk-child"></div><div class="sk-circle5 sk-child"></div><div class="sk-circle6 sk-child"></div><div class="sk-circle7 sk-child"></div><div class="sk-circle8 sk-child"></div><div class="sk-circle9 sk-child"></div><div class="sk-circle10 sk-child"></div><div class="sk-circle11 sk-child"></div><div class="sk-circle12 sk-child"></div></div></div>');
}

function hideLoadingIndicator(){
    $('#body .loading-overlay').fadeOut('slow', function() {
        $(this).remove();
    });
}
