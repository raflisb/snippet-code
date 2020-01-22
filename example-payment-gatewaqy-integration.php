 // ===================== Midtrans Payment Gateway integrration =========================
 // write on Lumen 6 
 
 
 // 3ds function OTP 
 
 public function midtrans3ds(Request $request) {


        $this->validate($request, [
          'provider_id' => 'required',
          'provider_name' => 'required',
          'total_amount' => 'required',
          'services' => 'required',
          'product' => 'required',
          'order_id' => 'required',
          'token_id' => 'required',
        ]);

//check cicilan / installment
      if ($request->installment_term == null || $request->installment_term == '') {
        $curl_data = [
          'payment_type' => 'credit_card',
          'transaction_details'=> [
            'order_id' => $request->order_id,
            'gross_amount' => $request->total_amount
          ],
          'credit_card'=> [
            "token_id" => $request->token_id,
            "authentication" => true,
          ]
        ];

      }
      else {
        $curl_data = [
          'payment_type' => 'credit_card',
          'transaction_details'=> [
            'order_id' => $request->order_id,
            'gross_amount' => $request->price
          ],
          'credit_card'=> [
            "token_id" => $request->token_id,
            "authentication" => true,
            "installment_term" => $request->installment_term
          ]
        ];

      }

      if ($request->save_token_id == true) {
        $curl_data['save_token_id'] = true;
      }

        // encode to json

        $curl_json_data = json_encode($curl_data);


        $ch = curl_init('https://api.sandbox.midtrans.com/v2/charge');

        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curl_json_data);

        $midtransResponse = curl_exec($ch);

        curl_close($ch);

        $transaction_obj = json_decode($midtransResponse,true);

        if($transaction_obj['status_code'] == "200" || $transaction_obj['status_code'] == "201" )
        {
            $respons = [
              'status' => 'success',
              'status_code' => 200,
              'redirect_url' => $transaction_obj['redirect_url'],
              'message' => ' 3ds authentication',
              'installment_term' => $transaction_obj['installment_term']
            ];
            return response()->json($respons);
          }

        else {
          $respons = [
            'status' => 'error',
            'message'=> 'testing for error',
            'status_code' => 500,
            'message' => $transaction_obj,
          ];

          return response()->json($respons);
        }
    }




    public function saveToDb(Request $request) {


      $transaction = New Transaction();
      $transaction->services = $request->services;
      $transaction->order_id = $request->order_id;
      $transaction->amount= $request->price;
      $transaction->bank = $request->bank;
      $transaction->payment_type = 'credit_card';
      $transaction->provider_id = $request->provider_id;
      $transaction->product= $request->product;
      $transaction->provider_name= $request->provider_name;

      $transaction->save();
    }
