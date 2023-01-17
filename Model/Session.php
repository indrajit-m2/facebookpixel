<?php


namespace Indrajit\FacebookPixel\Model;


use Magento\Framework\Session\SessionManager;

class Session extends SessionManager
{
    const ACTION_PAGE = 'facebookpixel_action_page';
    const CUSTOMER_REGISTER = 'facebookpixel_customer_register';
    const SUBSCRIBE = 'facebookpixel_subscribe';
    const ADD_TO_WHISHLIST = 'facebookpixel_add_to_wishlist';
    const ADD_TO_CART = 'facebookpixel_add_to_cart';
    const CATALOG_SEARCH = 'facebookpixel_catalog_search';
    const INITIATE_CHECKOUT = 'facebookpixel_initiate_checkout';

    /**
     * store current action page in session
     * @return \Indrajit\FacebookPixel\Model\Session $this
     **/
    public function setActionPage($action){
        $this->setData(self::ACTION_PAGE, $action);
        return $this;
    }
    /**
     * get action from the session
     * @return mixed|null
     **/
    public function getActionPage(){
        if($this->hasActionStored()){
            $data = $this->getData(self::ACTION_PAGE);
            $this->unsetData(self::ACTION_PAGE);
            return $data;
        }
        return null;
    }

    /**
     * @return boolean
     **/
    public function hasActionPage(){
        return $this->hasData(self::ACTION_PAGE);
    }


    /**
     * set customer registration data
     * @return \Indrajit\FacebookPixel\Model\Session $this
     **/
    public function setRegister($customer_register){
        $this->setData(self::CUSTOMER_REGISTER, $customer_register);
        return $this;
    }

    /**
     * Customer registration data
     * @return mixed|null
     **/
    public function getRegister(){
        if($this->hasRegister()) {
            $data = $this->getData(self::CUSTOMER_REGISTER);
            $this->unsetData(self::CUSTOMER_REGISTER);
            return $data;
        }
        return null;
    }

    /**
     * @return boolean
     **/
    public function hasRegister(){
        return $this->hasData(self::CUSTOMER_REGISTER);
    }

    /**
     * set data after subscribe
     * @return Indrajit\FacebookPixel\Model\Session $this
     **/
    public function setSubscribe($subscribeData){
        $this->setData(self::SUBSCRIBE,$subscribeData);
        return $this;
    }

    /**
     * get data of subscriber
     * @return mixed|null
     **/
    public function getSubscribe(){
        if($this->hasSubscribe()){
            $data = $this->getData(self::SUBSCRIBE);
            $this->unsetData(self::SUBSCRIBE);
            return $data;
        }
        return  null;
    }
    /**
     * @return boolean
     **/
    public function hasSubscribe(){
        return $this->hasData(self::SUBSCRIBE);
    }

    /**
     * set data after user search
     * @return Indrajit\FacebookPixel\Model\Session $this
     **/
    public function setSearch($searchData){
        $this->setData(self::CATALOG_SEARCH,$searchData);
        return $this;
    }

    /**
     * get Search data from session
     * @return mixed|null
    **/
    public function getSearch(){
        if($this->hasSearch()){
            $data = $this->getData(self::CATALOG_SEARCH);
            $this->unsetData(self::CATALOG_SEARCH);
            return $data;
        }
        return null;
    }
    /**
     * @return boolean
    **/
    public function hasSearch(){
        return $this->hasData(self::CATALOG_SEARCH);
    }

    /**
     * set data after product is added to wishlist
     * @return Indrajit\FacebookPixel\Model\Session $this
     **/
    public function setAddToWishList($wishlistData){
        $this->setData(self::ADD_TO_WHISHLIST,$wishlistData);
        return $this;
    }

    /**
     * get wishlist data from session
     * @return mixed|null
     **/
    public function getAddToWishlist(){
        if($this->hasAddToWishList()){
            $data = $this->getData(self::ADD_TO_WHISHLIST);
            $this->unsetData(self::ADD_TO_WHISHLIST);
            return $data;
        }
        return  null;
    }

    /**
     * @return boolean
    **/
    public function hasAddToWishList(){
        return $this->hasData(self::ADD_TO_WHISHLIST);
    }

    /**
     * set data after product is added to the cart
     * @return Indrajit\FacebookPixel\Model\Session $this
     **/
    public function setAddToCart($cartData){
        $this->setData(self::ADD_TO_CART,$cartData);
        return $this;
    }

    /**
     * get cart data from session
     * @return mixed|null
     **/
    public function getAddToCart(){
        if($this->hasAddToCart()){
            $data = $this->getData(self::ADD_TO_CART);
            $this->unsetData(self::ADD_TO_CART);
            return $data;
        }
        return null;
    }
    /**
     * @return boolean
    **/
    public function hasAddToCart(){
        return $this->hasData(self::ADD_TO_CART);
    }

    public function setInitiateCheckout($checkoutData){
        $this->setData(self::INITIATE_CHECKOUT,$checkoutData);
        return $this;
    }
    public function getInitiateCheckout(){
        if($this->hasInitiateCheckout()){
            $data = $this->getData(self::INITIATE_CHECKOUT);
            $this->unsetData(self::INITIATE_CHECKOUT);
            return $data;
        }
        return null;
    }
    public function hasInitiateCheckout(){
        return $this->hasData(self::INITIATE_CHECKOUT);
    }


}
