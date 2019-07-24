<?php namespace Bugcat\Lunar;

use Bugcat\Lunar\Traits\{GetDateStr, setConfig, GetInfo};

class LunarCalendar
{
    use GetDateStr;
    use setConfig;
    use GetInfo;
    
    /**
     * 文本數據的完整類名
     *
     * @const string
     */
    const TEXT_OBJ = 'Bugcat\Lunar\Datas\Text';
    
    /**
     * 配置
     *
     * @property array
     */
    protected $cfg = [];
    
    /**
     * 解析後的時間信息
     *
     * @property array
     */
    protected $info = [];
    
    
    /**
     * 以漢字獲取格式化後的時間
     *
     * @param  string  $format 輸出的時間格式
     * @param  mix     $time   輸入的時間 默認當前時間
     * @param  string  $type   輸入時間的格式 支持 int str stamp
     * @return string  
     */
    public function str(string $format = null, $time = null, string $type = null)
    {
        $this->initInfo($time, $type);
        $date = $this->dateStr($format, 'string');
        return $date;
    }
    
    /**
     * 以數字獲取格式化後的時間
     *
     * @param  string  $format 輸出的時間格式
     * @param  mix     $time   輸入的時間 默認當前時間
     * @param  string  $type   輸入時間的格式 支持 int str stamp
     * @return string  
     */
    public function num(string $format = null, $time = null, string $type = null)
    {
        $this->initInfo($time, $type);
        $date = $this->dateStr($format, 'number');
        return $date;
    }
    
    /**
     * 設置配置
     *
     * @param  array  $config
     * @return array
     */
    public function config(array $config = [])
    {
        $this->initConfig($config);
        return $this->cfg;
    }
    
    
    /**
     * 獲取相關文本
     *
     */
    public function __call($name, $arguments) 
    {
        $lang = strtoupper($this->cfg['lang']);
        $const = self::TEXT_OBJ . '::' . $lang;
        $text_arr = constant($const);
        $key = ltrim($name, '_');
        return $text_arr[$key] ?? null;
    }
    
}
