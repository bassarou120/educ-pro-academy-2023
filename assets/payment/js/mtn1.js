
// create token
function createToken(Ocp_Apim_Subscription_Key,X_Reference_Id, ApiKey) {

    console.log("demande de token");
    // var proxy = 'https://cors-anywhere.herokuapp.com/';

  var proxy = 'https://cors-anywhere.herokuapp.com/';
  //  var proxy = '';

    var url=  "https://sandbox.momodeveloper.mtn.com/collection/token/";

    var settings = {
        "url": proxy + url,
        "method": "POST",
        "timeout": 0,
        "headers": {
            "Ocp-Apim-Subscription-Key": Ocp_Apim_Subscription_Key,
            "Authorization": "Basic "+ btoa(X_Reference_Id + ":" + ApiKey)

        },
    };

    var token='';
    return $.ajax(settings);

    //      .done(function (response) {
    //     console.log(response.access_token);
    //
    //     token=response.access_token;
    //     return token;
    // });

    // sleep(2000);

    // setTimeout(function() {
    //
    //     alert("bassae");
    //
    // }, 5000);


}



// Request to pay

function requestTopay(Ocp_Apim_Subscription_Key ,token,id_ref, amount, currency="EUR", phoneNnumber) {
    console.log("demande de payement");

  var proxy = 'https://cors-anywhere.herokuapp.com/';

    //  var proxy = '';

    // "Authorization": "Bearer "+ token,

    var externalIdf= getIdcode(7);
    var settings = {
        "url": proxy + "https://sandbox.momodeveloper.mtn.com/collection/v1_0/requesttopay",
        "method": "POST",
        "timeout": 0,
        "headers": {
            "X-Reference-Id": id_ref ,
            "X-Target-Environment": "sandbox",
            "Ocp-Apim-Subscription-Key":Ocp_Apim_Subscription_Key,
            "Content-Type": "application/json",
            "Authorization": "Bearer "+token,
        },
        "data": JSON.stringify( {
            "amount": amount,
            "currency": "EUR",
            "externalId": externalIdf,
            "payer": {
                "partyIdType": "MSISDN",
                "partyId": phoneNnumber
            },
            "payerMessage": "Pay sddsfor product a",
            "payeeNote": "payer note"
        }),
    };




    return $.ajax(settings);

    //     .done(function (response) {
    //     console.log(response);
    // });

}




// Request to pay

function requestTopayStatus(Ocp_Apim_Subscription_Key ,token, IdRef) {
    console.log("demande de payement");

    var proxy = 'https://cors-anywhere.herokuapp.com/';

   //  var proxy = '';

    // "Authorization": "Bearer "+ token,
    var id_ref=getUUcode();
    var externalIdf= getIdcode(7);
    var settings = {
        "url": proxy + "https://sandbox.momodeveloper.mtn.com/collection/v1_0/requesttopay/"+ IdRef,
        "method": "GET",
        "timeout": 0,
        "headers": {
            "X-Reference-Id": id_ref ,
            "X-Target-Environment": "sandbox",
            "Ocp-Apim-Subscription-Key":Ocp_Apim_Subscription_Key,
            "Content-Type": "application/json",
            "Authorization": "Bearer "+token,
        }
    };


    return $.ajax(settings);


}




// funtion for delay in seconds
function sleep(milliseconds) {
    var start = new Date().getTime();
    for (var i = 0; i < 1e7; i++) {
        if ((new Date().getTime() - start) > milliseconds){
            break;
        }
    }
}

//genere un UUID identifier
function getUUcode() {

    var dt = new Date().getTime();
    var uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c)
    {
        var r = (dt + Math.random() * 16) % 16 | 0;
        dt = Math.floor(dt / 16);
        return (c == 'x' ? r : (r & 0x3 | 0x8)).toString(16);
    });
    console.log(uuid);
    return uuid;

}


//genere un  ID simple
function getIdcode(length) {

    // const characters ='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    const characters ='0123456789';
    let result = '';
    const charactersLength = characters.length;
    for ( let i = 0; i < length; i++ )
    {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }

    console.log(result );
    return result;
}
