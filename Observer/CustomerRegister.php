<?php


namespace Indrajit\FacebookPixel\Observer;


use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Indrajit\FacebookPixel\Helper\Data as IndrajitHelper;
use Indrajit\FacebookPixel\Model\SessionFactory as IndrajitSessionFactory;

class CustomerRegister implements ObserverInterface
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

    public function execute(Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        if(!$this->indrajitHelper->isRegistration() || !$customer){
            return true;
        }

        $customerData = [
            'customer_id' => $customer->getId(),
            'email' => $customer->getEmail(),
            'first_name' => $customer->getFirstName(),
            'last_name' => $customer->getLastName()
        ];

        $this->indrajitSession->create()->setRegister($customerData);

        return true;
    }
}
