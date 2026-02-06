/**
 * Gestpay Payment Method Integration for WooCommerce Blocks
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
    "wc_gateway_gestpay"
  )
) {
  const { registerPaymentMethod } = window.wc.wcBlocksRegistry;
  const { getPaymentMethodData } = window.wc.wcSettings;
  const { decodeEntities } = window.wp.htmlEntities;
  const { createElement } = window.wp.element;
  const { useState, useEffect } = window.wp.element;
  const { __ } = window.wp.i18n;

  /**
   * Gestpay payment method config object.
   */
  const gestpayPaymentMethod = {
    name: "wc_gateway_gestpay",
    label: decodeEntities(
      getPaymentMethodData("wc_gateway_gestpay", {}).title ||
        __("Gestpay", "gestpay-for-woocommerce")
    ),
    content: createElement(GestpayContent),
    edit: createElement(GestpayEdit),
    canMakePayment: () => {
      // Check if payment method data is available
      const paymentMethodData = getPaymentMethodData("wc_gateway_gestpay", {});
      return paymentMethodData && paymentMethodData.title;
    },
    ariaLabel: decodeEntities(
      getPaymentMethodData("wc_gateway_gestpay", {}).title ||
        __("Payment via Gestpay", "gestpay-for-woocommerce")
    ),
    supports: {
      features: getPaymentMethodData("wc_gateway_gestpay", {}).supports || [],
    },
  };

  /**
   * Content component for the Gestpay payment method.
   */
  function GestpayContent(props) {
    const { eventRegistration, emitResponse } = props;
    const { onPaymentSetup } = eventRegistration;

    const paymentMethodData = getPaymentMethodData("wc_gateway_gestpay", {});
    const isS2S = paymentMethodData.isS2S || false;
    const isSandbox = paymentMethodData.isSandbox || false;
    const sandbox = paymentMethodData.sandbox || "";
    const cvvRequired = paymentMethodData.cvvRequired || false;
    const buyerName = paymentMethodData.buyerName || false;
    const expMonthLabel =
      paymentMethodData.expMonthLabel || __("MM", "gestpay-for-woocommerce");
    const expYearLabel =
      paymentMethodData.expYearLabel || __("YY", "gestpay-for-woocommerce");
    const expDateLabel =
      paymentMethodData.expDateLabel ||
      __("Expiry (MM/YY)", "gestpay-for-woocommerce");
    const cardholderNameLabel =
      paymentMethodData.cardholderNameLabel ||
      __("Cardholder Name", "gestpay-for-woocommerce");
    const cardNumberLabel =
      paymentMethodData.cardNumberLabel ||
      __("Card Number", "gestpay-for-woocommerce");
    const cardCVVLabel =
      paymentMethodData.cardCVVLabel ||
      __("Card Code", "gestpay-for-woocommerce");
    const infoBox = paymentMethodData.infoBox || "";
    const infoModal = paymentMethodData.infoModal || "";
    const savedCards = paymentMethodData.savedCards;
    const [defaultToken, setDefaultToken] = useState(
      paymentMethodData.defaultToken || "new-card"
    );
    const [cardData, setCardData] = useState({
      cardNumber: "",
      expiryMonth: "",
      expiryYear: "",
      cvv: "",
      cardholderName: "",
      token: defaultToken,
    });
    const manageCardsEndpoint = paymentMethodData.manageCardsEndpoint;
    const manageCardsText =
      paymentMethodData.manageCardsText ||
      __("Manage Cards", "gestpay-for-woocommerce");
    const newCardText =
      paymentMethodData.newCardText ||
      __("New Card", "gestpay-for-woocommerce");

    useEffect(() => {
      const unsubscribe = onPaymentSetup(async () => {
        // Here we can do any processing we need, and then emit a response.
        // For example, we might validate a custom field, or perform an AJAX request, and then emit a response indicating it is valid or not.
        //setIsProcessing(true);

        const customDataIsValid = true;

        if (customDataIsValid) {
          return {
            type: emitResponse.responseTypes.SUCCESS,
            meta: {
              paymentMethodData: {
                "gestpay-cc-number": cardData.cardNumber.replace(/\s/g, ""),
                "gestpay-cc-exp-month": cardData.expiryMonth,
                "gestpay-cc-exp-year": cardData.expiryYear,
                "gestpay-cc-buyer-name": cardData.cardholderName,
                "gestpay-cc-cvv": cardData.cvv,
                "gestpay-s2s-cc-token": cardData.token,
              },
            },
          };
        }

        return {
          type: emitResponse.responseTypes.ERROR,
          message: "There was an error",
        };
      });
      // Unsubscribes when this component is unmounted.
      return () => {
        unsubscribe();
      };
    }, [
      emitResponse.responseTypes.ERROR,
      emitResponse.responseTypes.SUCCESS,
      onPaymentSetup,
      cardData,
    ]);

    // Handle input changes
    const handleInputChange = (field, value) => {
      setCardData((prev) => ({
        ...prev,
        [field]: value,
      }));
    };

    const handleTokenChange = (token) => {
      setDefaultToken(token);
      handleInputChange("token", token);
    };

    // Format card number with spaces
    const formatCardNumber = (value) => {
      const v = value.replace(/\s+/g, "").replace(/[^0-9]/gi, "");
      const matches = v.match(/\d{4,16}/g);
      const match = (matches && matches[0]) || "";
      const parts = [];
      for (let i = 0, len = match.length; i < len; i += 4) {
        parts.push(match.substring(i, i + 4));
      }
      if (parts.length) {
        return parts.join(" ");
      } else {
        return v;
      }
    };

    // If not S2S mode, show simple content
    if (!isS2S) {
      return createElement(
        "div",
        {
          className: "gestpay-payment-method-content",
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
          isSandbox &&
            createElement("p", {
              key: "sandbox",
              dangerouslySetInnerHTML: {
                __html: decodeEntities(paymentMethodData.sandbox || ""),
              },
            }),
        ]
      );
    }

    // S2S mode - show credit card form
    return createElement(
      "div",
      {
        className: "gestpay-payment-method-content",
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

        isSandbox &&
          createElement("p", {
            key: "sandbox",
            dangerouslySetInnerHTML: {
              __html: decodeEntities(sandbox || ""),
            },
          }),

        // Saved cards
        savedCards &&
          createElement(
            "p",
            {
              key: "saved-cards",
              className: "form-row form-row-wide",
            },
            [
              createElement(
                "a",
                {
                  key: "manage-cards",
                  href: manageCardsEndpoint,
                  target: "_blank",
                },
                manageCardsText
              ),
              createElement(
                "div",
                {
                  key: "clear-div",
                  className: "clear",
                },
                null
              ),
              savedCards.map((card, index) => {
                return [
                  createElement("input", {
                    key: `gestpay-s2s-cc-token-${index}`,
                    id: `gestpay-s2s-cc-token-${index}`,
                    name: "gestpay-s2s-cc-token",
                    className: "gestpay-s2s-card-selection",
                    style: {
                      width: "auto",
                      display: "inline-block",
                      outline: "none",
                    },
                    type: "radio",
                    value: card.token,
                    checked: defaultToken === card.token,
                    onChange: (e) => handleTokenChange(card.token),
                  }),
                  createElement(
                    "label",
                    {
                      key: `gestpay-s2s-cc-token-label-${index}`,
                      htmlFor: `gestpay-s2s-cc-token-${index}`,
                      style: {
                        display: "inline-block",
                      },
                    },
                    card.expir_str
                  ),
                  createElement("br", {
                    key: `gestpay-s2s-cc-token-br-${index}`,
                  }),
                ];
              }),
              createElement("input", {
                key: "new-card",
                id: "gestpay-s2s-use-new-card",
                type: "radio",
                name: "gestpay-s2s-cc-token",
                value: "new-card",
                checked: defaultToken === "new-card",
                onChange: (e) => handleTokenChange("new-card"),
                style: {
                  width: "auto",
                  display: "inline-block",
                  outline: "none",
                },
              }),
              createElement(
                "label",
                {
                  key: "new-card-label",
                  htmlFor: "gestpay-s2s-use-new-card",
                  style: {
                    display: "inline-block",
                  },
                },
                newCardText
              ),
            ]
          ),

        // Credit card form
        defaultToken === "new-card" &&
          createElement(
            "div",
            {
              className: "gestpay-credit-card-form",
            },
            [
              // Card Number
              createElement(
                "div",
                {
                  key: "card-number",
                  className: "form-row form-row-wide",
                },
                [
                  createElement(
                    "label",
                    {
                      key: "label",
                      htmlFor: "gestpay-card-number",
                    },
                    cardNumberLabel
                  ),
                  createElement("input", {
                    key: "input",
                    type: "text",
                    id: "gestpay-card-number",
                    name: "gestpay-cc-number",
                    value: cardData.cardNumber,
                    onChange: (e) =>
                      handleInputChange(
                        "cardNumber",
                        formatCardNumber(e.target.value)
                      ),
                    placeholder: "1234 5678 9012 3456",
                    maxLength: 19,
                    required: true,
                    className: "input-text",
                  }),
                ]
              ),

              createElement(
                "div",
                {
                  key: "dateAndCvv",
                  style: {
                    overflow: "auto",
                  },
                },
                [
                  // Expiry Date
                  createElement(
                    "div",
                    {
                      key: "expiry",
                      className: "form-row form-row-first",
                    },
                    [
                      createElement(
                        "label",
                        {
                          key: "label",
                          htmlFor: "gestpay-expiry-month",
                        },
                        expDateLabel
                      ),
                      createElement(
                        "div",
                        {
                          key: "expiry-inputs",
                          className: "expiry-inputs",
                        },
                        [
                          createElement(
                            "select",
                            {
                              key: "month",
                              id: "gestpay-expiry-month",
                              name: "gestpay_expiry_month",
                              value: cardData.expiryMonth,
                              onChange: (e) =>
                                handleInputChange(
                                  "expiryMonth",
                                  e.target.value
                                ),
                              required: true,
                              className: "input-text",
                            },
                            [
                              createElement(
                                "option",
                                { key: "default", value: "" },
                                expMonthLabel
                              ),
                              ...Array.from({ length: 12 }, (_, i) => {
                                const month = String(i + 1).padStart(2, "0");
                                return createElement(
                                  "option",
                                  { key: month, value: month },
                                  month
                                );
                              }),
                            ]
                          ),
                          createElement(
                            "select",
                            {
                              key: "year",
                              id: "gestpay-expiry-year",
                              name: "gestpay_expiry_year",
                              value: cardData.expiryYear,
                              onChange: (e) =>
                                handleInputChange("expiryYear", e.target.value),
                              required: true,
                              className: "input-text",
                            },
                            [
                              createElement(
                                "option",
                                { key: "default", value: "" },
                                expYearLabel
                              ),
                              ...Array.from({ length: 10 }, (_, i) => {
                                const year = String(
                                  new Date().getFullYear() + i
                                );
                                return createElement(
                                  "option",
                                  {
                                    key: year.slice(-2),
                                    value: year.slice(-2),
                                  },
                                  year
                                );
                              }),
                            ]
                          ),
                        ]
                      ),
                    ]
                  ),

                  // CVV
                  cvvRequired &&
                    createElement(
                      "div",
                      {
                        key: "cvv",
                        className: "form-row form-row-last",
                      },
                      [
                        createElement(
                          "label",
                          {
                            key: "label",
                            htmlFor: "gestpay-cvv",
                          },
                          cardCVVLabel
                        ),
                        createElement("input", {
                          key: "input",
                          type: "text",
                          id: "gestpay-cvv",
                          name: "gestpay_cvv",
                          value: cardData.cvv,
                          onChange: (e) =>
                            handleInputChange(
                              "cvv",
                              e.target.value.replace(/\D/g, "")
                            ),
                          placeholder: "123",
                          maxLength: 4,
                          required: true,
                          className: "input-text",
                        }),
                        createElement(
                          "span",
                          {
                            key: "cvv-info",
                            className: "description",
                          },
                          [
                            createElement(
                              "a",
                              {
                                key: "cvv-info-link",
                                className: "cvv-info-link",
                                id: "gestpay-fancybox-cvv-link",
                                "data-fancybox": "data-src",
                                "data-src": "#gestpay-fancybox-cvv-modal",
                                href: "javascript:;",
                              },
                              infoBox
                            ),
                          ]
                        ),
                      ]
                    ),

                  // Fancybox modal
                  createElement("div", {
                    key: "fancybox-modal",
                    dangerouslySetInnerHTML: {
                      __html: decodeEntities(infoModal || ""),
                    },
                  }),
                ]
              ),

              // Cardholder Name
              buyerName &&
                createElement(
                  "div",
                  {
                    key: "cardholder",
                    className: "form-row form-row-wide",
                  },
                  [
                    createElement(
                      "label",
                      {
                        key: "label",
                        htmlFor: "gestpay-cc-buyer-name",
                      },
                      cardholderNameLabel
                    ),
                    createElement("input", {
                      key: "input",
                      type: "text",
                      id: "gestpay-cc-buyer-name",
                      name: "gestpay_cardholder_name",
                      value: cardData.cardholderName,
                      onChange: (e) =>
                        handleInputChange("cardholderName", e.target.value),
                      required: true,
                      className: "input-text",
                    }),
                  ]
                ),
            ]
          ),
      ]
    );
  }

  /**
   * Edit component for the Gestpay payment method (admin/editor view).
   */
  function GestpayEdit() {
    const paymentMethodData = getPaymentMethodData("wc_gateway_gestpay", {});

    return createElement(
      "div",
      {
        className: "gestpay-payment-method-edit",
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
      ]
    );
  }

  // Register the payment method
  registerPaymentMethod(gestpayPaymentMethod);

  // Mark as registered
  window.wc.wcBlocksRegistry.__registeredPaymentMethods.add(
    "wc_gateway_gestpay"
  );
}
