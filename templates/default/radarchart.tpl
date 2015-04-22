    <canvas class="chart" id="{$idfield}" style="width:100%;height:300px;"></canvas>
    <div id="{$idfield}Legend"></div>
    <script charset="utf-8" type="text/javascript">
    //<![CDATA[
    var scale = chroma.scale('{$color_scale}').domain([{$minval},{$maxval}]);
    var _ChartDataCtx_{$idfield} = {$chartdata};

    jQuery(document).ready(function($) {
        var Ctx_{$idfield} = $('#{$idfield}').get(0).getContext("2d");
        window.myLnCtx_{$idfield} = new Chart(Ctx_{$idfield}).Radar(_ChartDataCtx_{$idfield},{
            animationEasing: "easeOutCubic"
        });
    });
    //]]>
    </script>

