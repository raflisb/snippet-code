 <script>
  var cardData = {
    "card_number": 4811111111111114,
    "card_exp_month": 02,
    "card_exp_year": 2023,
    "card_cvv": 123,
  };


  var postData = {
      "provider_id" : 5,
      "provider_name" : "OYO",
      "total_amount" : 5000 ,
      "services" : "Travelpedi",
      "product" : "HOTEL",
      "order_id" : "TVP999999H",
      "token_id" : "345734gb5nt456l",
      "payment_type_services" : "BOTH",
  }

  var saveData = {
   "provider_id" : 5,
   "provider_name" : "OYO",
   "total_amount" : 5000 ,
   "services" : "Travelpedi",
   "product" : "HOTEL",
   "order_id" : "TVP999999H",
   "token_id" : "345734gb5nt456l",
   "payment_type_services" : "BOTH",

  }


  function sendBackend(object) {

    $.ajax({ //Process the form using $.ajax()
       type      : 'POST', //Method type
       headers: {
         "api-key" : "dHJhdmVscGVkaTEyMy4="
       },
       url       : '/v1/apis/payment', //Your form processing file URL
       data      : object, //data
       dataType  : 'json',
       success   : function(data) {
                       console.log(data);

                      midtrans3dsAuth(data.redirect_url,optionsFor3ds,data.status_code,data.message)
                   }
        });
  }

  function saveToDb(objectData) {

    $.ajax({ //Process the form using $.ajax()
       type      : 'POST', //Method type
       url       : '/v1/apis/payment-save', //Your form processing file URL
       data      : objectData, //data
       dataType  : 'json',
       success   : function(data) {
                  console.log('saved');
                   }
        });
  }

  function midtrans3dsAuth(redirect_url, options3ds, status_code, message){
    if(status_code == "200" || status_code == "201" ){

      MidtransNew3ds.authenticate(redirect_url, options3ds);
      console.log(status_code);
      console.log(message);

    }
    else {
      alert('3ds Auth Error!');
      console.log(status_code);
      console.log(message);
    }

  }

  var options = {
    onSuccess: function(response){
      // Sukses mendapatkan token_id kartu, implementasi sesuai kebutuhan
      console.log('Success to get card token_id, response:', response);
      var token_id = response.token_id;

      // console.log('This is the card token_id:', token_id);

      postData.token_id = token_id;
      sendBackend(postData);


    },
    onFailure: function(response){
      // Gagal mendapatkan token_id kartu, implementasi sesuai kebutuhan
      console.log('Fail to get card token_id, response:', response);
    }
  };

  var optionsFor3ds = {
    performAuthentication: function(redirect_url){
      // Implement how you will open iframe to display 3ds authentication redirect_url to customer
      $('#iframeModal').append("<iframe frameborder='0' width='100%' height='400px' src='"+redirect_url+"'></iframe>");
      $('#modal3ds').modal('show');
    },
    onSuccess: function(response){
      // 3ds authentication success, implement payment success scenario
      $('#modal3ds').modal('hide');
      saveData.payment_type_device = 'Web';
      saveData.payment_type_midtrans = 'credit_card';
      saveData.save_token_id = false;

      saveToDb(saveData);
      
      alert('Transaction Success and Saved');

    },
    onFailure: function(response){
      // 3ds authentication failure, implement payment failure scenario
      console.log('response:',response);
    },
    onPending: function(response){
      // transaction is pending, transaction result will be notified later via POST notification, implement as you wish here
      console.log('response:',response);

    }
  };

  // trigger `authenticate` function


  MidtransNew3ds.getCardToken(cardData, options);
</script> 
