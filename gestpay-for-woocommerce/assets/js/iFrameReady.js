var GestpayIframe = {};

/**
 * Handle asynchronous security check result for the 1st and 2nd page load.
 */
GestpayIframe.PaymentPageLoad = function (Result) {
  // Check for errors: if the Result.ErroCode is 10 the iFrame
  // is created correctly and the security check are passed
  if (Result.ErrorCode == 10) {
    // Handle 3D authentication 2nd call
    var paRes = gestpayReadyObject.paRes;
    var transKey = gestpayReadyObject.transKey;

    if (paRes.length > 0 && transKey.length > 0) {
      // The cardholder land for the 2nd page load, after 3D Secure authentication,
      // so we can proceed to process the transaction without showing the form

      document.getElementById("gestpay-inner-freeze-pane-text").innerHTML =
        gestpayReadyObject.iframe_pay_progress;

      var params = {
        PARes: paRes,
        TransKey: transKey,
      };

      GestPay.SendPayment(params, GestpayIframe.PaymentCallBack);
    } else {
      // 1st page load: show the form with the credit card fields
      document.getElementById("gestpay-inner-freeze-pane").className =
        "gestpay-off";
      document.getElementById("gestpay-freeze-pane").className = "gestpay-off";
      document.getElementById("gestpay-cc-form").className = "gestpay-on";
    }
  } else {
    GestpayIframe.OnError(Result);
  }
};

/**
 * Handle payment results.
 */
GestpayIframe.PaymentCallBack = function (Result) {
  if (Result.ErrorCode == 0) {
    // --- Transaction correctly processed

    var baseUrl = gestpayReadyObject.ws_S2S_resp_url;

    // Decrypt the string to read the transaction results
    document.location.replace(
      baseUrl +
        "&a=" +
        gestpayReadyObject.shopLogin +
        "&b=" +
        Result.EncryptedString
    );
  } else {
    // --- An error has occurred: check for 3D authentication required

    if (Result.ErrorCode == 8006) {
      // The credit card is enrolled: we must send the card holder
      // to the authentication page on the issuer website

      var expDate = new Date();
      expDate.setTime(expDate.getTime() + 1200000);
      expDate = expDate.toGMTString();

      // Get the TransKey, IMPORTANT! this value must be stored for further use
      var TransKey = Result.TransKey;
      var SameSite = gestpayReadyObject.sameSite;
      document.cookie =
        "TransKey=" +
        TransKey.toString() +
        "; expires=" +
        expDate +
        " ; path=/" +
        SameSite;

      // Retrieve all parameters.
      var a = gestpayReadyObject.shopLogin;
      var b = Result.VBVRisp;

      // The landing page where the user will be redirected after the issuer authentication
      var c = document.location.href;

      // Redirect the user to the issuer authentication page
      var AuthUrl = gestpayReadyObject.pagam3d_url;

      document.location.replace(AuthUrl + "?a=" + a + "&b=" + b + "&c=" + c);
    } else {
      // Hide overlapping layer
      document.getElementById("gestpay-inner-freeze-pane").className =
        "gestpay-off";
      document.getElementById("gestpay-freeze-pane").className = "gestpay-off";
      document.getElementById("gestpay-submit").disabled = false;

      // Check the ErrorCode and ErrorDescription
      if (Result.ErrorCode == 1119 || Result.ErrorCode == 1120) {
        document.getElementById("gestpay-cc-number").focus();
      } else if (Result.ErrorCode == 1124 || Result.ErrorCode == 1126) {
        document.getElementById("gestpay-cc-exp-month").focus();
      } else if (Result.ErrorCode == 1125) {
        document.getElementById("gestpay-cc-exp-year").focus();
      } else if (Result.ErrorCode == 1149) {
        if (gestpayReadyObject.is_cvv_required)
          document.getElementById("gestpay-cc-cvv").focus();
      }

      GestpayIframe.OnError(Result);
    }
  }
};

GestpayIframe.OnError = function (Result) {
  //if (Result.ErrorCode === 'none') return;
  // Show the error box
  document.getElementById("gestpay-error-box").innerHTML =
    "Error: " + Result.ErrorCode + " - " + Result.ErrorDescription;
  document.getElementById("gestpay-error-box").className = "gestpay-on";
  document.getElementById("gestpay-inner-freeze-pane").className =
    "gestpay-off";
  document.getElementById("gestpay-freeze-pane").className = "gestpay-off";

  // Clean up cookies.
  document.cookie = "TransKey=;expires=Thu, 01 Jan 1970 00:00:01 GMT;";
  document.cookie = "GestPayEncString=;expires=Thu, 01 Jan 1970 00:00:01 GMT;";

  // Show the reload button
  document.getElementById("iframe-reload-btn").style.display = "inline-block";
};

/**
 * Send data to GestPay and process transaction.
 * @see gestpay-for-woocommerce/inc/checkout-payment-fields.php
 */
function gestpayCheckCC() {
  document.getElementById("gestpay-submit").disabled = true;
  document.getElementById("gestpay-freeze-pane").className =
    "gestpay-freeze-pane-on";
  document.getElementById("gestpay-inner-freeze-pane-text").innerHTML =
    gestpayReadyObject.iframe_pay_progress;
  document.getElementById("gestpay-inner-freeze-pane").className = "gestpay-on";

  var params = {
    CC: document.getElementById("gestpay-cc-number").value,
    EXPMM: document.getElementById("gestpay-cc-exp-month").value,
    EXPYY: document.getElementById("gestpay-cc-exp-year").value,
  };

  if (gestpayReadyObject.is_cvv_required)
    params.CVV2 = document.getElementById("gestpay-cc-cvv").value;

  GestPay.SendPayment(params, GestpayIframe.PaymentCallBack);

  // To free the Shop from the need to comply with PCI-DSS Security standard, the OnSubmit event
  // of the Credit card form must avoid to postback the Credit Card data to the checkout page!
  return false;
}

jQuery(document).ready(function ($) {
  if (typeof BrowserEnabled !== "undefined" && BrowserEnabled) {
    // Check if the browser support HTML5 postmessage
    var a = gestpayReadyObject.shopLogin;
    var b = gestpayReadyObject.encString;

    // Create the iFrame
    GestPay.CreatePaymentPage(a, b, GestpayIframe.PaymentPageLoad);

    // Raise the Overlap layer and text
    document.getElementById("gestpay-freeze-pane").className =
      "gestpay-freeze-pane-on";
    document.getElementById("gestpay-inner-freeze-pane-text").innerHTML =
      gestpayReadyObject.iframe_loading;
    document.getElementById("gestpay-inner-freeze-pane").className =
      "gestpay-on";
  } else {
    document.getElementById("gestpay-error-box").innerHTML =
      gestpayReadyObject.iframe_browser_err;
    document.getElementById("gestpay-error-box").className = "gestpay-on";
  }
});
