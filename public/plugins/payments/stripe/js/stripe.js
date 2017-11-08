class Stripe_module{
    init(order_data){
        "use strict";

        var order_slack = order_data.order_slack;
        var new_order_link = order_data.new_order_link;
        var order_print_link = order_data.order_print_link;
        var public_order = order_data.public_order;

        const stripe_payment_intent_url = (public_order == true)?"/api/get_stripe_payment_intent_public":"/api/get_stripe_payment_intent"; 
        const stripe_stripe_payment_success_url = (public_order == true)?"/api/record_stripe_payment_success_public":"/api/record_stripe_payment_success"; 

        var stripe;

        var orderData = {
            access_token: window.settings.access_token,
            order_slack: order_slack,
        };

        // Disable the button until we have Stripe set up on the page
        document.querySelector("button").disabled = true;

        fetch(stripe_payment_intent_url, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(orderData)
        })
        .then(function(result) {
            return result.json();
        })
        .then(function(data) {
            return setupElements(data);
        })
        .then(function({ stripe, card, clientSecret }) {
            document.querySelector("button").disabled = false;

            // Handle form submission.
            var form = document.getElementById("payment-form");
            form.addEventListener("submit", function(event) {
                event.preventDefault();
                // Initiate payment when the submit button is clicked
                pay(stripe, card, clientSecret);
            });
        });

        // Set up Stripe.js and Elements to use in checkout form
        var setupElements = function(data) {
            stripe = Stripe(data.publishableKey);
            var elements = stripe.elements();
            var style = {
                base: {
                color: "#32325d",
                fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                fontSmoothing: "antialiased",
                fontSize: "16px",
                "::placeholder": {
                    color: "#aab7c4"
                }
                },
                invalid: {
                color: "#fa755a",
                iconColor: "#fa755a"
                }
            };

            var card = elements.create("card", { style: style });
            card.mount("#card-element");

            return {
                stripe: stripe,
                card: card,
                clientSecret: data.clientSecret
            };
        };

        /*
        * Calls stripe.confirmCardPayment which creates a pop-up modal to
        * prompt the user to enter extra authentication details without leaving your page
        */
        var pay = function(stripe, card, clientSecret) {
        changeLoadingState(true);

        // Initiate the payment.
        // If authentication is required, confirmCardPayment will automatically display a modal
        stripe
            .confirmCardPayment(clientSecret, {
            payment_method: {
                card: card
            }
            })
            .then(function(result) {
            if (result.error) {
                // Show error to your customer
                showError(result.error.message);
            } else {
                // The payment has been processed!
                orderComplete(clientSecret);
            }
            });
        };

        /* ------- Post-payment helpers ------- */

        /* Shows a success / error message when the payment is complete */
        var orderComplete = function(clientSecret) {
            // Just for the purpose of the sample, show the PaymentIntent response object
            stripe.retrievePaymentIntent(clientSecret).then(function(result) {
                var paymentIntent = result.paymentIntent;
                var paymentIntentJson = JSON.stringify(paymentIntent, null, 2);
                
                document.querySelector(".sr-payment-form").classList.add("hidden");

                document.querySelector(".sr-result").classList.remove("hidden");
                setTimeout(function() {
                    document.querySelector(".sr-result").classList.add("expand");
                }, 200);

                changeLoadingState(false);
                
                var parsed_json_response = JSON.parse(paymentIntentJson);
                if(parsed_json_response.status == "succeeded"){
                    document.querySelector("pre").textContent = "Payment was successfull!";
                    record_stripe_payment_success(orderData.order_slack, paymentIntentJson);
                    setTimeout(function() {
                        window.location.href = order_print_link;
                    }, 1800);
                }else{
                    document.querySelector("pre").innerHTML = "Payment failed! <b onclick='window.location.reload()' class='cursor'>Retry</b>";
                }
            });
        };

        var showError = function(errorMsgText) {
            changeLoadingState(false);
            var errorMsg = document.querySelector(".sr-field-error");
            errorMsg.textContent = errorMsgText;
            setTimeout(function() {
                errorMsg.textContent = "";
            }, 4000);
        };

        // Show a spinner on payment submission
        var changeLoadingState = function(isLoading) {
            if (isLoading) {
                document.querySelector("button").disabled = true;
                document.querySelector("#spinner").classList.remove("hidden");
                document.querySelector("#button-text").classList.add("hidden");
            } else {
                document.querySelector("button").disabled = false;
                document.querySelector("#spinner").classList.add("hidden");
                document.querySelector("#button-text").classList.remove("hidden");
            }
        };

        var record_stripe_payment_success = function(order_slack, stripe_response){

            var response_order_data = {
                access_token: window.settings.access_token,
                order_slack: order_slack,
                stripe_response: stripe_response,
                public_order: public_order
            };

            fetch(stripe_stripe_payment_success_url, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(response_order_data)
            });
        }
    }
}