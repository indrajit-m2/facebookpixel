<?php


namespace Indrajit\FacebookPixel\Block;

use Magento\Framework\View\Element\Template;
use phpDocumentor\Reflection\Types\Boolean;
use Indrajit\FacebookPixel\Helper\Data as IndrajitHelper;
use Indrajit\FacebookPixel\Model\SessionFactory as IndrajitSession;
use Magento\Framework\Registry;
use Magento\Checkout\Model\SessionFactory;
use Indrajit\FacebookPixel\Logger\Logger;

class Pixel extends Template
{
    protected $indrajitHelper;
    protected $indrajitSession;
    protected $registry;
    protected $checkoutSession;
    protected $logger;

    public function __construct(Template\Context $context, array $data = [],
                                IndrajitHelper $indrajitHelper,
                                IndrajitSession $indrajitSession,
                                Registry $registry,
                                SessionFactory $checkoutSession,
                                Logger $logger)
    {
        $this->indrajitHelper    = $indrajitHelper;
        $this->indrajitSession   = $indrajitSession;
        $this->registry         = $registry;
        $this->checkoutSession  = $checkoutSession;
        $this->logger           = $logger;
        parent::__construct($context, $data);
    }

    /**
     * @var Indrajit\FacebookPixel\Model\Session $session
     **/
    public function getRegistration(){
        $session = $this->indrajitSession->create();
        $registration = 0;
        if ($this->indrajitHelper->isRegistration()
            && $session->hasRegister()) {
            $registration = $this->indrajitHelper->getHtml($session->getRegister());
        }
        return $registration;
    }
    public function getFacebookPixelData(){
        $fbData = array();
        $fbData['id'] = $this->indrajitHelper->getPixelId();
        $fbData['action'] = $this->getRequest()->getFullActionName();
        return $fbData;
    }
    public function isDisabledPage(){
        $listDisablePages = $this->indrajitHelper->getDisabledPagesList();
        $pixelData = $this->getFacebookPixelData();
        if(in_array($pixelData['action'], $listDisablePages)){
            return 'disabled';
        }
        return 'enabled';
    }
    /*CATALOG PRODUCT VIEW*/
    public function getProduct(){
        $product = 0;
        $action = $this->getRequest()->getFullActionName();
        if($action == 'catalog_product_view' &&
            $this->indrajitHelper->isProductView()){
            if ($this->getProductData() !== null) {
                $product = $this->indrajitHelper->serialize($this->getProductData());
            }
        }
        return $product;
    }
    protected function getProductData(){
        if (!$this->indrajitHelper->isProductView()) {
            return [];
        }
        $currentProduct = $this->registry->registry('current_product');

        $data = [];

        $data['content_name']     = $this->indrajitHelper
            ->escapeSingleQuotes($currentProduct->getName());
        $data['content_ids']      = $this->indrajitHelper
            ->escapeSingleQuotes($currentProduct->getSku());
        $data['content_type']     = 'product';
        $data['value']            = $this->formatPrice(
            $this->indrajitHelper->getProductPrice($currentProduct)
        );
        $data['currency']         = $this->indrajitHelper->getCurrentCurrencyCode();

        return $data;
    }
    public function formatPrice($price){
        return number_format($price, 2, '.', '');
    }
    /*CATEGORY*/
    public function getCurrentCategory(){
        $currentCategory = 0;
        $action = $this->getRequest()->getFullActionName();
        if($action == 'catalog_category_view' &&
            $this->indrajitHelper->isCategoryView()){
            $currentCategory = $this->indrajitHelper->serialize($this->getCurrentCategoryData());
        }
        return $currentCategory;
    }
    protected function getCurrentCategoryData(){
        if (!$this->indrajitHelper->isCategoryView()) {
            return [];
        }
        $currentCategory = $this->registry->registry('current_category');

        $data = [];

        $data['content_name']     = $this->indrajitHelper
            ->escapeSingleQuotes($currentCategory->getName());
        $data['content_ids']      = $this->indrajitHelper
            ->escapeSingleQuotes($currentCategory->getId());
        $data['content_type']     = 'category';
        $data['currency']         = $this->indrajitHelper->getCurrentCurrencyCode();

        return $data;
    }
    /*CATALOG SEARCH*/
    public function getSearchData(){
        $session = $this->indrajitSession->create();
        $searchData = 0;
        if ($this->indrajitHelper->isSearch()
            && $session->hasSearch()) {
            $searchData = $session->getSearch();
        }
        return $searchData;
    }
    /*WISHLIST*/
    public function getAddToWishListData(){
        $session = $this->indrajitSession->create();
        $add_to_wishlist = 0;
        if ($this->indrajitHelper->isAddToWishList()
            && $session->hasAddToWishlist()) {
            $add_to_wishlist = $this->indrajitHelper->getHtml($session->getAddToWishlist());
        }
        return $add_to_wishlist;
    }
    /*CHECKOU INITIATE DATA*/
    public function getInitiateCheckout(){
        $session = $this->indrajitSession->create();
        $initiateCheckout = 0;
        if ($this->indrajitHelper->isInitiateCheckout()
            && $session->hasInitiateCheckout()) {
            $initiateCheckout = $this->indrajitHelper->getHtml($session->getInitiateCheckout());
        }
        return $initiateCheckout;
    }
    /*PURCHASE/ORDER*/
    public function getPurchase(){
        $order = $this->checkoutSession->create()->getLastRealOrder();
        $orderId = $order->getIncrementId();

        if ($orderId && $this->indrajitHelper->isPurchase()) {
            $customerEmail = $order->getCustomerEmail();
            if ($order->getShippingAddress()) {
                $addressData = $order->getShippingAddress();
            } else {
                $addressData = $order->getBillingAddress();
            }

            if ($addressData) {
                $customerData = $addressData->getData();
            } else {
                $customerData = null;
            }
            $product = [
                'content_ids' => [],
                'contents' => [],
                'value' => "",
                'currency' => "",
                'num_items' => 0,
                'email' => "",
                'address' => []
            ];

            $num_item = 0;
            foreach ($order->getAllVisibleItems() as $item) {
                $product['contents'][] = [
                    'id' => $item->getSku(),
                    'name' => $item->getName(),
                    'quantity' => (int)$item->getQtyOrdered(),
                    'item_price' => $item->getPrice()
                ];
                $product['content_ids'][] = $item->getSku();
                $num_item += round($item->getQtyOrdered());
            }
            $data = [
                'content_ids' => $product['content_ids'],
                'contents' => $product['contents'],
                'content_type' => 'product',
                'value' => number_format(
                    $order->getGrandTotal(),
                    2,
                    '.',
                    ''
                ),
                'num_items' => $num_item,
                'currency' => $order->getOrderCurrencyCode(),
                'email' => $customerEmail,
                'phone' => $this->getValue($customerData, 'telephone'),
                'firtname' => $this->getValue($customerData, 'firstname'),
                'lastname' => $this->getValue($customerData, 'lastname'),
                'city' => $this->getValue($customerData, 'city'),
                'country' => $this->getValue($customerData, 'country_id'),
                'state' => $this->getValue($customerData, 'region'),
                'zipcode' => $this->getValue($customerData, 'postcode')
            ];
            return $this->indrajitHelper->serialize($data);
        } else {
            return 0;
        }
    }
    protected function getValue($array, $key)
    {
        if (!empty($array) && isset($array[$key])) {
            return $array[$key];
        }
        return '';
    }

    public function isQuickSearch(){
        return $this->indrajitHelper->isQuickSearch()?1:0;
    }

}
