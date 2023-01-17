<?php


namespace Indrajit\FacebookPixel\Observer;


use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Setup\Exception;
use Magento\TestFramework\Event\Magento;
use Indrajit\FacebookPixel\Helper\Data as IndrajitHelper;
use Indrajit\FacebookPixel\Model\SessionFactory as IndrajitSessionFactory;
use Magento\Catalog\Model\ProductRepository;
use Indrajit\FacebookPixel\Logger\Logger;

class AddToCart implements ObserverInterface
{

    protected $indrajitHelper;
    protected $indrajitSession;
    protected $productRepository;
    protected $logger;
    public function __construct(
        IndrajitHelper $indrajitHelper,
        IndrajitSessionFactory $indrajitSession,
        ProductRepository $productRepository,
        Logger $logger
    )
    {
        $this->indrajitHelper        = $indrajitHelper;
        $this->indrajitSession       = $indrajitSession;
        $this->productRepository    = $productRepository;
        $this->logger               = $logger;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $items = $observer->getItems();
        $productData = [
            'content_ids' => [],
            'value' => 0,
            'currency' => ""
        ];

        if (!$this->indrajitHelper->isAddToCart() || !$items) {
            return true;
        }

        foreach ($items as $item) {
            try {
                if ($item->getProduct()->getTypeId() == 'configurable') {
                    continue;
                }
                if ($item->getParentItem()) {
                    if ($item->getParentItem()->getProductType() == 'configurable') {
                        $product = [
                            'id' => $item->getId(),
                            'name' => $item->getName(),
                            'quantity' => $item->getParentItem()->getQtyToAdd()
                        ];
                        $productData['contents'][] = $product;
                        $price = $this->indrajitHelper->getConvertedPrice($item->getProduct()->getFinalPrice());
                        $productData['value'] += $price * $item->getParentItem()->getQtyToAdd();
                    } else {
                        $product = [
                            'id' => $item->getSku(),
                            'name' => $item->getName(),
                            'quantity' => $item->getData('qty')
                        ];
                        $productData['contents'][] = $product;
                        $price = $this->indrajitHelper->getConvertedPrice($item->getProduct()->getFinalPrice());
                        $productData['value'] += $price * $item->getData('qty');
                    }
                } else {
                    $product = [
                        'id' => $this->getSkuBundle($item),
                        'name' => $item->getName(),
                        'quantity' => $item->getQtyToAdd()
                    ];
                    $productData['contents'][] = $product;
                    $price = $this->indrajitHelper->getConvertedPrice($item->getProduct()->getFinalPrice());
                    $productData['value'] += $price * $item->getQtyToAdd();
                }
                $productData['content_ids'][] = $this->getSkuBundle($item);
            }

            catch(\Exception $ex){
                $this->logger->info($ex->getMessage());
            }
        }

        $data = [
            'content_type' => 'product',
            'content_ids' => $productData['content_ids'],
            'contents' => $productData['contents'],
            'currency' => $this->indrajitHelper->getCurrentCurrencyCode(),
            'value' => $productData['value']
        ];
        $this->indrajitSession->create()->setAddToCart($data);
        return  true;
    }
    public function getSkuBundle($item){
        $sku = $item->getSku();
        if ($item->getProductType() == 'bundle') {
            $sku = $this->productRepository->getById($item->getProductId())->getSku();
        }
        return $sku;
    }
}

