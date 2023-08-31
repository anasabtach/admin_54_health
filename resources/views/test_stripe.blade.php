<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Stripe Subscription Form</title>
</head>
<body>

<div class="panel">
    <form action="{{ route('stripe_test') }}" method="POST" id="payment-form">
        @csrf
        <div class="panel-heading">
            <h3 class="panel-title">Plan Subscription with Stripe</h3>
			
            <!-- Plan Info -->
            <p>
                <b>Select Plan:</b>
                <select name="subscr_plan" id="subscr_plan">
                    <!-- Populate options here -->
                </select>
            </p>
        </div>
        <div class="panel-body">
            <div id="card-element"></div>
            <div id="card-errors" role="alert"></div>
            <button type="submit" data-secret="{{ $intent }}" id="card-button" class="btn btn-success">Subscribe</button>
        </div>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://js.stripe.com/v3/"></script>
<script>
$(document).ready(function(){
    const stripe = Stripe('pk_test_51NfnBdGkONwmLpxCcOjr5Ui9kDzvyaOkuEcFSyKWAcilk7hBOlpsWm7jRUJiCU46cxTHkTaoYaa4AwDndYRgZI2H00NQoNJidR');
    const elements = stripe.elements();
    const card = elements.create('card', {
        hidePostalCode: true,
        style: {
            base: {
                color: '#32325d',
                fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                fontSmoothing: 'antialiased',
                fontSize: '16px',
                '::placeholder': {
                    color: '#aab7c4'
                }
            },
            invalid: {
                color: '#fa755a',
                iconColor: '#fa755a'
            }
        }
    });
    card.mount('#card-element');

    card.addEventListener('change', function(event) {
        const displayError = document.getElementById('card-errors');
        if (event.error) {
            displayError.textContent = event.error.message;
        } else {
            displayError.textContent = '';
        }
    });

    const cardButton = document.getElementById('card-button');
    const clientSecret = cardButton.dataset.secret;

    cardButton.addEventListener('click', async (e) => {
        e.preventDefault();
        const { setupIntent, error } = await stripe.confirmCardSetup(
            clientSecret, {
                payment_method: {
                    card: card
                }
            }
        );
        if (error) {
            const errorElement = document.getElementById('card-errors');
            errorElement.textContent = error.message;
        } else {
            // Payment method setup successful, handle further actions if needed
            paymentMethodHandler(setupIntent.payment_method);
        }
    });

    function paymentMethodHandler(payment_method) {
        const form = document.getElementById('payment-form');
        const hiddenInput = document.createElement('input');
        hiddenInput.setAttribute('type', 'hidden');
        hiddenInput.setAttribute('name', 'payment_method');
        hiddenInput.setAttribute('value', payment_method);
        form.appendChild(hiddenInput);
        form.submit();
    }
});
</script>

</body>
</html>
