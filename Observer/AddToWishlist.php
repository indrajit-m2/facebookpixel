<?php


namespace Indrajit\FacebookPixel\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Indrajit\FacebookPixel\Helper\Data as IndrajitHelper;
use Indrajit\FacebookPixel\Model\SessionFactory as IndrajitSessionFactory;
use Indrajit\FacebookPixel\Logger\Logger;

class AddToWishlist implements ObserverInterface
{
    protected $indrajitHelper;
    protected $indrajitSession;
    protected $logger;

    public function __construct(
        IndrajitHelper $indrajitHelper,
        IndrajitSessionFactory $indrajitSession,
        Logger $logger
    )
    {
        $this->indrajitHelper    = $indrajitHelper;
        $this->indrajitSession   = $indrajitSession;
        $this->logger           = $logger;
    }
    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $product = $observer->getItem()->getProduct();

        if (!$this->indrajitHelper->isAddToWishList() || !$product) {
            return true;
        }

        $data = [
            'content_type' => 'product',
            'content_ids' => $product->getSku(),
            'content_name' => $product->getName(),
            'currency' => $this->indrajitHelper->getBaseCurrencyCode()
        ];

        $this->logger->info('Data',$data);
        $this->indrajitSession->create()->setAddToWishlist($data);

        return true;
    }
}
