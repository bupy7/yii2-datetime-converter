<?php

namespace bupy7\date\translator;

class LocalDate extends Component
{
    public $saveTimeZone = 'UTC';
    public $saveDate = 'php:Y-m-d';
    public $saveTime = 'php:H:i:s';
    public $saveDateTime = 'php:U';
    
    public $displayTimeZone;
    
    
    public $displayDate;
    
    
    public $displayTime;    
}
