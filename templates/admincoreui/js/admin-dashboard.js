$(function(){

    var ctx, chart, controller, section, interval, dataUrl, type;
    var chart_data = {};

    function initChart(){

        ctx = $("#chart-canvas");
        interval = $('#chart').data('interval');
        dataUrl = $('#chart').data('url');
        type = $('#chart').data('type');

        $('#chart select').on('change', function(e){

            var $option = $(this).find('option:selected');

            controller = $option.data('ctrl');
            section = $option.data('section');

            loadChartData();

        }).triggerHandler('change');

        $('#menu-period button').on('click', function(e){

            e.preventDefault();

            var $link = $(this);

            interval = $link.data('interval');

            $('#menu-period button').removeClass('active is-busy');
            $(this).addClass('is-busy');

            loadChartData(function (){
                $($link).addClass('active');
                $($link).removeClass('is-busy');
            });
        });

        $('#toggle-type').on('click', function(e){

            if($(this).hasClass('btn-primary')){
                type = 'bar';
            } else {
                type = 'line';
            }

            $(this).toggleClass('btn-primary btn-outline-secondary');

            renderChart();
        });

    };

    function loadChartData(callback){

        callback = callback || function(){};

        $('#chart-spinner').show();

        $.post(dataUrl, {id: controller, section: section, interval: interval}, function(result){

            chart_data = result.result.chart_data;

            $('.chart-footer-show').remove();
            if(result.result.footer.length > 0){
                $('#chart-footer').show();
                for (var item in result.result.footer) {

                    var list_template = $('#chart-footer-tpl').clone(true).addClass('chart-footer-show');
                    $('.text-muted', list_template).html(result.result.footer[item].title);
                    $('strong', list_template).html(result.result.footer[item].count);
                    if(result.result.footer[item].progress){
                        $('.callout', list_template).addClass('callout-'+result.result.footer[item].progress);
                    }
                    $('#chart-footer > .row').append($(list_template).show());
                }
            } else {
                $('#chart-footer').hide();
            }

            callback();

            renderChart();

        }, 'json');
    };

    function renderChart(){

        $.cookie('icms[dashboard_chart]', JSON.stringify({
            c: controller, s: section, i: interval, t: type
        }));

        if (chart) { chart.destroy(); }
        var progress = $('#chart-spinner');
        chart = new Chart(ctx, {
            type: type,
            options: {
                animation: {
					onComplete: function() {
						$(progress).hide();
                    }
                },
                legend: {
                    display: false
                },
                tooltips: {
                    mode: 'point'
                },
                maintainAspectRatio: false,
                scales: {
                    xAxes: [{
                        gridLines: {
                            drawOnChartArea: false
                        }
                    }],
                    yAxes: [{
                        ticks: {
                            stepSize: 1,
                            beginAtZero: true
                        }
                    }]
                }
            },
            data: chart_data
        });
    }

    $('#dashboard').sortable({
        items: ".is-sortable:not(.disabled)",
        handle: '.card-header > .db-sortable-handle',
        cursor: 'move',
        opacity: 0.85,
        delay: 150,
        connectWith:".is-sortable:not(.disabled)",tolerance:'pointer',forcePlaceholderSize:true,
        placeholder: 'colplaceholder',
        start: function(event, ui) {
            $(ui.placeholder).addClass($(ui.item).attr('class'));
            $(ui.placeholder).height($(ui.item).height());
        },
        update: function(event, ui) {
            var id_list = new Array();
            $('#dashboard .is-sortable:not(.disabled)').each(function(){
                var name = $(this).data('name');
                id_list.push(name);
            });
            $.post($('#dashboard').data('save_order_url'), {items: id_list}, function(result){
                toastr.success(result.success_text);
            }, 'json');
        }
    }).disableSelection();

    if($('#chart select').length > 0){
        initChart();
    }

});