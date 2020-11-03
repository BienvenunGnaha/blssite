<div class="col-12 border mt-3 p-2"> 
    <input id="cardholder-name" type="text" class="form-control" placeholder="Full Name">
    <!-- placeholder for Elements -->
    <form id="setup-form" >
        <div class="p-3"></div>
        <div id="card-element" class="m-2"></div>
        <div style="height: 15px;"></div>
        <button id="card-button" data-secret="{{$clientSecret}}" class="btn btn-primary bold uppercase">@lang('Subscribe')</button>
    </form>
</div>
<input type="hidden" id="bls-stripe-pk" value="{{$publishable_key}}">
<script>
        $(function(){
  
            var stripe = Stripe($('#bls-stripe-pk').val());
  
              var elements = stripe.elements();
              var cardElement = elements.create('card', {hidePostalCode: true});
              cardElement.mount('#card-element');
              function closeModal() {
                  overlay.style.display='none';
              }
  
              var cardholderName = document.getElementById('cardholder-name');
              var cardButton = document.getElementById('card-button');
              var clientSecret = cardButton.dataset.secret;
  
            cardButton.addEventListener('click', function(ev) {
                ev.preventDefault();
                stripe.confirmCardSetup(
                    clientSecret,
                    {
                    payment_method: {
                    card: cardElement,
                    billing_details: {
                    name: cardholderName.value,
                    },
                },
                }
            ).then(function(result) {
            if (result.error) {
                console.log(result.error);
                $.notify('Votre moyen de paiement n\'a pas été accepté.', 'error');
            } else {
                
            // 
                $('#pm').val(result.setupIntent.payment_method) ; 
                setTimeout(function(){
                    $('#recaptcha').trigger('click');
                }, 2000);
        }   
        })
        
        });

    });
    </script>
                            
