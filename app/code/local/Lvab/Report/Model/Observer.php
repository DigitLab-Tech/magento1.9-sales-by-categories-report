<?php
class Lvab_Report_Model_Observer
{
    public function applyLimitToGrid(Varien_Event_Observer $observer)
    {
    	$block = $observer->getEvent()->getBlock();
    	if($block instanceof Lvab_Report_Block_Adminhtml_Product_Sold_Grid)
		{
			$block->setDefaultLimit(200);
		}
    }
}