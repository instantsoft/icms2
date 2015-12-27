$(function(){

    var ctx = $("#chart-canvas").get(0).getContext("2d");
    var chart;
    var controller, section, period = $('#chart').data('period');
    var dataUrl = $('#chart').data('url');

    function loadChartData(){

        $.cookie('icms[dashboard_chart]', JSON.stringify({
            c: controller, s: section, p: period
        }));

        $.post(dataUrl, {id: controller, section: section, period: period}, function(result){

            if (chart) { chart.destroy(); }

            var data = {
                labels: result.labels,
                datasets: [{
                    label: "My First dataset",
					fillColor : "rgba(100, 131, 157, 0.1)",
					strokeColor : "#3498DB",
					pointColor : "rgba(100, 131, 157, 1)",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "rgba(220,220,220,1)",
                    data: result.values,
                }]
            };

            //console.log(data);

            chart = new Chart(ctx).Bar(data);

        }, 'json');


    }

    $('#chart select').change(function(e){

        var $option = $(this).find('option:selected');

        controller = $option.data('ctrl');
        section = $option.data('section');

        //console.log(controller, section);

        loadChartData();

    }).change();

    $('#chart .pills-menu a').click(function(e){

        e.preventDefault();

        var $link = $(this);

        period = $link.data('period');

        loadChartData();

        $('#chart .pills-menu li').removeClass('active')
        $link.parent('li').addClass('active');

    });

});