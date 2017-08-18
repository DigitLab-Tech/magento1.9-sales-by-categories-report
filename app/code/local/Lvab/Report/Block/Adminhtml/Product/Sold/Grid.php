<?php
class Lvab_Report_Block_Adminhtml_Product_Sold_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $_resourceCollectionName = 'lvab_report/productsold';
	protected $_isCsv;

    public function __construct()
    {
        parent::__construct();

        $this->setPagerVisibility(false);
        $this->setUseAjax(false);
        $this->setFilterVisibility(false);
        $this->setEmptyCellLabel(Mage::helper('lvab_report')->__('No records found.'));
        $this->setId('vignobleReportsGrid');
        $this->setCountTotals(false);
    }

    public function getResourceCollectionName()
    {
        return $this->_resourceCollectionName;
    }

    public function getResourceCollection()
    {
		$collection = Mage::getModel($this->getResourceCollectionName())->getCollection();
		$collection->groupByCat();
        return $collection;
    }

    public function getCurrentCurrencyCode()
    {
        return Mage::app()->getStore()->getBaseCurrencyCode();
    }
	
    public function getRate($toCurrency)
    {
        return Mage::app()->getStore()->getBaseCurrency()->getRate($toCurrency);
    }
	
    protected function _prepareColumns()
    {
        $currencyCode           = $this->getCurrentCurrencyCode();
        $rate                   = $this->getRate($currencyCode);
		
		$this->addColumn('cat_name', array(
            'header' => Mage::helper('lvab_report')->__('Catégories'),
            'index' => 'cat_name',
            'width' => 100,
			'type'=>'text',
			'sortable' => true
        ));
        $this->addColumn('qty_ordered', array(
            'header' => Mage::helper('lvab_report')->__('Qty Commandé'),
            'index' => 'qty_ordered',
            'width' => 100,
			'align'     =>'left',
			'renderer' => 'Lvab_Report_Block_Adminhtml_Grid_Render_Integer',
            'sortable' => true
        ));
		$this->addColumn('price', array(
            'header' => Mage::helper('lvab_report')->__('Total Gain'),
            'index' => 'price',
            'width' => 100,
			'type'=>'price',
            'sortable' => true,
			'renderer' => 'Lvab_Report_Block_Adminhtml_Grid_Render_Price'
        ));
		$this->addColumn('tax', array(
            'header' => Mage::helper('lvab_report')->__('Total taxes'),
            'index' => 'tax',
            'width' => 100,
			'type'=>'price',
            'sortable' => true,
			'renderer' => 'Lvab_Report_Block_Adminhtml_Grid_Render_Price'
        ));
		$this->addColumn('price_with_tax', array(
            'header' => Mage::helper('lvab_report')->__('Gain + taxes'),
            'index' => 'price_with_tax',
            'width' => 100,
			'type'=>'price',
            'sortable' => true,
			'renderer' => 'Lvab_Report_Block_Adminhtml_Grid_Render_Price'
        ));
        $this->addExportType('*/*/exportCsv', Mage::helper('lvab_report')->__('CSV'));

        return parent::_prepareColumns();
    }

    protected function _prepareCollection()
    {
        $filterData             = $this->getFilterData();
        $resourceCollection     = $this->getResourceCollection();

        $this->_addCustomFilter(
            $resourceCollection,
            $filterData
        );

        $this->setCollection($resourceCollection);

        if ($this->_isExport) {
            return $this;
        }

		return parent::_prepareCollection();
    }
	
    protected function _addCustomFilter($collection, $filterData)
    {
		$collection->addDateFilter($filterData->getData("from"),$filterData->getData("to"));
        return $this;
    }
	
	public function getCsvFile()
    {
		$filterData = $this->getFilterData();
		$from = new DateTime($filterData->getData("from"));
		$to = new DateTime($filterData->getData("to"));
		
        $this->_isExport = true;
        $this->_prepareGrid();

        $io = new Varien_Io_File();

        $path = Mage::getBaseDir('var') . DS . 'export' . DS;
        $name = md5(microtime());
        $file = $path . DS . $name . '.csv';

        $io->setAllowCreateFolders(true);
        $io->open(array('path' => $path));
		ob_clean();
        $io->streamOpen($file, 'w+');		//#######################CHANGE NEXT LINE DEPENDING ON YOUR EXCEL SEPERATOR######################################
		$io->streamWrite("sep=, \r\n");
		$io->streamWrite("Du: ".$from->format('Y-m-d')." | AU: ".$to->format('Y-m-d')." \r\n");
        $io->streamLock(true);
        $io->streamWriteCsv($this->_getExportHeaders());

        $this->_exportIterateCollection('_exportCsvItem', array($io));

        if ($this->getCountTotals()) {
            $io->streamWriteCsv(
                Mage::helper("core")->getEscapedCSVData($this->_getExportTotals())
            );
        }

        $io->streamUnlock();
        $io->streamClose();

        return array(
            'type'  => 'filename',
            'value' => $file,
            'rm'    => true
        );
    }
	
	public function getIsCsv()
	{
		return $this->_isCsv;
	}
	
	public function setIsCsv($bool)
	{
		$this->_isCsv = $bool;
	}
}