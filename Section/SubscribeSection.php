<?php


namespace Indrajit\FacebookPixel\Section;
use Magento\Customer\CustomerData\SectionSourceInterface;
use Indrajit\FacebookPixel\Model\SessionFactory as IndrajitSessionFactory;

class SubscribeSection implements SectionSourceInterface
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
        $subscribeData = [];
        if($this->indrajitSession->create()->hasSubscribe()){
            $subscribeData = $this->indrajitSession->create()->getSubscribe();
        }
        return $subscribeData;
    }
}
