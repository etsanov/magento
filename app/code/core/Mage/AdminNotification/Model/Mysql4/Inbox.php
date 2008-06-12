<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_AdminNotification
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * AdminNotification Inbox model
 *
 * @category   Mage
 * @package    Mage_AdminNotification
 * @author     Victor Tihonchuk <victor@varien.com>
 */
class Mage_AdminNotification_Model_Mysql4_Inbox extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('adminnotification/inbox', 'notification_id');
    }

    public function loadLatestNotice(Mage_AdminNotification_Model_Inbox $object)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable())
            ->order($this->getIdFieldName() . ' desc')
            ->limit(1);
        $data = $this->_getReadAdapter()->fetchRow($select);

        if ($data) {
            $object->setData($data);
        }

        $this->_afterLoad($object);

        return $this;
    }

    public function getNoticeStatus(Mage_AdminNotification_Model_Inbox $object)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), array(
                'severity'     => 'severity',
                'count_notice' => 'COUNT(' . $this->getIdFieldName() . ')'))
            ->group('severity')
            ->where('is_remove=?', 0)
            ->where('is_read=?', 0);
        $return = array();
        $rowSet = $this->_getReadAdapter()->fetchAll($select);
        foreach ($rowSet as $row) {
            $return[$row['severity']] = $row['count_notice'];
        }
        return $return;
    }

    public function parse(Mage_AdminNotification_Model_Inbox $object, array $data)
    {
        $write = $this->_getWriteAdapter();
        foreach ($data as $item) {
            $select = $write->select()
                ->from($this->getMainTable())
                ->where('url=?', $item['url']);
            $row = $write->fetchRow($select);

            if (!$row) {
                $write->insert($this->getMainTable(), $item);
            }
        }
    }
}