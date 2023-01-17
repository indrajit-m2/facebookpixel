<?php


namespace Indrajit\FacebookPixel\Observer;


use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Indrajit\FacebookPixel\Helper\Data as IndrajitHelper;
use Indrajit\FacebookPixel\Logger\Logger;
use Indrajit\FacebookPixel\Model\SessionFactory as IndrajitSessionFactory;
use Magento\Search\Helper\Data as SearchHelper;
use Magento\Framework\App\RequestInterface;

class CatalogSearch implements ObserverInterface
{

    protected $indrajitHelper;
    protected $indrajitSession;
    protected $searchHelper;
    protected $request;
    protected $logger;

    public function __construct(
        IndrajitHelper $indrajitHelper,
        IndrajitSessionFactory $indrajitSession,
        SearchHelper $searchHelper,
        RequestInterface $request,
        Logger $logger
    )
    {
        $this->indrajitHelper    = $indrajitHelper;
        $this->indrajitSession   = $indrajitSession;
        $this->searchHelper     = $searchHelper;
        $this->request          = $request;
        $this->logger           = $logger;
    }

    public function execute(Observer $observer)
    {
        $text = $this->searchHelper->getEscapedQueryText();
        if (!$text) {
            $text = $this->request->getParams();
            foreach ($this->request->getParams() as $key => $value) {
                $text[$key] = $value;
            }
        }
        if (!$this->indrajitHelper->isSearch() || !$text) {
            return true;
        }
        $this->indrajitSession->create()->setSearch($text);
        return true;
    }
}
