    <canvas class="chart" id="{$idfield}" width="300" height="300"></canvas>
    <div id="{$idfield}Legend"></div>
    <script charset=utf-8 type="text/javascript">
    //<![CDATA[
    var scale = chroma.scale('{$color_scale}').domain([{$minval},{$maxval}]);
    var _ChartDataCtx_{$idfield} = [ {$chartdata} ];

    jQuery(document).ready(function($) {
        var Ctx_{$idfield} = $('#{$idfield}').get(0).getContext("2d");
        window.myPieCtx_{$idfield} = new Chart(Ctx_{$idfield}).Doughnut(_ChartDataCtx_{$idfield},{
            animationEasing: "easeOutCubic"
        });
        legend(document.getElementById('{$idfield}Legend'), _ChartDataCtx_{$idfield});
    });

    //]]>
    </script>

 