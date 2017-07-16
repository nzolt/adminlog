<?php
/**
 * Time2 Toggles Helper
 *
 * http://opensource.org/licenses/osl-3.0.php
 *
 *
 * @category   Time2
 * @package    Time2_Adminlog
 * @copyright  Copyright (c) 2013 Time2 Digital (http://time2.digital)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class Time2_Adminlog_Helper_Data
 */
class Time2_Adminlog_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Check if security is enabled
     * @return bool
     */
    public function checkModuleActive()
    {
        return Mage::getStoreConfig('adminlog/general/is_active',Mage::app()->getStore() ? true : false);
    }

    /**
     * Get time for account lock
     * @return int
     */
    public function getLockTime()
    {
        $lockTime = Mage::getStoreConfig('adminlog/general/lock_for',Mage::app()->getStore());
        return !empty($lockTime) ? (int)$lockTime : 0;

    }
}