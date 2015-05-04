    <canvas class="chart" id="{$idfield}" width="300" height="300"></canvas>
    <div id="{$idfield}Legend"></div>
    <script charset=utf-8 type="text/javascript">
    //<![CDATA[
    jQuery(document).ready(function($) {
        var scale = chroma.scale('{$color_scale}').domain([{$minval},{$maxval}]);
        var _ChartDataCtx_{$idfield} = [ {$chartdata} ];
        var Ctx_{$idfield} = $('#{$idfield}').get(0).getContext("2d");
        window.myPieCtx_{$idfield} = new Chart(Ctx_{$idfield}).PolarArea(_ChartDataCtx_{$idfield},{
            animationEasing: "easeOutCubic"
        });
        legend(document.getElementById('{$idfield}Legend'), _ChartDataCtx_{$idfield});
    });

    //]]>
    </script>

 