<?php

class Time2_Adminlog_Model_Logins extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('time2_adminlog/logins');
    }

    /**
     * @param Mage_Admin_Model_User $user
     * @param $status
     * @return $this
     */
    public function setLoginData(Mage_Admin_Model_User $user, $status)
    {
        if($user) {
            $when = new DateTime();
            $this->admin_id = $user['user_id'];
            $this->logged_in = $when->format(Varien_Date::DATETIME_PHP_FORMAT);
            $this->status = $status;
        }

        return $this;
    }
}