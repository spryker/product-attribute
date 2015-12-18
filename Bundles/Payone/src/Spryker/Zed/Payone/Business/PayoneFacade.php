<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Payone\Business;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\PayoneCreditCardTransfer;
use Generated\Shared\Transfer\PayonePaymentTransfer;
use Generated\Shared\Transfer\PayoneRefundTransfer;
use Generated\Shared\Transfer\PaymentDataTransfer;
use Generated\Shared\Transfer\PayoneAuthorizationCheckResponseTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\PayonePaymentLogTransfer;
use Generated\Shared\Transfer\PayoneTransactionStatusUpdateTransfer;
use Propel\Runtime\Collection\ObjectCollection;
use Spryker\Zed\Kernel\Business\AbstractFacade;
use Spryker\Zed\Payone\Business\Api\Response\Container\AuthorizationResponseContainer;
use Spryker\Zed\Payone\Business\Api\Response\Container\DebitResponseContainer;
use Spryker\Zed\Payone\Business\Api\Response\Container\RefundResponseContainer;
use Spryker\Zed\Payone\Business\Api\Response\Container\CaptureResponseContainer;
use Spryker\Zed\Payone\Business\Api\Response\Container\CreditCardCheckResponseContainer;
use Spryker\Zed\Payone\Business\Api\TransactionStatus\TransactionStatusResponse;

/**
 * @method PayoneBusinessFactory getFactory()
 */
class PayoneFacade extends AbstractFacade
{

    /**
     * @param OrderTransfer $orderTransfer
     *
     * @return void
     */
    public function saveOrder(OrderTransfer $orderTransfer)
    {
        $this->getFactory()->createOrderManager()->saveOrder($orderTransfer);
    }

    /**
     * @param int $idPayment
     *
     * @return AuthorizationResponseContainer
     */
    public function authorizePayment($idPayment)
    {
        return $this->getFactory()->createPaymentManager()->authorizePayment($idPayment);
    }

    /**
     * @param int $idPayment
     *
     * @return AuthorizationResponseContainer
     */
    public function preAuthorizePayment($idPayment)
    {
        return $this->getFactory()->createPaymentManager()->preAuthorizePayment($idPayment);
    }

    /**
     * @param int $idPayment
     *
     * @return CaptureResponseContainer
     */
    public function capturePayment($idPayment)
    {
        return $this->getFactory()->createPaymentManager()->capturePayment($idPayment);
    }

    /**
     * @param int $idPayment
     *
     * @return DebitResponseContainer
     */
    public function debitPayment($idPayment)
    {
        return $this->getFactory()->createPaymentManager()->debitPayment($idPayment);
    }

    /**
     * @param PayoneRefundTransfer $refundTransfer
     *
     * @return RefundResponseContainer
     */
    public function refundPayment(PayoneRefundTransfer $refundTransfer)
    {
        return $this->getFactory()->createPaymentManager()->refundPayment($refundTransfer);
    }

    /**
     * @param PayoneCreditCardTransfer $creditCardData
     *
     * @return CreditCardCheckResponseContainer
     */
    public function creditCardCheck(PayoneCreditCardTransfer $creditCardData)
    {
        return $this->getFactory()->createPaymentManager()->creditCardCheck($creditCardData);
    }

    /**
     * @param PayoneTransactionStatusUpdateTransfer $transactionStatusUpdateTransfer
     *
     * @return TransactionStatusResponse
     */
    public function processTransactionStatusUpdate(PayoneTransactionStatusUpdateTransfer $transactionStatusUpdateTransfer)
    {
        $transactionManager = $this->getFactory()->createTransactionStatusManager();
        $transactionTransfer = $this->getFactory()->createTransactionStatusUpdateRequest($transactionStatusUpdateTransfer);

        return $transactionManager->processTransactionStatusUpdate($transactionTransfer);
    }

    /**
     * @param OrderTransfer $orderTransfer
     *
     * @return bool
     */
    public function isAuthorizationApproved(OrderTransfer $orderTransfer)
    {
        return $this->getFactory()->createApiLogFinder()->isAuthorizationApproved($orderTransfer);
    }

    /**
     * @param OrderTransfer $orderTransfer
     *
     * @return bool
     */
    public function isAuthorizationRedirect(OrderTransfer $orderTransfer)
    {
        return $this->getFactory()->createApiLogFinder()->isAuthorizationRedirect($orderTransfer);
    }

    /**
     * @param OrderTransfer $orderTransfer
     *
     * @return bool
     */
    public function isPreauthorizationApproved(OrderTransfer $orderTransfer)
    {
        return $this->getFactory()->createApiLogFinder()->isPreauthorizationApproved($orderTransfer);
    }

    /**
     * @param OrderTransfer $orderTransfer
     *
     * @return bool
     */
    public function isPreauthorizationRedirect(OrderTransfer $orderTransfer)
    {
        return $this->getFactory()->createApiLogFinder()->isPreauthorizationRedirect($orderTransfer);
    }

    /**
     * @param OrderTransfer $orderTransfer
     *
     * @return bool
     */
    public function isAuthorizationError(OrderTransfer $orderTransfer)
    {
        return $this->getFactory()->createApiLogFinder()->isAuthorizationError($orderTransfer);
    }

    /**
     * @param OrderTransfer $orderTransfer
     *
     * @return bool
     */
    public function isCaptureApproved(OrderTransfer $orderTransfer)
    {
        return $this->getFactory()->createApiLogFinder()->isCaptureApproved($orderTransfer);
    }

    /**
     * @param OrderTransfer $orderTransfer
     *
     * @return bool
     */
    public function isCaptureError(OrderTransfer $orderTransfer)
    {
        return $this->getFactory()->createApiLogFinder()->isCaptureError($orderTransfer);
    }

    /**
     * @param OrderTransfer $orderTransfer
     *
     * @return bool
     */
    public function isRefundApproved(OrderTransfer $orderTransfer)
    {
        return $this->getFactory()->createApiLogFinder()->isRefundApproved($orderTransfer);
    }

    /**
     * @param OrderTransfer $orderTransfer
     *
     * @return bool
     */
    public function isRefundError(OrderTransfer $orderTransfer)
    {
        return $this->getFactory()->createApiLogFinder()->isRefundError($orderTransfer);
    }

    /**
     * @param OrderTransfer $orderTransfer
     *
     * @return bool
     */
    public function isRefundPossible(OrderTransfer $orderTransfer)
    {
        return $this->getFactory()->createPaymentManager()->isRefundPossible($orderTransfer);
    }

    /**
     * @param OrderTransfer $orderTransfer
     *
     * @return bool
     */
    public function isPaymentDataRequired(OrderTransfer $orderTransfer)
    {
        return $this->getFactory()->createPaymentManager()->isPaymentDataRequired($orderTransfer);
    }

    /**
     * @param PayonePaymentTransfer $payment
     *
     * @return PayoneAuthorizationCheckResponseTransfer
     */
    public function getAuthorizationResponse(PayonePaymentTransfer $payment)
    {
        return $this->getFactory()->createApiLogFinder()->getAuthorizationResponse($payment);
    }

    /**
     * @param int $idSalesOrder
     * @param int $idSalesOrderItem
     *
     * @return bool
     */
    public function isPaymentNotificationAvailable($idSalesOrder, $idSalesOrderItem)
    {
        return $this->getFactory()
            ->createTransactionStatusManager()
            ->isPaymentNotificationAvailable($idSalesOrder, $idSalesOrderItem);
    }

    /**
     * @param int $idSalesOrder
     * @param int $idSalesOrderItem
     *
     * @return bool
     */
    public function isPaymentPaid($idSalesOrder, $idSalesOrderItem)
    {
        return $this->getFactory()
            ->createTransactionStatusManager()
            ->isPaymentPaid($idSalesOrder, $idSalesOrderItem);
    }

    /**
     * @param int $idSalesOrder
     * @param int $idSalesOrderItem
     *
     * @return bool
     */
    public function isPaymentOverpaid($idSalesOrder, $idSalesOrderItem)
    {
        return $this->getFactory()
            ->createTransactionStatusManager()
            ->isPaymentOverpaid($idSalesOrder, $idSalesOrderItem);
    }

    /**
     * @param int $idSalesOrder
     * @param int $idSalesOrderItem
     *
     * @return bool
     */
    public function isPaymentUnderpaid($idSalesOrder, $idSalesOrderItem)
    {
        return $this->getFactory()
            ->createTransactionStatusManager()
            ->isPaymentUnderpaid($idSalesOrder, $idSalesOrderItem);
    }

    /**
     * @param int $idSalesOrder
     * @param int $idSalesOrderItem
     *
     * @return bool
     */
    public function isPaymentAppointed($idSalesOrder, $idSalesOrderItem)
    {
        return $this->getFactory()
            ->createTransactionStatusManager()
            ->isPaymentAppointed($idSalesOrder, $idSalesOrderItem);
    }

    /**
     * @param int $idSalesOrder
     * @param int $idSalesOrderItem
     *
     * @return bool
     */
    public function isPaymentOther($idSalesOrder, $idSalesOrderItem)
    {
        return $this->getFactory()
            ->createTransactionStatusManager()
            ->isPaymentOther($idSalesOrder, $idSalesOrderItem);
    }

    /**
     * @param int $idSalesOrder
     * @param int $idSalesOrderItem
     *
     * @return bool
     */
    public function isPaymentCapture($idSalesOrder, $idSalesOrderItem)
    {
        return $this->getFactory()
            ->createTransactionStatusManager()
            ->isPaymentCapture($idSalesOrder, $idSalesOrderItem);
    }

    /**
     * @param OrderTransfer $orderTransfer
     * @param CheckoutResponseTransfer $checkoutResponse
     *
     * @return CheckoutResponseTransfer
     */
    public function postSaveHook(OrderTransfer $orderTransfer, CheckoutResponseTransfer $checkoutResponse)
    {
        return $this->getFactory()
            ->createPaymentManager()
            ->postSaveHook($orderTransfer, $checkoutResponse);
    }

    /**
     * @param ObjectCollection $orders
     *
     * @return PayonePaymentLogTransfer[]
     */
    public function getPaymentLogs(ObjectCollection $orders)
    {
        return $this->getFactory()->createPaymentManager()->getPaymentLogs($orders);
    }

    /**
     * @param int $idPayment
     *
     * @return PaymentDataTransfer
     */
    public function getPaymentData($idPayment)
    {
        return $this->getFactory()->createPaymentManager()->getPaymentData($idPayment);
    }

    /**
     * @param PaymentDataTransfer $paymentData
     * @param int $idOrder
     *
     * @return void
     */
    public function updatePaymentDetail(PaymentDataTransfer $paymentData, $idOrder)
    {
        $this->getFactory()->createPaymentManager()->updatePaymentDetail($paymentData, $idOrder);
    }

}