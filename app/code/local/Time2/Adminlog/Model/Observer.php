<?php

/**
 * Time2/Adminlog Admin login attempts Observer
 * Class Time2_Adminlog_Model_Observer
 */
class Time2_Adminlog_Model_Observer
{
    const LOGIN_SUCCESS = 1;
    const LOGIN_FAIL = 0;
    const ADMIN_USER_LOCKED = 243;

    /**
     * Check Admin user before login
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function logAdminUserAuthenticateBefore(Varien_Event_Observer $observer)
    {

        if( Mage::helper('time2_adminlog')->checkModuleActive() == false )
        {
            return $this;
        }

        // Reset Lock if expired or deny login
        $user = Mage::getModel('admin/user')->getCollection()->addFieldToFilter('username',$observer->getData('username'));
        $userModel = $user->getFirstItem();

        $accountLocked = time() - strtotime($userModel->getData('locked_until'));

        if($accountLocked <= 0) {
            $userModel->locked_until = 'NULL';
            $userModel->setId($userModel->user_id)->save();
        } elseif($userModel->getData('locked_until') == '0000-00-00 00:00:00') {
            return $this;
        } else {
            $this->denyLogin();
        }

        return $this;
    }

    /**
     * Log Admin login success
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function logAdminUserLoginSuccess(Varien_Event_Observer $observer)
    {

        if( Mage::helper('time2_adminlog')->checkModuleActive() == false )
        {
            return $this;
        }

        $this->saveLoginAttempt($this->getRequestData($observer), self::LOGIN_SUCCESS);

        return $this;
    }

    /**
     * Log Admin user login failue
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function logAdminUserLoginFailed(Varien_Event_Observer $observer)
    {

        if( Mage::helper('time2_adminlog')->checkModuleActive() == false )
        {
            return $this;
        }

        // Load Admin user data by name
        $user = Mage::getModel('admin/user')->getCollection()->addFieldToFilter('username',$observer->getData('user_name'));
        $userRequestData = $user->getData();

        // if user found log fail attempt
        if(!empty($userRequestData)) {

            $to = new DateTime();
            $from = new DateTime();

            $this->saveLoginAttempt($userRequestData[0], self::LOGIN_FAIL);
            // check failed logins
            $collection = Mage::getModel("time2_adminlog/logins")->getCollection();
            $collection->addFieldToFilter('status', array('eq' => self::LOGIN_FAIL));

            $fromDate = $from->sub(new DateInterval('PT1M'))->format(Varien_Date::DATETIME_PHP_FORMAT);
            $toDate = $to->format(Varien_Date::DATETIME_PHP_FORMAT);
            $collection->addFieldToFilter('logged_in', array('from'=>$fromDate, 'to'=>$toDate));
            $collection->load();
            //var_dump($collection->getSelectSql(true), $collection->count());die();
            if (true || $collection->count() >= 3) {
                // Update admin/user table
                $userModel = $user->getFirstItem();
                $userModel->locked_until = $from->add(new DateInterval('PT' . Mage::helper('time2_adminlog')->getLockTime() . 'S'))->format(Varien_Date::DATETIME_PHP_FORMAT);
                $userModel->setId($userModel->user_id)->save();

                $this->denyLogin();
            }

        }
        return $this;
    }

    protected function denyLogin()
    {
        // Set message
        Mage::getSingleton('core/session')->addError('Account Locked');
        // Deny login
        $adminSession = Mage::getSingleton('admin/session');
        $adminSession->unsetAll();
        $adminSession->getCookie()->delete($adminSession->getSessionName());

        return $this;
    }

    /**
     * Get User from Obrserver
     * @param Varien_Event_Observer $observer
     * @return Mage_Admin_Model_User
     */
    protected function getRequestData(Varien_Event_Observer $observer)
    {
        return $observer->getEvent()->getData('user');
    }

    protected function saveLoginAttempt($user, $status)
    {
        $model = Mage::getModel("time2_adminlog/logins");
        $model->setLoginData($user, $status);
        if($model->hasDataChanges()) {
            $model->save();
        }

        return $this;
    }

}
