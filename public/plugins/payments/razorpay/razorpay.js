class Razorpay_module{
    init(razorpay_array){

        var options = razorpay_array.razorpay_data;
        var order_slack = razorpay_array.order_slack;
        var new_order_link = razorpay_array.new_order_link;
        var order_print_link = razorpay_array.order_print_link;
        var public_order = order_data.public_order;

        const record_razorpay_payment_url = (public_order == true)?"/api/record_razorpay_payment_success_public":"/api/record_razorpay_payment_success"; 

        options.handler = function(response){
            record_razorpay_payment_success(response);
        };

        var rzp1 = new Razorpay(options);
        document.getElementById('rzp-button1').onclick = function(e){
            rzp1.open();
            e.preventDefault();
        }
        window.onload = function() {
            document.getElementById('rzp-button1').click();
        };

        var record_razorpay_payment_success = function(razorpay_response){
            if(typeof razorpay_response.razorpay_signature != 'undefined' && razorpay_response.razorpay_signature != ''){
                var response_order_data = {
                    access_token: window.settings.access_token,
                    order_slack: order_slack,
                    razorpay_response: JSON.stringify(razorpay_response),
                    public_order: public_order
                };

                fetch(record_razorpay_payment_url, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(response_order_data)
                }).then(function(data) {
                    document.getElementById("rzp-button1").style.visibility = 'hidden';
                    document.getElementById("razorpay-response-container").innerHTML = "<p>Payment was successfull</p><br><a href='"+order_print_link+"' target='_blank'>Print Order</a>";
                    window.location.href = order_print_link;
                    setTimeout(function() {
                        //window.location.href = new_order_link;
                    }, 3000);
                });
            }else{
                document.getElementById("razorpay-response-container").innerHTML = "<p>Payment Failed!, <b onclick='window.location.reload()' class='cursor'>Retry</b></p>";
            }
        }
    }

    
}