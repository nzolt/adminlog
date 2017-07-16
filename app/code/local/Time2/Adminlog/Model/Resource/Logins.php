<?php

class Time2_Adminlog_Model_Resource_Logins extends Mage_Core_Model_Resource_Db_Abstract
{


    /**
     * Initialize main table and identifier field.
     * Set main entity table name and primary key field name.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('time2_adminlog/logins', 'login_id');
    }

    /**
     * Add events log to a collection
     * The collection id field is used without corellation, so it must be unique.
     * DESC ordering by event will be added to the collection
     *
     * @param Varien_Data_Collection_Db $collection
     * @param int $eventTypeId
     * @param int $eventSubjectId
     * @param int $subtype
     * @param array $skipIds
     * @return Mage_Reports_Model_Resource_Event
     */
    public function applyLogToCollection(Varien_Data_Collection_Db $collection, $eventTypeId, $eventSubjectId, $subtype,
                                         $skipIds = array())
    {
        $idFieldName = $collection->getResource()->getIdFieldName();

        $derivedSelect = $this->getReadConnection()->select()
            ->from(
                $this->getTable('reports/event'),
                array('event_id' => new Zend_Db_Expr('MAX(event_id)'), 'object_id'))
            ->where('event_type_id = ?', (int)$eventTypeId)
            ->where('subject_id = ?', (int)$eventSubjectId)
            ->where('subtype = ?', (int)$subtype)
            ->where('store_id IN(?)', $this->getCurrentStoreIds())
            ->group('object_id');

        if ($skipIds) {
            if (!is_array($skipIds)) {
                $skipIds = array((int)$skipIds);
            }
            $derivedSelect->where('object_id NOT IN(?)', $skipIds);
        }

        $collection->getSelect()
            ->joinInner(
                array('evt' => new Zend_Db_Expr("({$derivedSelect})")),
                "{$idFieldName} = evt.object_id",
                array())
            ->order('evt.event_id ' . Varien_Db_Select::SQL_DESC);

        return $this;
    }

    /**
     * Obtain all current store ids, depending on configuration
     *
     * @param array $predefinedStoreIds
     * @return array
     */
    public function getCurrentStoreIds(array $predefinedStoreIds = null)
    {
        $stores = array();
        // get all or specified stores
        if (Mage::app()->getStore()->getId() == 0) {
            if (null !== $predefinedStoreIds) {
                $stores = $predefinedStoreIds;
            } else {
                foreach (Mage::app()->getStores() as $store) {
                    $stores[] = $store->getId();
                }
            }
        } else { // get all stores, required by configuration in current store scope
            switch (Mage::getStoreConfig('catalog/recently_products/scope')) {
                case 'website':
                    $resourceStore = Mage::app()->getStore()->getWebsite()->getStores();
                    break;
                case 'group':
                    $resourceStore = Mage::app()->getStore()->getGroup()->getStores();
                    break;
                default:
                    $resourceStore = array(Mage::app()->getStore());
                    break;
            }

            foreach ($resourceStore as $store) {
                $stores[] = $store->getId();
            }
        }
        foreach ($stores as $key => $store) {
            $stores[$key] = (int)$store;
        }

        return $stores;
    }

    /**
     * Clean report event table
     *
     * @param Mage_Reports_Model_Event $object
     * @return Mage_Reports_Model_Resource_Event
     */
    public function clean(Mage_Reports_Model_Event $object)
    {
        while (true) {
            $select = $this->_getReadAdapter()->select()
                ->from(array('event_table' => $this->getMainTable()), array('event_id'))
                ->joinLeft(
                    array('visitor_table' => $this->getTable('log/visitor')),
                    'event_table.subject_id = visitor_table.visitor_id',
                    array())
                ->where('visitor_table.visitor_id IS NULL')
                ->where('event_table.subtype = ?', 1)
                ->limit(1000);
            $eventIds = $this->_getReadAdapter()->fetchCol($select);

            if (!$eventIds) {
                break;
            }

            $this->_getWriteAdapter()->delete($this->getMainTable(), array('event_id IN(?)' => $eventIds));
        }
        return $this;
    }

}
