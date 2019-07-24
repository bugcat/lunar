<?php namespace Bugcat\Lunar\Traits;

use Bugcat\Lunar\Datas\{Defaults, Text};

trait setConfig
{
    /**
     * 初始化時間信息
     *
     * @param  mix     $time   輸入的時間 默認當前時間
     * @param  string  $type   輸入時間的格式 支持 int str stamp
     * @return bool  
     */
    private function initInfo($time = null, string $type = null)
    {
        $this->config();
        $timestamp = $this->getTimestamp($time, $type);
        $this->getDateInfo($timestamp);
        return true;
    }
    
    
    /**
     * 初始化配置
     *
     * @param  array  $config
     * @return array
     */
    private function initConfig(array $config = [])
    {
        if ( empty($config) && !empty($this->cfg) ) {
            return false;
        }
        //先定義恆定的配置
        $this->cfg = Defaults::CONST_CONFIG;
        //再定義可自定義的配置
        foreach ( Defaults::CUSTZ_CONFIG as $key => $default ) {
            $method = '__' . $key;
            $value = $config[$key] ?? $default;
            if ( method_exists($this, $method) ) {
                $this->$method($value);
            } else {
                $this->cfg[$key] = $value;
            }
        }
        return true;
    }
    
    /**
     * 配置時區
     *
     * @param  mix  $value
     * @return string
     */
    private function __timezone($value)
    {
        date_default_timezone_set($value);
        $this->cfg['timezone'] = $value;
    }
    
    /**
     * 配置語言
     *
     * @param  mix  $value
     * @return string
     */
    private function __lang($value)
    {
        $value = strtoupper($value);
        if ( isset($this->cfg['support_languages'][$value]) ) {
            $this->cfg['lang'] = $value;
        } else {
            $err = 'Please use one of the languages : [' . implode(', ', array_keys($this->cfg['support_languages'])).']';
            throw new \Exception($err);
        }
    }
    
}