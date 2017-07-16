<?php
/**
 * Time2 Adminlog
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */
/* @var $this Mage_Core_Model_Resource_Setup */
$this->startSetup();

$this->getConnection()
    ->addColumn($this->getTable('admin/user'),
        'locked_until', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable'  => true,
    ), 'Locked until Time');

$table = $installer->getConnection()
    ->newTable($installer->getTable('time2_adminlog/logins'))
    ->addColumn('login_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Id')
    ->addColumn('admin_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ), 'Admin user ID')
    ->addColumn('logged_in', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
    ), 'Login attempt')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ), 'Login attempt status')
    ->addColumn('failures', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => true,
    ), 'Login failures')
    ->addIndex($installer->getIdxName('time2_adminlog/logins', array('admin_id')),
        array('admin_id'))
    ->addForeignKey($installer->getFkName('time2_adminlog/logins', 'admin_id', 'admin/user', 'user_id'),
        'admin_id', $installer->getTable('admin/user'), 'user_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE);

$installer->getConnection()->createTable($table);

$installer->endSetup();
