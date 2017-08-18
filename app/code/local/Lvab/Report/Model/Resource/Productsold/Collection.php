<?php
class Lvab_Report_Model_Resource_Productsold_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract 
{
    protected function _construct()
    {
            $this->_init('lvab_report/productsold');
    }
	
	public function groupByCat()
	{
		$this->getSelect()
					->joinLeft(Mage::getConfig()->getTablePrefix().'catalog_category_product', 'main_table.product_id ='.Mage::getConfig()->getTablePrefix().'catalog_category_product.product_id')
					->join(Mage::getConfig()->getTablePrefix().'sales_flat_order', "main_table.order_id =".Mage::getConfig()->getTablePrefix()."`sales_flat_order`.entity_id AND `sales_flat_order`.state <> 'canceled'")
					->joinLeft(Mage::getConfig()->getTablePrefix().'catalog_category_entity_varchar', Mage::getConfig()->getTablePrefix()."catalog_category_product.category_id =".Mage::getConfig()->getTablePrefix()."catalog_category_entity_varchar.entity_id AND `catalog_category_entity_varchar`.attribute_id = 41")
					->join(Mage::getConfig()->getTablePrefix().'catalog_product_entity', "`catalog_product_entity`.type_id NOT IN ('grouped',  'configurable',  'bundle') AND `catalog_product_entity`.entity_id = main_table.product_id AND `catalog_product_entity`.entity_type_id =4 WHERE (parent_item_id IS NULL)")
					->reset(Zend_Db_Select::COLUMNS)
					->columns('SUM(main_table.qty_ordered) AS qty_ordered, `catalog_category_product`.category_id AS cat_id, catalog_category_entity_varchar.value as cat_name, SUM(main_table.price) AS price, SUM(main_table.price_incl_tax) AS price_with_tax, SUM(main_table.tax_amount) as tax')
					->group("catalog_category_product.category_id")
					->having("SUM(main_table.qty_ordered ) >0");
	}
	
	public function addDateFilter($from,$to)
	{
		$this->getSelect()->reset();
		parent::_initSelect();
		$this->getSelect()
				  ->joinLeft(Mage::getConfig()->getTablePrefix().'catalog_category_product', 'main_table.product_id ='.Mage::getConfig()->getTablePrefix().'catalog_category_product.product_id')
				  ->join(Mage::getConfig()->getTablePrefix().'sales_flat_order', "main_table.order_id =".Mage::getConfig()->getTablePrefix()."`sales_flat_order`.entity_id AND `sales_flat_order`.state <> 'canceled' AND (`sales_flat_order`.created_at BETWEEN '".$this->modifyDate($from,'from')."' AND '".$this->modifyDate($to,'to')."')")
				  ->joinLeft(Mage::getConfig()->getTablePrefix().'catalog_category_entity_varchar', Mage::getConfig()->getTablePrefix()."catalog_category_product.category_id =".Mage::getConfig()->getTablePrefix()."catalog_category_entity_varchar.entity_id AND `catalog_category_entity_varchar`.attribute_id = 41")
				  ->join(Mage::getConfig()->getTablePrefix().'catalog_product_entity', "`catalog_product_entity`.type_id NOT IN ('grouped',  'configurable',  'bundle') AND `catalog_product_entity`.entity_id = main_table.product_id AND `catalog_product_entity`.entity_type_id =4 WHERE (parent_item_id IS NULL)")
				  ->reset(Zend_Db_Select::COLUMNS)
				  ->columns('SUM(main_table.qty_ordered) AS qty_ordered, `catalog_category_product`.category_id AS cat_id, catalog_category_entity_varchar.value as cat_name, SUM(main_table.price) AS price, SUM(main_table.price_incl_tax) AS price_with_tax, SUM(main_table.tax_amount) as tax')
				  ->group("catalog_category_product.category_id")
				  ->having("SUM(main_table.qty_ordered ) >0");
	}
		//#####################IMPORTANT CHANGE THIS FUNCTION DEPENDING ON YOUR TIME ZONE###########################################
	protected function modifyDate($stringDate, $fromto)
	{
		$date = Mage::getModel('core/date')->date($stringDate);
		if(strtoupper($fromto) == "FROM")
		{
			$date = date('Y-m-d H:i:s', strtotime($date."+4 hours"));
		}
		elseif(strtoupper($fromto) == "TO")
		{
			$date = date('Y-m-d H:i:s', strtotime($date."+1 day +3 hours +59 minutes +59 seconds"));
		}

		return $date;
	}
}