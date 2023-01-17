<?php


namespace Indrajit\FacebookPixel\Helper;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Json\EncoderInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Helper\Data as CatalogHelper;
use Magento\Tax\Model\Config as TaxConfig;

class Data extends AbstractHelper
{
    protected $context;
    protected $storeManager;
    protected $taxConfig;
    protected $jsonEncoder;
    protected $catalogHelper;
    protected $store = null;
    protected $storeId = null;
    protected $baseCurrencyCode = null;
    protected $currentCurrencyCode = null;
    protected $displayTax = null;

    const IS_ENABLED            = 'indrajit_facebook_pixel/general/active';
    const PIXEL_ID              = 'indrajit_facebook_pixel/general/pixel_id';
    const DISABLED_PAGES        = 'indrajit_facebook_pixel/general/disabled_pageview';
    const IS_ADD_TO_WISH_LIST   = 'indrajit_facebook_pixel/indrajit_facebook_pixel_event_tracking/add_to_wishlist';
    const IS_ADD_TO_CART        = 'indrajit_facebook_pixel/indrajit_facebook_pixel_event_tracking/add_to_cart';
    const IS_SUBSCRIBE          = 'indrajit_facebook_pixel/indrajit_facebook_pixel_event_tracking/subscribe';
    const IS_REGISTER           = 'indrajit_facebook_pixel/indrajit_facebook_pixel_event_tracking/registration';
    const IS_PURCHASE           = 'indrajit_facebook_pixel/indrajit_facebook_pixel_event_tracking/purchase';
    const IS_INITIATE_CHECKOUT  = 'indrajit_facebook_pixel/indrajit_facebook_pixel_event_tracking/initiate_checkout';
    const IS_CATEGORY_VIEW      = 'indrajit_facebook_pixel/indrajit_facebook_pixel_event_tracking/category_view';
    const IS_PRODUCT_VIEW       = 'indrajit_facebook_pixel/indrajit_facebook_pixel_event_tracking/product_view';
    const IS_SEARCH             = 'indrajit_facebook_pixel/indrajit_facebook_pixel_event_tracking/search';
    const IS_QUICK_SEARCH             = 'indrajit_facebook_pixel/indrajit_facebook_pixel_event_tracking/quick_search';

    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        EncoderInterface $jsonEncoder,
        CatalogHelper $catalogHelper,
        TaxConfig $taxConfig
    )
    {
        $this->context          = $context;
        $this->storeManager     = $storeManager;
        $this->jsonEncoder      = $jsonEncoder;
        $this->catalogHelper    = $catalogHelper;
        $this->taxConfig        = $taxConfig;
        parent::__construct($context);
    }

    public function serialize($data){
        $result = $this->jsonEncoder->encode($data);
        if($result == false){
            throw new \InvalidArgumentException('Unable to serialize data');
        }
        return $result;
    }

    public function getConfig($configPath){
        return $this->scopeConfig->getValue(
            $configPath,
            ScopeInterface::SCOPE_STORE
        );
    }
    public function isPixelEnabled(){
        return $this->getConfig(self::IS_ENABLED);
    }
    public function getPixelId($scope = null){
        $pixelId = $this->getConfig(self::PIXEL_ID);
        return $pixelId;
    }
    public function getDisabledPagesList(){
        $list = $this->getConfig(self::DISABLED_PAGES);
        if(!empty($list)){
            return explode(',',$list);
        }
        return [];
    }
    public function isProductView(){
        return $this->getConfig(self::IS_PRODUCT_VIEW);
    }
    public function isCategoryView(){
        return $this->getConfig(self::IS_CATEGORY_VIEW);
    }
    public function isInitiateCheckout(){
        return $this->getConfig(self::IS_INITIATE_CHECKOUT);
    }
    public function isPurchase(){
        return $this->getConfig(self::IS_PURCHASE);
    }
    public function isAddToWishList(){
        return $this->getConfig(self::IS_ADD_TO_WISH_LIST);
    }
    public function isAddToCart(){
        return $this->getConfig(self::IS_ADD_TO_CART);
    }
    public function isRegistration(){
        return $this->getConfig(self::IS_REGISTER);
    }
    public function isSubscribe(){
        return $this->getConfig(self::IS_SUBSCRIBE);
    }
    public function isSearch(){
        return $this->getConfig(self::IS_SEARCH);
    }
    public function isQuickSearch(){
        return $this->getConfig(self::IS_QUICK_SEARCH);
    }

    public function getStore(){
        if($this->store == null){
            $this->store = $this->storeManager->getStore();
        }
        return $this->store;
    }
    public function getStoreId(){
        if($this->storeId == null){
            $this->storeId  = $this->getStore()->getId();
        }
    }
    public function getHtml($data = false)
    {
        $json = null;
        if ($data) {
            $json =$this->serialize($data);
        }

        return $json;
    }
    public function getBaseCurrencyCode(){
        if($this->baseCurrencyCode == null){
            $this->baseCurrencyCode = $this->getStore()->getBaseCurrencyCode();
        }
        return $this->baseCurrencyCode;
    }
    public function getCurrentCurrencyCode(){
        if($this->currentCurrencyCode == null){
            $currency = $this->getStore()->getCurrentCurrencyCode();
            $this->currentCurrencyCode = strtoupper($currency);
        }
        return $this->currentCurrencyCode;
    }
    public function escapeSingleQuotes($str)
    {
        return str_replace("'", "\'", $str);
    }
    /*PRODCUT RELATED METHODS*/
    public function isTaxConfig()
    {
        return $this->taxConfig;
    }
    private function getDisplayTaxFlag()
    {
        if ($this->displayTax === null) {
            // 0 - excluding tax
            // 1 - including tax
            $flag = $this->isTaxConfig()->getPriceDisplayType($this->getStoreId());

            if ($flag == 1) {
                $this->displayTax = 0;
            } else {
                $this->displayTax = 1;
            }

            return $this->displayTax;
        }
    }
    public function getProductPrice($product){
        switch ($product->getTypeId()) {
            case 'bundle':
                $price =  $this->getBundleProductPrice($product);
                break;
            case 'configurable':
                $price = $this->getConfigurableProductPrice($product);
                break;
            case 'grouped':
                $price = $this->getGroupedProductPrice($product);
                break;
            default:
                $price = $this->getFinalPrice($product);
        }

        return $price;
    }
    private function getBundleProductPrice($product)
    {
        $includeTax = (bool) $this->getDisplayTaxFlag();

        return $this->getFinalPrice(
            $product,
            $product->getPriceModel()->getTotalPrices(
                $product,
                'min',
                $includeTax,
                1
            )
        );
    }
    private function getConfigurableProductPrice($product)
    {
        if ($product->getFinalPrice() === 0) {
            $simpleCollection = $product->getTypeInstance()
                ->getUsedProducts($product);

            foreach ($simpleCollection as $simpleProduct) {
                if ($simpleProduct->getPrice() > 0) {
                    return $this->getFinalPrice($simpleProduct);
                }
            }
        }

        return $this->getFinalPrice($product);
    }
    private function getGroupedProductPrice($product)
    {
        $assocProducts = $product->getTypeInstance(true)
            ->getAssociatedProductCollection($product)
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('tax_class_id')
            ->addAttributeToSelect('tax_percent');

        $minPrice = INF;
        foreach ($assocProducts as $assocProduct) {
            $minPrice = min($minPrice, $this->getFinalPrice($assocProduct));
        }

        return $minPrice;
    }
    private function getFinalPrice($product, $price = null)
    {
        $price = $this->resultPriceFinal($product, $price);

        $productType = $product->getTypeId();
        //  Apply tax if needed

        if ($productType != 'configurable' && $productType != 'bundle') {
            // If display tax flag is on and catalog tax flag is off
            if ($this->getDisplayTaxFlag() && !$this->getCatalogTaxFlag()) {
                $price = $this->catalogHelper->getTaxPrice(
                    $product,
                    $price,
                    true,
                    null,
                    null,
                    null,
                    $this->getStoreId(),
                    false,
                    false
                );
            }
        }

        // Case when catalog prices are with tax but display tax is set to exclude tax
        if ($productType != 'bundle') {
            // If display tax flag is off and catalog tax flag is on
            if (!$this->getDisplayTaxFlag() && $this->getDisplayTaxFlag()) {
                $price = $this->catalogHelper->getTaxPrice(
                    $product,
                    $price,
                    false,
                    null,
                    null,
                    null,
                    $this->getStoreId(),
                    true,
                    false
                );
            }
        }

        return $price;
    }
    private function resultPriceFinal($product, $price)
    {
        if ($price === null) {
            $price = $product->getFinalPrice();
        }

        if ($price === null) {
            $price = $product->getData('special_price');
        }
        $productType = $product->getTypeId();
        // 1. Convert to current currency if needed
        if (($this->getBaseCurrencyCode() !== $this->getCurrentCurrencyCode())
            && $productType != 'configurable'
        ) {
            // Convert to from base currency to current currency
            $price = $this->getStore()->getBaseCurrency()
                ->convert($price, $this->getCurrentCurrencyCode());
        }
        return $price;
    }
    public function getConvertedPrice($price){
        if($this->getBaseCurrencyCode() !== $this->getCurrentCurrencyCode()){
            $price = $this->getStore()->getBaseCurrency()
                ->convert($price, $this->getCurrentCurrencyCode());
        }
        return $price;
    }


}
