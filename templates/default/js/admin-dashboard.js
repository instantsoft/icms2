$(function(){

    var ctx = $("#chart-canvas").get(0).getContext("2d");
    var chart;
    var controller, section, period = $('#chart').data('period');
    var dataUrl = $('#chart').data('url');
    var chart_data = {};

    function loadChartData(){

        $.cookie('icms[dashboard_chart]', JSON.stringify({
            c: controller, s: section, p: period
        }));

        $.post(dataUrl, {id: controller, section: section, period: period}, function(result){

            chart_data = {
                labels: result.labels,
                datasets: [{
                    label: "My First dataset",
					fillColor : "rgba(100, 131, 157, 0.1)",
					strokeColor : "#3498DB",
					pointColor : "rgba(100, 131, 157, 1)",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "rgba(220,220,220,1)",
                    data: result.values
                }]
            };

            renderChart();

        }, 'json');


    }

    function renderChart(){
        if (chart) { chart.destroy(); }
        chart = new Chart(ctx).Bar(chart_data);
    }

    $('#chart select').change(function(e){

        var $option = $(this).find('option:selected');

        controller = $option.data('ctrl');
        section = $option.data('section');

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

    $(window).on('resize', function (){
        renderChart();
    });

    $('#dashboard').sortable({
        items: ".col:not(.disabled)",
        handle: '.drag',
        cursor: 'move',
        opacity: 0.9,
        delay: 150,
        revert: true,
        placeholder: 'colplaceholder',
        start: function(event, ui) {
            $(ui.placeholder).addClass($(ui.item).attr('class'));
            $(ui.placeholder).height($(ui.item).height());
        },
        update: function(event, ui) {
            renderChart();
            var id_list = new Array();
            $('#dashboard .col:not(.disabled)').each(function(){
                var id = $(this).data('id');
                id_list.push(id);
            });
            $.post($('#dashboard').data('save_order_url'), {items: id_list}, function(){});
        }
    });

});