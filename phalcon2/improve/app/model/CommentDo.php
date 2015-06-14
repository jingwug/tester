<?php
/***************************************
*$File: app/controllers/CommentDo.php
*$Description:
*$Author: KongSeng
*$Time:  2015/4/18
****************************************/
class CommentDo extends ModelBase
{
    public function initialize() {
        //Skips fields/columns on both INSERT/UPDATE operations
        parent::initialize();
        $this->skipAttributes(array('year', 'price'));
    }
//    public function initialize()
//    {
//
//        //Skips only when inserting
//        $this->skipAttributesOnCreate(array('created_at'));
//
//        //Skips only when updating
//        $this->skipAttributesOnUpdate(array('modified_in'));
//    }
}