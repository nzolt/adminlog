<?php

/**
 * Class Time2_Adminlog_Model_Resource_Logins_Collection
 */
class Time2_Adminlog_Model_Resource_Logins_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('time2_adminlog/logins');
    }
}