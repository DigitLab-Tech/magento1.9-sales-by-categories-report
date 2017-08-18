<?php
class Lvab_Report_Block_Adminhtml_Grid_Render_Price extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
		$goodFormat = null;
		if($row->getData("cat_id") != Null)
		{
			$isCsv = $this->getColumn()->getGrid()->getIsCsv();
			$price = $row->getData($this->getColumn()->getIndex());
			if($isCsv == false)
			{
				$goodFormat = Mage::helper('core')->currency($price, true, false);
			}
			else
			{
				$goodFormat = number_format($price, 2, '.', '');
			}
		}
		return $goodFormat;
    }
}