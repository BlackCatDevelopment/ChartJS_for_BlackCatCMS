<script charset=utf-8 type="text/javascript">
    jQuery(document).ready(function($) {
        $('canvas.chart').each( function() {
alert($(this).data('chart'));
            var Ctx = $('#' + $(this).prop('id')).get(0).getContext("2d");
            window.myPie = new Chart(Ctx).Pie($(this).data('chart'),{
                animationEasing: "easeOutCubic"
            });
            //legend(document.getElementById("browserLegend"), browserChartData);
        });
    });
</script>
