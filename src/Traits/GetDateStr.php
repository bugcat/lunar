<?php namespace Bugcat\Lunar\Traits;

trait GetDateStr
{
    /**
     * 支持的格式
     *
     * @static array
     */
    private static $formats = [
        'Y' => '__numNoKey',
        //'y' => '__yearGZ',
        'o' => '__numNoKey',
        'M' => '__MonthNum',
        't' => '__numNoKey',
        'D' => '__DayNum',
        'j' => '__numNoKey',
        'H' => '__numNoKey',
        'h' => '__DHDZ',
        'Q' => '__numNoKey',
    ];
    
    /**
     * 獲取時間字符串
     *
     * @param  string  $format 輸出的時間格式
     * @param  integer $type   輸出的時間類型
     * @return string  
     */
    protected function dateStr(string $format = null, string $type = 'string')
    {
        $format = $format ?? $this->cfg['default_format'];
        $strlen = mb_strlen($format, $this->cfg['encoding']);
        if ( 0 == $strlen ) {
            return '';
        }
        $str_arr = [];
        for ( $i = 0; $i < $strlen; $i++ ) {
            $char = mb_substr($format, $i, 1, $this->cfg['encoding']);
            if ( 'string' == $type ) {
                $mothod = self::$formats[$char] ?? null;
                if ( !empty($mothod) && method_exists($this, $mothod) ) {
                    $str_arr[] = $this->$mothod($char);
                } else {
                    $str_arr[] = $this->info[$char] ?? $char;
                }
            } else {
                $str_arr[] = $this->info[$char] ?? $char;
            }
        }
        return implode('', $str_arr);
    }
    
    /**
     * 獲取不帶位數的數字文本
     *
     * @return string
     */
    private function __numNoKey($k = null)
    {
        $num = $this->info[$k] ?? 0;
        $str = $this->getNumNoKey($num);
        return $str;
    }
    
    
    /**
     * 獲取數字月
     *
     * @return string
     */
    public function __MonthNum($k = null)
    {
        $M = $this->info['M'] ?? 0;
        $L = $this->info['L'] ?? 0;
        $leap = 1 == $L ? ($this->_KEY()['LEAP'] ?? '') : '';
        $str = $leap . ($this->_M()[$M] ?? '');
        return $str;
    }
    
    /**
     * 獲取數字日
     *
     * @return string
     */
    public function __DayNum($k = null)
    {
        $D = $this->info['D'] ?? 0;
        $str = $this->_D()[$D] ?? '';
        return $str;
    }
    
    /**
     * 獲取時辰地支
     *
     * @return string
     */
    public function __DHDZ($k = null)
    {
        $h = $this->info['h'] ?? 0;
        $str = $this->_DZ()[$h] ?? '';
        return $str;
    }
    
}