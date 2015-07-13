function nextStep(){

    if (current_step === false){
        current_step = 0;
    } else {
        current_step = current_step + 1;
        showLoadingIndicator();
    }

    var url = 'index.php';
    var data = {step: current_step};
    var type = 'json';

    var callback = function(result){

        if("html" in result){
            $('#body').removeClass('loading').html( result.html );
        }

        $('#steps li').removeClass('active');
        $('#steps li').eq(current_step).addClass('active');

        hideLoadingIndicator();

    }

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

function submitStep(){

    var form_data = formToJSON($('form'));

    form_data.step = current_step;
    form_data.submit = 1;

    var url = 'index.php';

    showLoadingIndicator();

    $.post(url, form_data, function(result){

        hideLoadingIndicator();

        if (result.error == false){
            nextStep();
            return;
        }

        alert(result.message);

    }, 'json');

}

function showLoadingIndicator(){
    $('#body').prepend('<div class="loading-overlay"></div>');
}

function hideLoadingIndicator(){
    $('#body .loading-overlay').remove();
}
