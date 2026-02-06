/**
 * Gestpay MyBank Payment Method Integration for WooCommerce Blocks
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
    "wc_gateway_gestpay_mybank"
  )
) {
  const { registerPaymentMethod } = window.wc.wcBlocksRegistry;
  const { getPaymentMethodData } = window.wc.wcSettings;
  const { decodeEntities } = window.wp.htmlEntities;
  const { createElement } = window.wp.element;
  const { useState, useEffect } = window.wp.element;
  const { __ } = window.wp.i18n;

  /**
   * Gestpay MyBank payment method config object.
   */
  const gestpayMyBankPaymentMethod = {
    name: "wc_gateway_gestpay_mybank",
    label: decodeEntities(
      getPaymentMethodData("wc_gateway_gestpay_mybank", {}).title ||
        __("MyBank", "gestpay-for-woocommerce")
    ),
    content: createElement(GestpayMyBankContent),
    edit: createElement(GestpayMyBankEdit),
    canMakePayment: () => {
      // Check if payment method data is available
      const paymentMethodData = getPaymentMethodData(
        "wc_gateway_gestpay_mybank",
        {}
      );
      return paymentMethodData && paymentMethodData.title;
    },
    ariaLabel: decodeEntities(
      getPaymentMethodData("wc_gateway_gestpay_mybank", {}).title ||
        __("Payment via MyBank", "gestpay-for-woocommerce")
    ),
    supports: {
      features:
        getPaymentMethodData("wc_gateway_gestpay_mybank", {}).supports || [],
    },
  };

  /**
   * Content component for the Gestpay MyBank payment method.
   */
  function GestpayMyBankContent(props) {
    const { eventRegistration, emitResponse } = props;
    const { onPaymentSetup } = eventRegistration;
    const [selectedBank, setSelectedBank] = useState("");
    const [banks, setBanks] = useState([]);
    const [loading, setLoading] = useState(false);
    const [selectWooInitialized, setSelectWooInitialized] = useState(false);

    const paymentMethodData = getPaymentMethodData(
      "wc_gateway_gestpay_mybank",
      {}
    );

    // Load banks on component mount
    useEffect(() => {
      if (paymentMethodData.mybankData && paymentMethodData.mybankData.banks) {
        setBanks(paymentMethodData.mybankData.banks);
      }
    }, [paymentMethodData]);

    // Initialize selectWoo when banks are loaded and component is mounted
    useEffect(() => {
      if (
        Object.keys(banks).length > 0 &&
        !selectWooInitialized &&
        window.jQuery &&
        window.jQuery.fn.selectWoo
      ) {
        // Small delay to ensure DOM is ready
        setTimeout(() => {
          const selectElement = document.getElementById(
            "gestpay-mybank-banklist"
          );
          if (selectElement) {
            window.jQuery(selectElement).selectWoo({
              placeholder:
                "--- " +
                __("Choose an option", "gestpay-for-woocommerce") +
                " ---",
              allowClear: false,
              width: "100%",
              language: {
                noResults: function () {
                  return __("No banks found", "gestpay-for-woocommerce");
                },
                searching: function () {
                  return __("Searching...", "gestpay-for-woocommerce");
                },
              },
            });

            // Listen to selectWoo change events
            window.jQuery(selectElement).on("change", function (e) {
              const value = e.target.value;
              setSelectedBank(value);
            });

            setSelectWooInitialized(true);
          }
        }, 250);
      }
    }, [banks, selectWooInitialized]);

    // Cleanup selectWoo when component unmounts
    useEffect(() => {
      return () => {
        if (selectWooInitialized && window.jQuery) {
          const selectElement = document.getElementById(
            "gestpay-mybank-banklist"
          );
          if (selectElement) {
            window.jQuery(selectElement).selectWoo("destroy");
          }
        }
      };
    }, [selectWooInitialized]);

    useEffect(() => {
      const unsubscribe = onPaymentSetup(async () => {
        try {
          setLoading(true);

          // Check if bank selection is required
          if (
            paymentMethodData.mybankData &&
            paymentMethodData.mybankData.requiredSelection
          ) {
            if (!selectedBank) {
              return {
                type: emitResponse.responseTypes.ERROR,
                message:
                  paymentMethodData.mybankData.strings
                    ?.gestpay_mybank_list_must || "Please select a bank.",
              };
            } else {
              return {
                type: emitResponse.responseTypes.SUCCESS,
                meta: {
                  paymentMethodData: {
                    gestpay_mybank_bank: selectedBank,
                  },
                },
              };
            }
          }

          // For MyBank, we redirect to GestPay's payment page
          // The actual payment processing happens on the GestPay side
          const redirectUrl = getMyBankRedirectUrl(selectedBank);

          return {
            type: emitResponse.responseTypes.REDIRECT,
            meta: {
              redirectUrl: redirectUrl,
            },
          };
        } catch (error) {
          console.error("MyBank payment error:", error);
          return {
            type: emitResponse.responseTypes.ERROR,
            message: "Payment processing failed. Please try again.",
          };
        } finally {
          setLoading(false);
        }
      });

      return unsubscribe;
    }, [onPaymentSetup, emitResponse.responseTypes, selectedBank]);

    return createElement(
      "div",
      {
        className: "wc-gestpay-mybank-payment-method",
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
        // MyBank banner and info
        createElement("div", {
          key: "mybank-info",
          className: "gestpay-mybank-info",
          dangerouslySetInnerHTML: {
            __html: getMyBankInfoHTML(paymentMethodData),
          },
        }),
        // Bank selection if required
        paymentMethodData.mybankData &&
          paymentMethodData.mybankData.requiredSelection &&
          createElement(
            "div",
            {
              key: "bank-selection",
              className: "gestpay-mybank-bank-selection",
            },
            [
              createElement("p", {
                key: "intro",
                dangerouslySetInnerHTML: {
                  __html:
                    paymentMethodData.mybankData.strings
                      ?.gestpay_mybank_list_intro || "Select your bank:",
                },
              }),
              createElement(
                "select",
                {
                  key: "bank-select",
                  id: "gestpay-mybank-banklist",
                  name: "gestpay_mybank_bank",
                  value: selectedBank,
                  required: true,
                  className: "woocommerce-select gestpay-mybank-banklist",
                },
                [
                  createElement(
                    "option",
                    {
                      key: "empty",
                      value: "",
                    },
                    "--- " +
                      __("Choose an option", "gestpay-for-woocommerce") +
                      " ---"
                  ),
                  ...Object.entries(banks).map(([code, name]) =>
                    createElement(
                      "option",
                      {
                        key: code,
                        value: code,
                      },
                      name
                    )
                  ),
                ]
              ),
              createElement(
                "span",
                {
                  key: "required",
                  className: "required",
                },
                " *"
              ),
              createElement(
                "p",
                {
                  key: "help",
                  className: "gestpay-mybank-help",
                },
                [
                  createElement("a", {
                    key: "help-link",
                    href: "https://mybank.eu/faq/",
                    target: "_blank",
                    dangerouslySetInnerHTML: {
                      __html:
                        paymentMethodData.mybankData.strings
                          ?.gestpay_mybank_list_notfound ||
                        "Can't find your bank?",
                    },
                  }),
                ]
              ),
            ]
          ),
        paymentMethodData.isSandbox &&
          createElement("p", {
            key: "sandbox",
            dangerouslySetInnerHTML: {
              __html: decodeEntities(paymentMethodData.sandbox || ""),
            },
          }),
        loading &&
          createElement(
            "div",
            {
              key: "loading",
              className: "gestpay-mybank-loading",
            },
            __("Processing payment...", "gestpay-for-woocommerce")
          ),
      ]
    );
  }

  /**
   * Edit component for the Gestpay MyBank payment method.
   */
  function GestpayMyBankEdit() {
    const paymentMethodData = getPaymentMethodData(
      "wc_gateway_gestpay_mybank",
      {}
    );

    return createElement(
      "div",
      {
        className: "wc-gestpay-mybank-payment-method-edit",
      },
      [
        createElement("div", {
          className: "wc-gestpay-mybank-icon",
          dangerouslySetInnerHTML: { __html: paymentMethodData.icon },
        }),
        createElement(
          "div",
          {
            className: "wc-gestpay-mybank-title",
          },
          paymentMethodData.title
        ),
        createElement(
          "div",
          {
            className: "wc-gestpay-mybank-description",
          },
          paymentMethodData.description
        ),
      ]
    );
  }

  /**
   * Get MyBank info HTML including banner and payoff text
   */
  function getMyBankInfoHTML(paymentMethodData) {
    if (
      !paymentMethodData.mybankData ||
      !paymentMethodData.mybankData.strings
    ) {
      return "";
    }

    const mybankUrl =
      '<a href="https://mybank.eu" target="_blank" title="MyBank"><strong>mybank.eu</strong></a>';

    const payoff = paymentMethodData.mybankData.strings.mybank_payoff || "";

    return "<p>" + payoff + " " + mybankUrl + "</p>";
  }

  /**
   * Get the MyBank redirect URL for payment processing.
   * This should match the URL structure used by the main GestPay gateway.
   */
  function getMyBankRedirectUrl(selectedBank) {
    // Get the current checkout URL
    const checkoutUrl = window.location.href;

    // Add MyBank-specific parameters
    const url = new URL(checkoutUrl);
    url.searchParams.set("gestpay_payment_type", "MYBANK");
    url.searchParams.set("gestpay_blocks", "1");
    url.searchParams.set("payment_method", "wc_gateway_gestpay_mybank");

    // Add selected bank if provided
    if (selectedBank) {
      url.searchParams.set("gestpay_mybank_bank", selectedBank);
    }

    return url.toString();
  }

  // Register the payment method
  registerPaymentMethod(gestpayMyBankPaymentMethod);

  // Mark as registered
  window.wc.wcBlocksRegistry.__registeredPaymentMethods.add(
    "wc_gateway_gestpay_mybank"
  );
}
