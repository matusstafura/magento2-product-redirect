<?php

namespace MatusStafura\ProductRedirect\Controller\Index;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class Index implements HttpGetActionInterface
{
    public function __construct(
        protected RequestInterface $request,
        protected RedirectFactory $redirectFactory,
        protected ManagerInterface $messageManager,
        protected ProductRepositoryInterface $productRepository,
        protected StoreManagerInterface $storeManager,
        protected LoggerInterface $logger
    ) {
    }

    /**
     * Redirect by SKU or ID to localized product URL
     * 
     * Examples:
     * 1. /product?sku=ABC123
     * 2. /product?id=12301
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $redirect = $this->redirectFactory->create();

        try {
            $currentStoreId = $this->storeManager->getStore()->getId();
            $product = null;
            $identifier = null;
            $identifierType = null;

            // Priority 1: Check for SKU in query parameter
            $sku = $this->request->getParam('sku');
            if ($sku) {
                $identifier = $sku;
                $identifierType = 'SKU';
                $product = $this->loadProductBySku($sku, $currentStoreId);
            }

            // Priority 2: Check for ID in query parameter
            if (!$product) {
                $productId = $this->request->getParam('id');
                if ($productId && is_numeric($productId)) {
                    $identifier = $productId;
                    $identifierType = 'ID';
                    $product = $this->loadProductById($productId, $currentStoreId);
                }
            }

            // If no product found with any method
            if (!$product) {
                $this->messageManager->addErrorMessage(
                    __('Product not found. Please check the link and try again.')
                );
                $this->logger->warning('ProductRedirect: No valid product identifier provided');
                return $redirect->setPath('/');
            }

            // CRITICAL FIX: Reload product in the CURRENT store context to ensure correct URL
            // This ensures getProductUrl() returns the URL for the current store, not the store
            // where the product was initially loaded
            $productInCurrentStore = $this->productRepository->getById(
                $product->getId(),
                false,
                $currentStoreId
            );
            
            $productUrl = $productInCurrentStore->getProductUrl();

            // Log successful redirect
            $this->logger->info(sprintf(
                'ProductRedirect: Redirecting %s "%s" to %s (Store ID: %s)',
                $identifierType,
                $identifier,
                $productUrl,
                $currentStoreId
            ));

            // 301 redirect to SEO-friendly URL
            return $redirect->setHttpResponseCode(301)->setUrl($productUrl);

        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('An error occurred while loading the product.')
            );
            $this->logger->error('ProductRedirect Error: ' . $e->getMessage());
            return $redirect->setPath('/');
        }
    }

    /**
     * Load product by SKU for a specific store
     *
     * @param string $sku
     * @param int $storeId
     * @return \Magento\Catalog\Api\Data\ProductInterface|null
     */
    protected function loadProductBySku($sku, $storeId)
    {
        try {
            return $this->productRepository->get($sku, false, $storeId);
        } catch (NoSuchEntityException $e) {
            $this->logger->warning(sprintf(
                'ProductRedirect: Product with SKU "%s" not found in store %s',
                $sku,
                $storeId
            ));
            return null;
        }
    }

    /**
     * Load product by ID for a specific store
     *
     * @param int $productId
     * @param int $storeId
     * @return \Magento\Catalog\Api\Data\ProductInterface|null
     */
    protected function loadProductById($productId, $storeId)
    {
        try {
            return $this->productRepository->getById($productId, false, $storeId);
        } catch (NoSuchEntityException $e) {
            $this->logger->warning(sprintf(
                'ProductRedirect: Product with ID "%s" not found in store %s',
                $productId,
                $storeId
            ));
            return null;
        }
    }
}