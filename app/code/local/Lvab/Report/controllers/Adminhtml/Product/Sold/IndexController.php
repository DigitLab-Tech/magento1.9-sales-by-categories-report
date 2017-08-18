<?php
class Lvab_Report_Adminhtml_Product_Sold_IndexController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->_title($this->__('Rapport'))
            ->_title($this->__('Ventes'))
            ->_title($this->__('Vente par Catégories'));
        $this->loadLayout()
            ->_setActiveMenu('report/sales');
        return $this;
    }
    protected function _initReportAction($blocks)
    {
        if (!is_array($blocks)) {
            $blocks = array($blocks);
        }
 
        $requestData = Mage::helper('adminhtml')->prepareFilterString($this->getRequest()->getParam('filter'));
        $requestData = $this->_filterDates($requestData, array('from', 'to'));
        $params = $this->_getDefaultFilterData();
        foreach ($requestData as $key => $value) {
            if (!empty($value)) {
                $params->setData($key, $value);
            }
        }
 
        foreach ($blocks as $block) {
            if ($block) {
                $block->setFilterData($params);
            }
        }
        return $this;
    }
    public function indexAction()
    {
        $this->_initAction();

        $gridBlock = $this->getLayout()->getBlock('adminhtml_product_sold.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');
		$gridBlock->setIsCsv(false);
        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));

        $this->renderLayout();
    }
    public function exportCsvAction()
    {
        $fileName = 'lvab_report.csv';
        $grid = $this->getLayout()->createBlock('lvab_report/adminhtml_product_sold_grid');
		$grid->setIsCsv(true);
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }
	
    protected function _getDefaultFilterData()
    {
        return new Varien_Object(array(
            'from' => date('Y-m-d G:i:s', strtotime('-1 month -1 day')),
            'to' => date('Y-m-d G:i:s', strtotime('-1 day')),
			'catId' => 55
        ));
    }
	protected function _isAllowed()
	{
		return true;
	}
}