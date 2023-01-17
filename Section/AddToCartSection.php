<?php


namespace Indrajit\FacebookPixel\Section;


use Magento\Customer\CustomerData\SectionSourceInterface;
use Indrajit\FacebookPixel\Model\SessionFactory as IndrajitSessionFactory;
class AddToCartSection implements SectionSourceInterface
{

    protected $indrajitSession;
    public function __construct(IndrajitSessionFactory $indrajitSession)
    {
        $this->indrajitSession = $indrajitSession;
    }

    /**
     * @return array
     */
    public function getSectionData()
    {
        $cartData = [];
        if($this->indrajitSession->create()->hasAddToCart()){
            $cartData = $this->indrajitSession->create()->getAddToCart();
        }
        return $cartData;
    }
}
