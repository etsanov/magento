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
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Layer category filter
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Layer_Filter_Category extends Mage_Catalog_Model_Layer_Filter_Abstract
{
    protected $_categoryId;
    protected $_appliedCategory = null;

    public function __construct()
    {
        parent::__construct();
        $this->_requestVar = 'cat';
    }

    /**
     * Get filter value for reset current filter state
     *
     * @return mixed
     */
    public function getResetValue()
    {
        if ($this->_appliedCategory) {
            /**
             * Revert path ids
             */
        	$pathIds = array_reverse($this->_appliedCategory->getPathIds());
        	$curCategoryId = $this->getLayer()->getCurrentCategory()->getId();
            if (isset($pathIds[1]) && $pathIds[1] != $curCategoryId) {
            	return $pathIds[1];
            }
        }
        return null;
    }

    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        $filter = (int) $request->getParam($this->getRequestVar());
        $this->_categoryId = $filter;
        $this->_appliedCategory = Mage::getModel('catalog/category')->load($filter);

        if ($this->_isValidCategory($this->_appliedCategory)) {
            $this->getLayer()->getProductCollection()
                ->addCategoryFilter($this->_appliedCategory, true);

            $this->getLayer()->getState()->addFilter(
                $this->_createItem($this->_appliedCategory->getName(), $filter)
            );
        }

        return $this;
    }

    protected function _isValidCategory($category)
    {
        return $category->getId();
    }

    public function getName()
    {
        return Mage::helper('catalog')->__('Category');
    }

    public function getCategory()
    {
        if ($this->_categoryId) {
            $category = Mage::getModel('catalog/category')->load($this->_categoryId);
            if ($category->getId()) {
                return $category;
            }
        }
        return Mage::getSingleton('catalog/layer')->getCurrentCategory();
    }

    protected function _initItems()
    {
        $categoty   = $this->getCategory();
        $collection = Mage::getResourceModel('catalog/category_collection')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('all_children')
            ->addAttributeToSelect('is_anchor')
            ->addAttributeToFilter('is_active', 1)
            ->addAttributeToSort('position', 'asc')
            ->joinUrlRewrite()
            ->addIdFilter($categoty->getChildren())
            ->load();

        Mage::getSingleton('catalog/layer')->getProductCollection()
            ->addCountToCategories($collection);

        $items=array();
        foreach ($collection as $category) {
            if ($category->getIsActive() && $category->getProductCount()) {
                $items[] = Mage::getModel('catalog/layer_filter_item')
                    ->setFilter($this)
                    ->setLabel($category->getName())
                    ->setValue($category->getId())
                    ->setCount($category->getProductCount());
            }
        }
        $this->_items = $items;
        return $this;
    }
}
