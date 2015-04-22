<?php

/**
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 3 of the License, or (at
 *   your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful, but
 *   WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 *   General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program; if not, see <http://www.gnu.org/licenses/>.
 *
 *   @author          Black Cat Development
 *   @copyright       2015, Black Cat Development
 *   @link            http://blackcat-cms.org
 *   @license         http://www.gnu.org/licenses/gpl.html
 *   @category        CAT_Modules
 *   @package         lib_chartjs
 *
 */

if (!class_exists('lib_chartjs_Chart'))
{
    if (!class_exists('CAT_Object', false))
    {
        @include CAT_PATH.'/framework/CAT/Object.php';
    }

    class lib_chartjs_Chart extends CAT_Object
    {
        // array to store config options
        protected $_config         = array(
            'loglevel'    => 8,
            'color_scale' => 'Spectral'
        );
        protected static $sums     = array();
        private   static $instance;

        public static function getInstance()
        {
            if (!self::$instance)
            {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function __call($method, $args)
        {
            if ( ! isset($this) || ! is_object($this) )
                return false;
            if ( method_exists( $this, $method ) )
                return call_user_func_array(array($this, $method), $args);
        }

        /**
         * this is an alias for getDoughnutchart()
         *
         * @access public
         * @param  array  $options
         * @return string
         **/
        public static function getDonutchart($options)
        {
            $options['type'] = 'doughnut';
            return self::getPiechart($options);
        }   // end function getDonutchart()

        /**
         * creates a Doughnut chart
         *
         * @access public
         * @param  array  $options
         * @return string
         **/
        public static function getDoughnutchart($options) {
            $options['type'] = 'doughnut';
            return self::getPiechart($options);
        }   // end function getDoughnutchart()

        /**
         * creates a Doughnut chart
         *
         * @access public
         * @param  array  $options
         * @return string
         **/
        public static function getPolarchart($options)
        {
            $options['type'] = 'polar';
            return self::getPiechart($options);
        }   // end function getDoughnutchart()

        /**
         * creates a Pie chart by default; also for Doughnut and Polar
         * ('type' option)
         *
         * @access public
         * @param  array  $options
         * @return string
         **/
        public static function getPiechart($options)
        {

            $options = array(
                'data'        => ( isset($options['data'])        ? $options['data']        : NULL    ),
                'id'          => ( isset($options['id'])          ? $options['id']          : CAT_Users::generateRandomString() ),
                'group_by'    => ( isset($options['group_by'])    ? $options['group_by']    : NULL    ),
                'color_by'    => ( isset($options['color_by'])    ? $options['color_by']    : 'value' ),
                'type'        => ( isset($options['type'])        ? $options['type']        : 'Pie' ),
                'color_scale' => ( isset($options['color_scale']) ? $options['color_scale'] : self::getInstance()->_config['color_scale'] ),
            );

            if(!is_array($options['data']) || !count($options['data'])) return false;

            global $parser;

            $minval    = 0;
            $maxval    = 100;

            $chartdata = array();
            $chartitem = "{\n"
                       . "    value: %%value%%,\n"
                       . "    color: scale(" . ( $options['color_by'] == 'value' ? '%%value%%' : '%%count%%' ) . "),\n"
                       . "    highlight: chroma(scale(%%value%%)).brighten(),\n"
                       . "    label: '%%label%%'\n"
                       . "}\n"
                       ;

            // group example: Firefox
            $cnt   = 1;
            $_temp = 0;
            $last_group = NULL;
            foreach($options['data'] as $group => $items)
            {
                if($options['group_by'])
                {
                    $last_group = $group;
                    $chartdata[] = str_replace(
                        array('%%value%%','%%label%%','%%count%%'),
                        array($items['sum'],$options['data'][$group]['title'],($options['color_by'] == 'value' ? $items['sum'] : $cnt)),
                        $chartitem
                    );
                    if($options['color_by']  == 'value' && $item['count'] > $maxval) $maxval = $item['count'];
                    $cnt++;
                }
                else
                {
                    foreach($items as $item)
                    {
                        if(!is_array($item)) continue;
                        $chartdata[] = str_replace(
                            array('%%value%%','%%label%%','%%count%%'),
                            array($item['count'], $options['data'][$group]['title'], ($options['color_by'] == 'value' ? $item['count'] : $cnt)),
                            $chartitem
                        );
                        if($options['color_by']  == 'value' && $item['count'] > $maxval) $maxval = $item['count'];
                        $cnt++;
                    }
                }
            }

            if($options['color_by'] != 'value') $maxval = $cnt;

            $parser->setPath(CAT_PATH.'/modules/lib_chartjs/templates/default');
            $output = $parser->get($options['type'] .'chart.tpl',array(
                'items'       => $options['data'],
                'idfield'     => $options['id'],
                'color_scale' => $options['color_scale'],
                'minval'      => $minval,
                'maxval'      => $maxval,
                'chartdata'   => implode( ",\n", $chartdata )
            ));
            $parser->resetPath();
            return $output;

        }   // end function getPiechart()

        /**
         * creates a Bar chart
         *
         * @access public
         * @param  array  $options
         * @return string
         **/
        public static function getBarchart($options) {
            $options['type'] = 'bar';
            return self::getLinechart($options);
        }   // end function getBarchart()

        /**
         * creates a Bar chart
         *
         * @access public
         * @param  array  $options
         * @return string
         **/
        public static function getRadarchart($options) {
            $options['type'] = 'radar';
            return self::getLinechart($options);
        }   // end function getRadarchart()

        /**
         * creates a line chart; also used for bar charts
         *
         * @access public
         * @param  array  $options
         * @return string
         **/
        public static function getLinechart($options)
        {

            $options = array(
                'data'        => ( isset($options['data'])        ? $options['data']        : NULL    ),
                'labels'      => ( isset($options['labels'])      ? $options['labels']      : NULL    ),
                'id'          => ( isset($options['id'])          ? $options['id']          : CAT_Users::generateRandomString() ),
                'color_scale' => ( isset($options['color_scale']) ? $options['color_scale'] : self::getInstance()->_config['color_scale'] ),
                'type'        => ( isset($options['type'])        ? $options['type']        : 'Line' ),
            );

            if(!is_array($options['data']) || !count($options['data'])) return false;

            global $parser;

            $chartdata = "{\n"
                       . '    labels: [ "' . implode( '", "', $options['labels'] ) . '" ],' . "\n"
                       . '    datasets: [ %%datasets%% ]'
                       . "}\n"
                       ;

            $datasets = array();
            $minval   = 0;
            $maxval   = 100;

            foreach($options['data']['datasets'] as $datasetname => $items)
            {
                
                $datasets[] = "\n{\n"
                            . "    label: '$datasetname',\n"
                            . "    fillColor: 'rgba(220,220,220,0.2)',\n"
                            . "    strokeColor: scale(10),\n"
                            . "    pointColor: scale(50),\n"
                            . "    pointStrokeColor: '#fff',\n"
                            . "    pointHighlightFill: '#fff',\n"
                            . "    pointHighlightStroke: 'rgba(220,220,220,1)',\n"
                            . "    data: [ " . implode( ', ', array_values($items) ) . " ]\n"
                            . "}"
                            ;
            }

            $chartdata = str_replace(
                '%%datasets%%',
                implode(",\n", $datasets),
                $chartdata
            );

            $parser->setPath(CAT_PATH.'/modules/lib_chartjs/templates/default');
            $output = $parser->get($options['type'].'chart.tpl',array(
                'chartdata'   => $chartdata,
                'idfield'     => $options['id'],
                'minval'      => $minval,
                'maxval'      => $maxval,
                'color_scale' => $options['color_scale'],
            ));
            $parser->resetPath();
            return $output;

        }   // end function getLinechart()

        /**
         *
         * @access public
         * @return
         **/
        public static function supportedTypes()
        {
            return array(
                'pie', 'doughnut', 'polar', 'line'
            );
        }   // end function supportedTypes()

        /**
         * converts a given array of data to a data structure usable for a chart
         * the result is usable for pie and polar area type charts; other charts
         * need labels and a different structure, see ChartJS docs
         *
         * result:
         *     array(
         *         <$group_key> => array(
         *             ...items of $data...
         *         )
         *     )
         *
         * Optional $converts array allows to map functions to values, for example,
         * to convert UNIX timestamps to formatted date:
         *
         * $converts = array(
         *     <$key> => 'CAT_Helper_DateTime::getDateTime'
         * );
         *
         * Optional $internals array allows to map internal functions to values
         *
         *
         *
         * @access public
         * @param  array  $data      - data array
         * @param  string $group_key - name of array key to group by
         * @param  array  $converts  - map array keys to functions
         * @param  array  $internals - map array of keys to internal functions
         * @return array
         **/
        public static function prepareData($options)
        {

            $options = array(
                'data'      => ( isset($options['data'])      ? $options['data']      : NULL ),
                'group_by'  => ( isset($options['group_by'])  ? $options['group_by']  : NULL ),
                'converts'  => ( isset($options['converts'])  ? $options['converts']  : NULL ),
                'internals' => ( isset($options['internals']) ? $options['internals'] : NULL ),
            );

            $result = array();
            if($options['data'])
            {
                if(!is_array($options['data']))
                {
                    $this->logError($this->lang()->translate('No data!'));
                    return false;
                }
                foreach($options['data'] as $index => $item)
                {
                    if(!isset($item[$options['group_by']]))
                    {
                        self::logError(self::lang()->translate('Group key not present in current item!'));
                        continue;
                    }
                    if(!isset($result[$item[$options['group_by']]]))
                    {
                        $result[$item[$options['group_by']]] = array();
                    }
                    self::handleConverts($options['converts'],$item);
                    $result[$item[$options['group_by']]][] = $item;
                }
                self::handleInternals($options['internals'],$options['data'],$result);
            }
            return $result;
        }   // end function prepareData()
        
        /**
         * handle converts; please note that @param arrays are passed by
         * reference!
         *
         * @access private
         * @param  array   $converts
         * @param  array   $item
         * @return void
         **/
        private static function handleConverts(&$converts,&$item)
        {
            if($converts)
            {
                foreach($item as $key => $value)
                {
                    if(isset($converts[$key]))
                    {
                        $func = $converts[$key];
                        if(substr_count($func,'::'))
                        {
                            list($class,$func) = explode('::',$func,2);
                            if(is_array($value))
                            {
                                $value = call_user_func_array(array($class,$func),$value);
                            }
                            else
                            {
                                $value = call_user_func(array($class,$func),$value);
                            }
                        }
                        else
                        {
                            $value = $func($value);
                        }
                        $item[$key] = $value;
                    }
                }
            }
        }   // end function handleConverts()

        /**
         *
         * @access private
         * @return
         **/
        private static function handleInternals(&$internals,&$data,&$result)
        {
            if($internals)
            {
                foreach($internals as $method => $config)
                {
                    foreach($result as $group => $items)
                    {
                        $arr = self::$method($config,$result[$group]);
                        if(is_array($arr))
                        {
                            $result[$group] = array_merge(
                                $result[$group], $arr
                            );
                        }
                    }
                }
            }
        }   // end function handleInternals()

        /**
         * allows to summarize data on per-group-basis; adds a new key to the
         * result array; the name of the key to be added can be set in the
         * $config array, see below
         *
         * config:
         *     array(
         *         'key' => <Key in $data array>, // default: 'count'
         *         'return_as' => <Key to add>    // default: 'sum'
         *     )
         *
         * return array:
         *     array(
         *         <Key to add> => <Result>
         *     )
         *
         * @access private
         * @param  array   $config
         * @param  array   $data
         * @return array
         **/
        private static function summarize($config,$data)
        {
            $key = isset($config['key'])       ? $config['key']       : 'count';
            $as  = isset($config['return_as']) ? $config['return_as'] : 'sum';
            $sum = 0;
            foreach($data as $index => $item)
            {
                if(isset($item[$key]))
                {
                    $sum += $item[$key];
                }
            }
            return array( $as => $sum );
        }   // end function summarize()

        /**
         *
         * @access private
         * @return
         **/
        private static function title($config,$data)
        {
            $title   = '';
            $key     = isset($config['key']) ? $config['key'] : 'name';
            $found   = array();

            // group items by $key
            foreach($data as $index => $item)
            {
                if(isset($item[$key]) && $item[$key] != '')
                {
                    $title = $item[$key];
                    if(isset($config['additionals']) && is_array($config['additionals']))
                    {
                        $title .= ' ('
                               . implode('; ',array_map(function($key) use($item) { return $item[$key]; }, $config['additionals']))
                               . ')'
                               ;
                    }
                }
            }
            return array( 'title' => $title );
        }   // end function title()
        
    } // class lib_chartjs_Chart

} // if class_exists()