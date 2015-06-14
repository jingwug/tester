<?php
/***************************************
*$File: app/controllers/CinemaFilm.php
*$Description:
*$Author: KongSeng
*$Time:  2015/4/18
****************************************/
class CinemaFilm extends ModelBase
{
    public function initialize() {
        $this->hasOne("film_id", "Film", "id");
        parent::initialize();
    }

}