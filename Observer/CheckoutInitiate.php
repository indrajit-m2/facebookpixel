<?php


namespace Indrajit\FacebookPixel\Observer;


use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Indrajit\FacebookPixel\Helper\Data as IndrajitHelper;
use Indrajit\FacebookPixel\Model\SessionFactory as IndrajitSessionFactory;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Indrajit\FacebookPixel\Logger\Logger;
use Magento\Checkout\Model\SessionFactory as CheckoutSession;

class CheckoutInitiate implements ObserverInterface
{
    protected $indrajitHelper;
    protected $indrajitSession;
    protected $priceHelper;
    protected $logger;
    protected $checkoutSession;

    public function __construct(
        IndrajitHelper $indrajitHelper,
        IndrajitSessionFactory $indrajitSession,
        PriceHelper $priceHelper,
        CheckoutSession $checkoutSession,
        Logger $logger
    )
    {
        $this->indrajitHelper    = $indrajitHelper;
        $this->indrajitSession   = $indrajitSession;
        $this->priceHelper      = $priceHelper;
        $this->checkoutSession  = $checkoutSession;
        $this->logger           = $logger;
    }

    public function execute(Observer $observer)
    {
        $this->logger->info('In Checkout Initiate');
        if (!$this->indrajitHelper->isInitiateCheckout()) {
            return true;
        }
        $this->logger->info('In Checkout Initiate - 2');
        $checkout = $this->checkoutSession->create();

        if (empty($checkout->getQuote())) {
            return true;
        }
        $this->logger->info('After get Quote');

        $products = [
            'content_ids' => [],
            'contents' => [],
            'value' => "",
            'currency' => ""
        ];

        $items = $checkout->getQuote()->getAllVisibleItems();
        foreach ($items as $item) {
            $products['contents'][] = [
                'id' => $item->getSku(),
                'name' => $item->getName(),
                'quantity' => $item->getQty(),
                'item_price' => $this->priceHelper->currency($item->getPrice(), false, false)
            ];
            $products['content_ids'][] = $item->getSku();
        }
        $data = [
            'content_ids' => $products['content_ids'],
            'contents' => $products['contents'],
            'content_type' => 'product',
            'value' => $checkout->getQuote()->getGrandTotal(),
            'currency' => $this->indrajitHelper->getCurrentCurrencyCode(),
        ];
        $this->logger->info('Data Checkout',$data);
        $this->indrajitSession->create()->setInitiateCheckout($data);

        return true;
    }
}
