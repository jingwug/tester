<?php
/***************************************
*$File: app/controllers/FilmHeat.php
*$Description:
*$Author: KongSeng
*$Time:  2015/4/18
****************************************/
class FilmHeat extends ModelBase
{
    public function initialize() {
        $this->belongsTo("film_id", "Film", "id");
        parent::initialize();
    }

}