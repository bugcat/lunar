<?php namespace Bugcat\Lunar\Traits;

use Bugcat\Lunar\Datas\{Period};

trait GetInfo
{
    
    /**
     * 獲取時間戳
     *
     * @param  mix     $time   輸入的時間 默認當前時間
     * @param  string  $type   輸入時間的格式 支持 int str stamp
     * @return int
     */
    protected function getTimestamp($time = null, string $type = null)
    {
        $now = time();
        $timestamp = 0;
        if ( null === $time ) {
            $timestamp = $now;
        } elseif ( null === $type ) {
            if ( is_object($time) ) {
                $timestamp = $time->getTimestamp();
            } elseif ( is_integer($time) ) {
                $timestamp = intval($time);
            } elseif ( is_string($time) ) {
                $int_time = '' . intval($time) . '';
                if ( $int_time === $time ) {
                    $timestamp = intval($time);
                } else {
                    $timestamp = strtotime($time);
                }
            } else {
                $timestamp = $now;
            }
            
        } else {
            switch ( $type ) {
                case 'int':
                    $timestamp = intval($time);
                    break;  
                case 'str':
                    $timestamp = strtotime($time);
                    break;
                case 'stamp':
                    $timestamp = $time->getTimestamp();
                    break;
                default:
                    $timestamp = $now;
            }
        }
        //判斷時間範圍
        if (
            $timestamp < $this->cfg['min_timestamp'] 
            || $timestamp > $this->cfg['max_timestamp'] 
        ) {
            $err = $this->_error()['limit'];
            throw new \Exception($err);
        }
        return $timestamp;
    }
    
    /**
     * 解析輸入的時間
     *
     * @param  integer $timestamp 輸入時間戳(整型)
     * @return string  
     */
    protected function getDateInfo(int $timestamp = 0)
    {
        //年
        $m_stamp = $this->__yearInfo($timestamp);
        
        //月
        $d_stamp = $this->__monthInfo($m_stamp);
        
        //旬
        //$this->info['X'] = ''; //稱呼旬
        //$this->info['x'] = ''; //數字旬
        //$this->info['S'] = ''; //每旬第幾天
        //$x_stamp = $this->__tenDaysInfo($d_stamp);
        
        //天
        $h_stamp = $this->__dayInfo($d_stamp);
        
        //时间
        $t_stamp = $this->__timeInfo($h_stamp);
        
    }
    
    /**
     * 獲取年相關信息
     *
     * @param  integer $timestamp
     * @return integer
     */
    private function __yearInfo(int $timestamp = 0)
    {
        $year_part = 0;
        foreach ( Period::YearStamps as $_Y => $stamp ) {
            if ( $timestamp >= $stamp ) {
                $year_part = $_Y;
            } else {
                break;
            }
        }
        $year_all = $year_part + Period::BaseYear[$this->cfg['era']] ?? 0;
        
        //$this->info['E'] = 'ZH'; //紀元
        $this->info['Y'] = $year_part; //紀元簡化年
        //$this->info['y'] = ''; //干支年
        $this->info['o'] = $year_all; //紀元完整年
        //$this->info['Z'] = ''; //生肖年
        
        $m_stamp = $timestamp - Period::YearStamps[$year_part];
        return $m_stamp;
    }
    
    /**
     * 獲取月相關信息
     *
     * @param  integer $m_stamp
     * @return integer
     */
    private function __monthInfo(int $m_stamp = 0)
    {
        $d_stamp = $m_stamp;
        $Y = $this->info['Y'];
        $m_days = Period::MonthDays[$Y];
        $leap = 0;
        $month = 0;
        $month_day = 0;
        for ( $i = 1; $i <= 12; $i++ ) {
            $day = $m_days[$i];
            $_stamp = $d_stamp - $day * 86400;
            if ( $_stamp >= 0 ) {
                //如果剩餘時間大於零
                $d_stamp = $_stamp;
            } else {
                $month = $i;
                $month_day = $day;
                break;
            }
        }
        //判断是否润月
        if ( $month_day > 30 ) {
            $day_nums = ceil($d_stamp / 86400);
            if ( $day_nums <= $m_days[0] ) {
                $leap = 0; //平月
                $month_day = $m_days[0];
            } else {
                $leap = 1; //润月
                $month_day -= $m_days[0];
                $d_stamp -= $m_days[0] * 86400;
            }
        }
        
        $this->info['L'] = $leap; //是否爲潤月
        $this->info['M'] = $month; //數字月份
        //$this->info['m'] = ''; //干支年
        //$this->info['F'] = ''; //生肖月
        $this->info['t'] = $month_day; //指定月份有幾天
        
        return $d_stamp;
    }
    
    /**
     * 獲取天相關信息
     *
     * @param  integer $d_stamp
     * @return integer
     */
    private function __dayInfo(int $d_stamp = 0)
    {
        $h_stamp = $d_stamp;
        $day = 0;
        for ( $i = 1; $i <= 30; $i++ ) {
            $_stamp = $d_stamp - $i * 86400;
            if ( $_stamp >= 0 ) {
                //如果剩餘時間大於零
                $h_stamp = $_stamp;
            } else {
                $day = $i;
                break;
            }
        }
        
        $this->info['D'] = $day; //數字日
        //$this->info['d'] = ''; //干支日
        $this->info['j'] = $day; //月份中的第幾天
        //$this->info['z'] = ''; //年份中的第幾天
        //$this->info['l'] = ''; //生肖日
        
        return $h_stamp;
    }
    
    /**
     * 獲取時間相關信息
     *
     * @param  integer $h_stamp
     * @return integer
     */
    private function __timeInfo(int $h_stamp = 0)
    {
        $t_stamp = $h_stamp;
        $DH = 0; //Double Hours
        for ( $i = 0; $i < 12; $i++ ) {
            $_stamp = $h_stamp - ($i + 1) * 7200;
            if ( $_stamp >= 0 ) {
                //如果剩餘時間大於零
                $t_stamp = $_stamp;
            } else {
                $DH = $i;
                break;
            }
        }
        $Q = 0; //Quarter Clock
        for ( $j = 0; $j < 8; $j++ ) {
            $_stamp = $t_stamp - ($j + 1) * 900;
            if ( $_stamp >= 0 ) {
                //如果剩餘時間大於零
            } else {
                $Q = $j;
                break;
            }
        }
        
        $this->info['H'] = $DH; //數字時辰
        $this->info['h'] = $DH; //干支時辰
        //$this->info['A'] = ''; //生肖時
        //$this->info['B'] = ''; //更
        //$this->info['G'] = ''; //時辰別稱
        $this->info['Q'] = $Q; //刻鍾
        
        return $t_stamp;
    }
    
    
    
    /**
     * 獲取不帶位數的數字
     *
     * @param  integer $number
     * @return string
     */
    private function getNumNoKey($number = 0)
    {
        $str = '';
        $text_arr = $this->_number();
        $num_arr = str_split($number, 1);
        foreach ( $num_arr as $num ) {
            $str .= $text_arr[$num] ?? '';
        }
        return $str;
    }
    
    /**
     * 獲取帶位數的數字 TODO
     *
     * @return string
     */
    private function getNumWithKey()
    {
        
    }
    
}