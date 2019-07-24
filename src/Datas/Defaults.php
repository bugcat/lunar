<?php namespace Bugcat\Lunar\Datas;

//默認值
abstract class Defaults
{
    /**
     * 固定配置 Constant Config
     *
     * @const array
     */
    const CONST_CONFIG = [
        'default_format' => 'M月Dh時Q刻',
        //444495600爲本甲子年開始時的時間戳
        'min_timestamp'  => 444495600, 
        //2337692399爲本甲子年結束的時間戳
        //2147483647為三十二位系統最大整型數 (2038-01-19 11:14:07)
        'max_timestamp'  => 2147483647,
        'support_languages' => [
            'CHO' => '正體中文(Orthodox Chinese)', 
            'CHS' => '簡體中文(Simplified Chinese)',
        ],
        //字符串的編碼
        'encoding' => 'UTF-8',
        //支持的紀元
        'support_eras' => [
            'ZH' => '中蕐紀元', 
            //'XH' => '羲皇紀元',
        ],
        
    ];
    
    /**
     * 可自定義的配置 Customizable Config
     *
     * @const array
     */
    const CUSTZ_CONFIG = [
        'timezone' => 'Asia/Shanghai',
        'lang' => 'CHO',
        'era' => 'ZH',
    ];
    
}
