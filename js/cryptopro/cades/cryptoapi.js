function CreateSignatureCadesBES_AsyncFile(certListBoxId, detached) {
    cadesplugin.async_spawn(function*(arg) {
        var e = document.getElementById(arg[0]);
        var selectedCertID = e.selectedIndex;
        if (selectedCertID == -1) {
            alert("Выберите сертификат");
            return;
        }
        var certificate = global_selectbox_container[selectedCertID];
        var SignatureFieldTitle = document.getElementsByName("SignatureTitle");
        var Signature;
        try
        {
            //FillCertInfo_Async(certificate);
            var errormes = "";
            try {
                var oSigner = yield cadesplugin.CreateObjectAsync("CAdESCOM.CPSigner");
            } catch (err) {
                errormes = "Failed to create CAdESCOM.CPSigner: " + err.number;
                throw errormes;
            }
            var oSigningTimeAttr = yield cadesplugin.CreateObjectAsync("CADESCOM.CPAttribute");

            var CAPICOM_AUTHENTICATED_ATTRIBUTE_SIGNING_TIME = 0;
            yield oSigningTimeAttr.propset_Name(CAPICOM_AUTHENTICATED_ATTRIBUTE_SIGNING_TIME);
            var oTimeNow = new Date();
            yield oSigningTimeAttr.propset_Value(oTimeNow);
            var attr = yield oSigner.AuthenticatedAttributes2;
            yield attr.Add(oSigningTimeAttr);


            /*
			Добавить в подписываемые атрибуты имя документа:
			var oDocumentNameAttr = yield cadesplugin.CreateObjectAsync("CADESCOM.CPAttribute");
            var CADESCOM_AUTHENTICATED_ATTRIBUTE_DOCUMENT_NAME = 1;
            yield oDocumentNameAttr.propset_Name(CADESCOM_AUTHENTICATED_ATTRIBUTE_DOCUMENT_NAME);
            yield oDocumentNameAttr.propset_Value("Document Name");
            yield attr.Add(oDocumentNameAttr);*/

            if (oSigner) {
                yield oSigner.propset_Certificate(certificate);
            }
            else {
                errormes = "Failed to create CAdESCOM.CPSigner";
                throw errormes;
            }

            var oSignedData = yield cadesplugin.CreateObjectAsync("CAdESCOM.CadesSignedData");
            var CADES_BES = 1;

            var dataToSign = fileContent; // fileContent - объявлен в Code.js
            if (dataToSign) {
                // Данные на подпись ввели
                yield oSignedData.propset_ContentEncoding(1); //CADESCOM_BASE64_TO_BINARY
                yield oSignedData.propset_Content(dataToSign);
                yield oSigner.propset_Options(1); //CAPICOM_CERTIFICATE_INCLUDE_WHOLE_CHAIN
                try {
                    var StartTime = Date.now();
                    Signature = yield oSignedData.SignCades(oSigner, CADES_BES, detached);
                    var EndTime = Date.now();
                    document.getElementsByName('TimeTitle')[0].innerHTML = "Время выполнения: " + (EndTime - StartTime) + " мс";
                }
                catch (err) {
                    errormes = "Не удалось создать подпись из-за ошибки: " + cadesplugin.getLastError(err);
                    throw errormes;
                }
            }
            document.getElementById("SignatureTxtBox").innerHTML = Signature;
            SignatureFieldTitle[0].innerHTML = "Подпись сформирована успешно:";
        }
        catch(err)
        {
            SignatureFieldTitle[0].innerHTML = "Возникла ошибка:";
            document.getElementById("SignatureTxtBox").innerHTML = err;
        }
    }, certListBoxId); //cadesplugin.async_spawn
    }


function CreateSignatureCadesBES_NPAPI_File(certListBoxId, detached) {
    var certificate = GetCertificate_NPAPI(certListBoxId);
    var dataToSign = fileContent;
    var x = GetSignatureTitleElement();
    try {
        var StartTime = Date.now();
        var setDisplayData;
        var signature = MakeCadesBesSign_NPAPI(dataToSign, certificate, setDisplayData, 1,detached);
        var EndTime = Date.now();
        document.getElementsByName('TimeTitle')[0].innerHTML = "Время выполнения: " + (EndTime - StartTime) + " мс";
        document.getElementById("SignatureTxtBox").innerHTML = signature;
        if (x != null) {
            x.innerHTML = "Подпись сформирована успешно:";
        }
    }
    catch (err) {
        if (x != null) {
            x.innerHTML = "Возникла ошибка:";
        }
        document.getElementById("SignatureTxtBox").innerHTML = err;
    }
}

function SignCadesBES_File(id,detached) {
    var canAsync = !!cadesplugin.CreateObjectAsync;
    if (canAsync) {
        include_async_code().then(function () {
            return CreateSignatureCadesBES_AsyncFile(id,detached);
        });
    } else {
        return CreateSignatureCadesBES_NPAPI_File(id,detached);
    }
}



function CreateSignatureCadesBES_Async(certListBoxId,detached, data, setDisplayData) {
    cadesplugin.async_spawn(function*(arg) {
        var e = document.getElementById(arg[0]);
        var selectedCertID = e.selectedIndex;
        if (selectedCertID == -1) {
            alert("Выберите сертификат");
            return;
        }

        var certificate = global_selectbox_container[selectedCertID];

        var dataToSign = document.getElementById("DataToSignTxtBox").value;
		 
 		
		var DataInBase64 = document.getElementById("DataInBase64");
		var NeedEncode = true;
		 if (DataInBase64)
		 {
			 if (DataInBase64.checked)
			  {				  
		        NeedEncode =false;
		      }   
		 }
		
		if (NeedEncode)
		{
         if(typeof(data) != 'undefined')
         {
            dataToSign = Base64.encode(data);
         }else {
            dataToSign = Base64.encode(dataToSign);
         }
		}
		
        var SignatureFieldTitle = document.getElementsByName("SignatureTitle");
        var Signature;
        try
        {
console.log("step1");
            //FillCertInfo_Async(certificate);
            var errormes = "";
            try {
                var oSigner = yield cadesplugin.CreateObjectAsync("CAdESCOM.CPSigner");
            } catch (err) {
                errormes = "Failed to create CAdESCOM.CPSigner: " + err.number;
                throw errormes;
            }
            var oSigningTimeAttr = yield cadesplugin.CreateObjectAsync("CADESCOM.CPAttribute");

            yield oSigningTimeAttr.propset_Name(cadesplugin.CAPICOM_AUTHENTICATED_ATTRIBUTE_SIGNING_TIME);
            var oTimeNow = new Date();
            yield oSigningTimeAttr.propset_Value(oTimeNow);
            var attr = yield oSigner.AuthenticatedAttributes2;
            yield attr.Add(oSigningTimeAttr);


            /*
			var oDocumentNameAttr = yield cadesplugin.CreateObjectAsync("CADESCOM.CPAttribute");
            yield oDocumentNameAttr.propset_Name(cadesplugin.CADESCOM_AUTHENTICATED_ATTRIBUTE_DOCUMENT_NAME);
            yield oDocumentNameAttr.propset_Value("Document Name");
            yield attr.Add(oDocumentNameAttr);*/

console.log("step2");
            if (oSigner) {
                yield oSigner.propset_Certificate(certificate);
            }
            else {
                errormes = "Failed to create CAdESCOM.CPSigner";
                throw errormes;
            }

console.log("step3");
            var oSignedData = yield cadesplugin.CreateObjectAsync("CAdESCOM.CadesSignedData");
            if (dataToSign) {
                // Данные на подпись ввели
                yield oSigner.propset_Options(cadesplugin.CAPICOM_CERTIFICATE_INCLUDE_WHOLE_CHAIN);
                yield oSignedData.propset_ContentEncoding(cadesplugin.CADESCOM_BASE64_TO_BINARY); //
                if(typeof(setDisplayData) != 'undefined')
                {
                    //Set display data flag flag for devices like Rutoken PinPad
                    yield oSignedData.propset_DisplayData(1);
                }
				
console.log("step4");
                yield oSignedData.propset_Content(dataToSign);

                try {
                    Signature = yield oSignedData.SignCades(oSigner, cadesplugin.CADESCOM_CADES_BES, detached);
                }
                catch (err) {
                    errormes = "Не удалось создать подпись из-за ошибки: " + cadesplugin.getLastError(err);
                    throw errormes;
                }
            }
console.log("step5");
            document.getElementById("SignatureTxtBox").innerHTML = Signature;
            SignatureFieldTitle[0].innerHTML = "Подпись сформирована успешно:";
console.log("SaveSign");
			SaveSign(Signature);
        }
        catch(err)
        {
            SignatureFieldTitle[0].innerHTML = "Возникла ошибка:";
            document.getElementById("SignatureTxtBox").innerHTML = err;
        }
    }, certListBoxId); //cadesplugin.async_spawn
}

function CreateSignatureCadesBES_NPAPI(certListBoxId, detached, data, setDisplayData) {
	
        document.getElementById("SignatureTxtBox").innerHTML ="step1";
    var certificate = GetCertificate_NPAPI(certListBoxId);
        document.getElementById("SignatureTxtBox").innerHTML ="step2";
    var dataToSign = document.getElementById("DataToSignTxtBox").value;
    if(typeof(data) != 'undefined')
    {
        dataToSign = data;
    }
    var x = GetSignatureTitleElement();
        document.getElementById("SignatureTxtBox").innerHTML ="step3";
    try
    {
        var signature = MakeCadesBesSign_NPAPI(dataToSign, certificate, setDisplayData,detached);
        document.getElementById("SignatureTxtBox").innerHTML = signature;
        if(x!=null)
        {
            x.innerHTML = "Подпись сформирована успешно:";
        }
    }
    catch(err)
    {
        if(x!=null)
        {
            x.innerHTML = "Возникла ошибка:";
        }
        document.getElementById("SignatureTxtBox").innerHTML = err;
    }
}


function SignCadesBES(id, detached, text, setDisplayData)
{
    var canAsync = !!cadesplugin.CreateObjectAsync;
    if(canAsync)
    {
		console.log('SignCadesBES, detached='+detached);
        include_async_code().then(function(){
            return CreateSignatureCadesBES_Async(id, detached, text, setDisplayData);
        });
    }else
    {
        return CreateSignatureCadesBES_NPAPI(id, detached, text, setDisplayData);
    }
}
