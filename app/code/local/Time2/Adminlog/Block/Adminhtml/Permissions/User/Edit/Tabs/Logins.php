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
 * @package     Mage_Api2
 * @copyright Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license http://www.magento.com/license/enterprise-edition
 */

/**
 * API2 role list for admin user permissions
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Time2_Adminlog_Block_Adminhtml_Permissions_User_Edit_Tabs_Logins
    extends Mage_Adminhtml_Block_Widget_Grid
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    /**
     * Constructor
     * Prepare grid parameters
     */
    public function __construct()
    {
        parent::__construct();

        $this->setId('time2_adminlog_section')
            ->setDefaultSort('sort_order')
            ->setDefaultDir(Varien_Db_Select::SQL_ASC)
            ->setTitle($this->__('Logins'))
            ->setUseAjax(true);
    }

    /**
     * Prepare grid collection object
     *
     * @return Time2_Adminlog_Block_Adminhtml_Permissions_User_Edit_Tabs_LoginAttempts
     */
    protected function _prepareCollection()
    {
        /** @var $collection Time2_Adminlog_Model_Resource_Logins_Collection */
        $collection = Mage::getModel("time2_adminlog/logins")->getCollection();
        $collection->addFieldToFilter('admin_id', array('eq' => Mage::registry('permissions_user')->getUserId()));
        $collection->getSelect()->limit(30);

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     *
     * @return Time2_Adminlog_Block_Adminhtml_Permissions_User_Edit_Tabs_Logins
     */
    protected function _prepareColumns()
    {
        $this->addColumn('logged_in', array(
            'header'    => $this->__('Login date'),
            'index'     => 'logged_in'
        ));

        $this->addColumn('status', array(
            'header'    => $this->__('Status'),
            'index'     => 'status'
        ));

        return parent::_prepareColumns();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('Logins');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('REST Role');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Get controller action url for grid ajax actions
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
}
