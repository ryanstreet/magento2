<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Sales\Controller\Adminhtml\Transactions;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect;

class Fetch extends \Magento\Sales\Controller\Adminhtml\Transactions
{
    /**
     * Fetch transaction details action
     *
     * @return Redirect
     */
    public function execute()
    {
        $txn = $this->_initTransaction();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if (!$txn) {
            return $resultRedirect->setPath('sales/*/');
        }
        try {
            $txn->getOrderPaymentObject()->setOrder($txn->getOrder())->importTransactionInfo($txn);
            $txn->save();
            $this->messageManager->addSuccess(__('The transaction details have been updated.'));
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError(__('We can\'t update the transaction details.'));
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
        }
        return $resultRedirect->setPath('sales/transactions/view', ['_current' => true]);
    }
}
