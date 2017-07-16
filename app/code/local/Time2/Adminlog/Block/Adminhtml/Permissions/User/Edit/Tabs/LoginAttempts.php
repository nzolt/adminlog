<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition End User License Agreement
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magento.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license http://www.magento.com/license/enterprise-edition
 */

/**
 * Admin page left menu
 *
 * @package    Time2_Adminlog
 */
class Time2_Adminlog_Block_Adminhtml_Permissions_User_Edit_Tabs_LoginAttempts extends Mage_Adminhtml_Block_Permissions_User_Edit_Tabs
{

    protected function _beforeToHtml()
    {
        $this->addTabAfter('adminlog_section', array(
            'label'     => Mage::helper('adminhtml')->__('Login attempts'),
            'title'     => Mage::helper('adminhtml')->__('Login attempts'),
            'content'   => $this->getLayout()->createBlock('time2_adminlog/adminhtml_permissions_user_edit_tabs_logins', 'time2.adminlog.form')->toHtml(),
        ), 'roles_section');
        return parent::_beforeToHtml();
    }

}
