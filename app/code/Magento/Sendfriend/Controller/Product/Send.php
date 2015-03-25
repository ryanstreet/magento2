<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Sendfriend\Controller\Product;

class Send extends \Magento\Sendfriend\Controller\Product
{
    /**
     * Show Send to a Friend Form
     *
     * @return void
     */
    public function execute()
    {
        $product = $this->_initProduct();

        if (!$product) {
            $this->_forward('noroute');
            return;
        }
        /* @var $session \Magento\Catalog\Model\Session */
        $catalogSession = $this->_objectManager->get('Magento\Catalog\Model\Session');

        if ($this->sendFriend->getMaxSendsToFriend() && $this->sendFriend->isExceedLimit()) {
            $this->messageManager->addNotice(
                __('You can\'t send messages more than %1 times an hour.', $this->sendFriend->getMaxSendsToFriend())
            );
        }

        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();

        $this->_eventManager->dispatch('sendfriend_product', ['product' => $product]);
        $data = $catalogSession->getSendfriendFormData();
        if ($data) {
            $catalogSession->setSendfriendFormData(true);
            $block = $this->_view->getLayout()->getBlock('sendfriend.send');
            if ($block) {
                $block->setFormData($data);
            }
        }

        $this->_view->renderLayout();
    }
}
