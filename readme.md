Here my snipped code of payment gateway integration using lumen 6 (laravel microframework)

Payment-gateway : Midtrans ( https://docs.midtrans.com/en/core_api/integration_card_basic.html) 

midtrans using JS to create a token from user input 

they will encrypt the card (visa,masteecard) number to an token 
before the backend send it via post to their services 

first they will encrypt the card data 
second my backend send to their service then we will get a response with a value of url to send OTP (it can be use by redirect or IFRAME) 
third after the user insertt OTP they gonna procced the payment 
