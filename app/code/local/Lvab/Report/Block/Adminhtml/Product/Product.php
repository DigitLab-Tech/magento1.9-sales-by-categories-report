<?php
class Lvab_Report_Block_Adminhtml_Product_Product
    extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    protected $_blockGroup      = 'lvab_report';
    protected $_controller      = 'adminhtml_product_sold';
    public function __construct()
    {
        $this->_headerText = Mage::helper('lvab_report')->__('Vente par Catégorie');
        $this->setTemplate('report/grid/container.phtml');
		
		    $this->addButton('filter_form_submit', array(
            'label'     => Mage::helper('lvab_report')->__('Show Report'),
            'onclick'   => 'filterFormSubmit()'
        ));
        parent::__construct();
        $this->_removeButton('add');
    }
    public function getFilterUrl()
    {
        $this->getRequest()->setParam('filter', null);
        return $this->getUrl('*/*/index', array('_current' => true));
    }
}