<?php

class Order extends ModelBase
{
	public function initialize() {
		parent::initialize();
        $this->skipAttributesOnCreate(array('feature_app_no','film_code','cinema_code','screen_code','third_code','pay_unique','pay_time','ticket_time','end_time'));
        $this->skipAttributesOnUpdate(array('feature_app_no','film_code','cinema_code','screen_code','third_code','pay_unique'));
	}

}
