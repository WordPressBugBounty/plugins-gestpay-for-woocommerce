/**
 * Gestpay PayPal Payment Method Integration for WooCommerce Blocks
 *
 * @package Gestpay_For_WooCommerce
 * @since 20250912
 */

// Check if already registered to avoid conflicts
if (
  typeof window.wc !== "undefined" &&
  window.wc.wcBlocksRegistry &&
  !window.wc.wcBlocksRegistry.__registeredPaymentMethods
) {
  window.wc.wcBlocksRegistry.__registeredPaymentMethods = new Set();
}

// Only register if not already registered
if (
  !window.wc.wcBlocksRegistry.__registeredPaymentMethods.has(
    "wc_gateway_gestpay_paypal"
  )
) {
  const { registerPaymentMethod } = window.wc.wcBlocksRegistry;
  const { getPaymentMethodData } = window.wc.wcSettings;
  const { decodeEntities } = window.wp.htmlEntities;
  const { createElement } = window.wp.element;
  const { useState, useEffect } = window.wp.element;
  const { __ } = window.wp.i18n;

  /**
   * Gestpay PayPal payment method config object.
   */
  const gestpayPayPalPaymentMethod = {
    name: "wc_gateway_gestpay_paypal",
    label: decodeEntities(
      getPaymentMethodData("wc_gateway_gestpay_paypal", {}).title ||
        __("PayPal", "woocommerce")
    ),
    content: createElement(GestpayPayPalContent),
    edit: createElement(GestpayPayPalEdit),
    canMakePayment: () => {
      // Check if payment method data is available
      const paymentMethodData = getPaymentMethodData(
        "wc_gateway_gestpay_paypal",
        {}
      );
      return paymentMethodData && paymentMethodData.title;
    },
    ariaLabel: decodeEntities(
      getPaymentMethodData("wc_gateway_gestpay_paypal", {}).title ||
        __("Payment via PayPal", "woocommerce")
    ),
    supports: {
      features:
        getPaymentMethodData("wc_gateway_gestpay_paypal", {}).supports || [],
    },
  };

  /**
   * Content component for the Gestpay PayPal payment method.
   */
  function GestpayPayPalContent(props) {
    const { eventRegistration, emitResponse } = props;
    const { onPaymentSetup } = eventRegistration;

    useEffect(() => {
      const unsubscribe = onPaymentSetup(async () => {
        try {
          // For PayPal, we redirect to GestPay's payment page
          // The actual payment processing happens on the GestPay side
          const redirectUrl =
            paymentMethodData.redirectUrl || getPayPalRedirectUrl();

          return {
            type: emitResponse.responseTypes.REDIRECT,
            meta: {
              redirectUrl: redirectUrl,
            },
          };
        } catch (error) {
          console.error("PayPal payment error:", error);
          return {
            type: emitResponse.responseTypes.ERROR,
            message: "Payment processing failed. Please try again.",
          };
        }
      });

      return unsubscribe;
    }, [onPaymentSetup, emitResponse.responseTypes]);

    const paymentMethodData = getPaymentMethodData(
      "wc_gateway_gestpay_paypal",
      {}
    );

    return createElement(
      "div",
      {
        className: "wc-gestpay-paypal-payment-method",
      },
      [
        createElement("p", {
          key: "description",
          dangerouslySetInnerHTML: {
            __html: decodeEntities(paymentMethodData.description || ""),
          },
        }),
        createElement("div", {
          key: "icon",
          className: "gestpay-payment-method-icon",
          dangerouslySetInnerHTML: {
            __html: paymentMethodData.icon || "",
          },
        }),
        paymentMethodData.isSandbox &&
          createElement("p", {
            key: "sandbox",
            dangerouslySetInnerHTML: {
              __html: decodeEntities(paymentMethodData.sandbox || ""),
            },
          }),
      ]
    );
  }

  /**
   * Edit component for the Gestpay PayPal payment method.
   */
  function GestpayPayPalEdit() {
    const paymentMethodData = getPaymentMethodData(
      "wc_gateway_gestpay_paypal",
      {}
    );

    return createElement(
      "div",
      {
        className: "wc-gestpay-paypal-payment-method-edit",
      },
      [
        createElement("div", {
          className: "wc-gestpay-paypal-icon",
          dangerouslySetInnerHTML: { __html: paymentMethodData.icon },
        }),
        createElement(
          "div",
          {
            className: "wc-gestpay-paypal-title",
          },
          paymentMethodData.title
        ),
        createElement(
          "div",
          {
            className: "wc-gestpay-paypal-description",
          },
          paymentMethodData.description
        ),
      ]
    );
  }

  /**
   * Get the PayPal redirect URL for payment processing.
   * This should match the URL structure used by the main GestPay gateway.
   */
  function getPayPalRedirectUrl() {
    // Get the current checkout URL
    const checkoutUrl = window.location.href;

    // Add PayPal-specific parameters
    const url = new URL(checkoutUrl);
    url.searchParams.set("gestpay_payment_type", "PAYPAL");
    url.searchParams.set("gestpay_blocks", "1");
    url.searchParams.set("payment_method", "wc_gateway_gestpay_paypal");

    return url.toString();
  }

  // Register the payment method
  registerPaymentMethod(gestpayPayPalPaymentMethod);

  // Mark as registered
  window.wc.wcBlocksRegistry.__registeredPaymentMethods.add(
    "wc_gateway_gestpay_paypal"
  );
}
