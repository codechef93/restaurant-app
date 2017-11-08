class Paypal_module{
    init(paypal, order_data){

        var order_amount = order_data.order_amount;
        var order_slack = order_data.order_slack;
        var new_order_link = order_data.new_order_link;
        var order_print_link = order_data.order_print_link;
        var public_order = order_data.public_order;
        
        const paypal_order_data_url = (public_order == true)?"/api/get_paypal_order_data_public":"/api/get_paypal_order_data"; 

        paypal.Buttons({
            createOrder: function(data, actions) {
                // This function sets up the details of the transaction, including the amount and line item details.
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: order_amount
                        }
                    }]
                });
            },
            onApprove: function(data, actions) {
              // This function captures the funds from the transaction.
              return actions.order.capture().then(function(details) {
                
                $('#paypal-button-container').html("<p>Please wait..</p>");

                if(details['status'] == 'COMPLETED'){
                    // Call your server to save the transaction
                    return fetch(paypal_order_data_url, {
                        method: 'post',
                        headers: {
                            'content-type': 'application/json'
                        },
                        body: JSON.stringify({
                            access_token: window.settings.access_token,
                            order_slack: order_slack,
                            order_id: data.orderID,
                            public_order: public_order
                        })
                    }).then(function(data) {
                        $('#paypal-button-container').html("<p>Payment was successfull</p><br><a href='"+order_print_link+"' target='_blank'>Print Order</a>");
                        setTimeout(function() {
                            //window.location.href = order_print_link;
                        }, 1800);
                    });
                }else{
                    $('#paypal-button-container').html("Payment failed! <b onclick='window.location.reload()' class='cursor'>Retry</b>");
                }
              });
            }
        }).render('#paypal-button-container');
    }
}