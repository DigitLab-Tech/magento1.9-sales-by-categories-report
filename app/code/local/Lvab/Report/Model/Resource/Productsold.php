<?php
class Lvab_Report_Model_Resource_Productsold extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('lvab_report/productsold', 'item_id');
    }
}