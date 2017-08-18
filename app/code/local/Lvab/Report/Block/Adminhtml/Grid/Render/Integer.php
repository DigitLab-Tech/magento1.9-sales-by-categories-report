<?php
class Lvab_Report_Block_Adminhtml_Grid_Render_Integer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
		if($row != null)
		{
			if($row->getData("cat_id") != Null)
			{
				$ordered = $row->getData("qty_ordered");
				$tmp = explode('.',$ordered);
				return $tmp[0];
			}
		}
    }
}