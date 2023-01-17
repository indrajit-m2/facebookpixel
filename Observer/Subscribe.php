<?php
namespace Indrajit\FacebookPixel\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Indrajit\FacebookPixel\Helper\Data as IndrajitHelper;
use Indrajit\FacebookPixel\Model\SessionFactory as IndrajitSessionFactory;


class Subscribe implements ObserverInterface
{

    protected $indrajitHelper;
    protected $indrajitSession;

    public function __construct(
        IndrajitHelper $indrajitHelper,
        IndrajitSessionFactory $indrajitSession
    )
    {
        $this->indrajitHelper = $indrajitHelper;
        $this->indrajitSession = $indrajitSession;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $email = $observer->getEvent()->getSubscriber()->getSubscriberEmail();
        $subscriberId =$observer->getEvent()->getSubscriber()->getSubscriberId();
        if (!$this->indrajitHelper->isSubscribe() || !$email) {
            return true;
        }
        $currency = $this->indrajitHelper->getCurrentCurrencyCode();
        $subscriberData = [
            'id'                => $subscriberId,
            'email'             => $email,
            'value'             => 0.00,
            'currentCurrency'   => $currency
        ];

        $this->indrajitSession->create()->setSubscribe($subscriberData);

        return true;
    }

}

