<?php
/***************************************
*$File: app/controllers/FilmInfo.php
*$Description:
*$Author: KongSeng
*$Time:  2015/4/18
****************************************/
class FilmInfo extends ModelBase
{
    public function initialize() {
        $this->belongsTo('film_id','Film','id');
        parent::initialize();
    }

}