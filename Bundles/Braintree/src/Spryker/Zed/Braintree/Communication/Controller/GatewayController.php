<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Braintree\Communication\Controller;

use Generated\Shared\Transfer\QuoteTransfer;

/**
 * @method \Spryker\Zed\Braintree\Business\BraintreeFacade getFacade()
 */
class GatewayController extends AbstractGatewayController
{

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\BraintreeCalculationResponseTransfer
     */
    public function calculateInstallmentPaymentsAction(QuoteTransfer $quoteTransfer)
    {
        return $this->getFacade()->calculateInstallmentPayments($quoteTransfer);
    }

}
