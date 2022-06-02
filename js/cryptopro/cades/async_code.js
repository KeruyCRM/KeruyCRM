function CertificateAdjuster()
{
}

CertificateAdjuster.prototype.extract = function(from, what){
  var FieldValue="";
	
	 var FieldPos = from.indexOf(what);
        if (FieldPos >= 0) {
            var FieldValue = from.substring(FieldPos + what.length, from.length);
            var CommaPos = FieldValue.indexOf(",");
            if (CommaPos >= 0) {
                return FieldValue.substring(0, CommaPos);
            }
		}
    return FieldValue;
			 
	/*		
    certName = "";
    var begin = from.indexOf(what);

    if(begin>=0)
    {
        var end = from.indexOf(', ', begin);
        certName = (end<0) ? from.substr(begin) : from.substr(begin+what.length, end - begin-1);
    }

    return certName;*/
}

CertificateAdjuster.prototype.Print2Digit = function(digit)
{
    return (digit<10) ? "0"+digit : digit;
}

CertificateAdjuster.prototype.GetCertDate = function(paramDate)
{
    var certDate = new Date(paramDate);
    return this.Print2Digit(certDate.getUTCDate())+"."+this.Print2Digit(certDate.getUTCMonth()+1)+"."+certDate.getFullYear() 
	       + " " +
           this.Print2Digit(certDate.getUTCHours()) + ":" + this.Print2Digit(certDate.getUTCMinutes()) + ":" + this.Print2Digit(certDate.getUTCSeconds());
}

CertificateAdjuster.prototype.GetCertName = function(certSubjectName)
{
    return this.extract(certSubjectName, 'CN=');
}

CertificateAdjuster.prototype.GetIssuer = function(certIssuerName)
{
    return this.extract(certIssuerName, 'CN=');
}

CertificateAdjuster.prototype.GetCertInfoString = function(certSubjectName, certFromDate, CertToDate)
{
      //this.GetCertDate(certFromDate) +" - " +
	return this.GetCertDate(CertToDate)+" "+
	this.extract(certSubjectName,'CN=')+" "+ 
    this.extract(certSubjectName,'SN=')	+" "+ 
    this.extract(certSubjectName,'G=')+" ИНН="+ 
    this.extract(certSubjectName,'ИНН=')+
    this.extract(certSubjectName,'INN=');
}

function CheckForPlugIn_Async() {
    function VersionCompare_Async(StringVersion, ObjectVersion)
    {
        if(typeof(ObjectVersion) == "string")
            return -1;
        var arr = StringVersion.split('.');
        var isActualVersion = true;

        cadesplugin.async_spawn(function *() {
            if((yield ObjectVersion.MajorVersion) == parseInt(arr[0]))
            {
                if((yield ObjectVersion.MinorVersion) == parseInt(arr[1]))
                {
                    if((yield ObjectVersion.BuildVersion) == parseInt(arr[2]))
                    {
                        isActualVersion = true;
                    }
                    else if((yield ObjectVersion.BuildVersion) < parseInt(arr[2]))
                    {
                        isActualVersion = false;
                    }
                }else if((yield ObjectVersion.MinorVersion) < parseInt(arr[1]))
                {
                    isActualVersion = false;
                }
            }else if((yield ObjectVersion.MajorVersion) < parseInt(arr[0]))
            {
                isActualVersion = false;
            }

            if(!isActualVersion)
            {
                document.getElementById('PluginEnabledImg').setAttribute("src", "images/yellow_dot.png");
                document.getElementById('PlugInEnabledTxt').innerHTML = "Плагин загружен, но есть более <a target=_blank href=https://www.cryptopro.ru/products/cades/plugin/get_2_0>свежая версия.</a>";
            }
            document.getElementById('PlugInVersionTxt').innerHTML = "Версия плагина: " + (yield CurrentPluginVersion.toString());
            var oAbout = yield cadesplugin.CreateObjectAsync("CAdESCOM.About");
            var ver = yield oAbout.CSPVersion("", 80);//75
            var ret = (yield ver.MajorVersion) + "." + (yield ver.MinorVersion) + "." + (yield ver.BuildVersion);
            document.getElementById('CSPVersionTxt').innerHTML = "Версия криптопровайдера: " + ret;

            try
            {
                var sCSPName = yield oAbout.CSPName(80);//75
                document.getElementById('CSPNameTxt').innerHTML = "Криптопровайдер: " + sCSPName;
            }
            catch(err){}
            return;
        });
    }

    function GetLatestVersion_Async(CurrentPluginVersion) {
        var xmlhttp = getXmlHttp();
        xmlhttp.open("GET", "js/cryptopro/cades/plugin.txt", true);
        xmlhttp.onreadystatechange = function() {
        var PluginBaseVersion;
            if (xmlhttp.readyState == 4) {
                if(xmlhttp.status == 200) {
                    PluginBaseVersion = xmlhttp.responseText;
                    VersionCompare_Async(PluginBaseVersion, CurrentPluginVersion)
                }
            }
        }
        xmlhttp.send(null);
    }

    document.getElementById('PluginEnabledImg').setAttribute("src", "images/green_dot.png");
    document.getElementById('PlugInEnabledTxt').innerHTML = "Плагин загружен";
    var CurrentPluginVersion;
    cadesplugin.async_spawn(function *() {
        var oAbout = yield cadesplugin.CreateObjectAsync("CAdESCOM.About");
        CurrentPluginVersion = yield oAbout.PluginVersion;
        GetLatestVersion_Async(CurrentPluginVersion);
        if(location.pathname.indexOf("symalgo_sample.html")>=0){
            FillCertList_Async('CertListBox1');
            FillCertList_Async('CertListBox2');
        }else {
            FillCertList_Async('CertListBox',''); // todo 20190628 передать отпечатки для фильтрации
            FillCertList_Async('CertificatesBox','');  
			}
        // var txtDataToSign = "Hello World";
        // document.getElementById("DataToSignTxtBox").innerHTML = txtDataToSign;
        // document.getElementById("SignatureTxtBox").innerHTML = "";
    }); //cadesplugin.async_spawn
}

function onCertificateSelected(event) {
    cadesplugin.async_spawn(function *(args) {
        var selectedCertID = args[0][args[0].selectedIndex].value;
        var certificate = global_selectbox_container[selectedCertID];
        FillCertInfo_Async(certificate, event.target.boxId, global_isFromCont[selectedCertID]);
    }, event.target);//cadesplugin.async_spawn
}

function FillCertList_Async(lstId, TrustedCerts) {
    cadesplugin.async_spawn(function *() {
        var MyStoreExists = true;
        try {
            var oStore = yield cadesplugin.CreateObjectAsync("CAdESCOM.Store");
            if (!oStore) {
                alert("Create store failed");
                return;
            }

            yield oStore.Open();
        }
        catch (ex) {
            MyStoreExists = false;
        }

        var lst = document.getElementById(lstId);
        if(!lst)
        {
            return;
        }
        lst.onchange = onCertificateSelected;
        lst.onclick = onCertificateSelected;
        lst.onclose = onCertificateSelected;
        lst.onselect = onCertificateSelected;
        lst.boxId = lstId;

        var certCnt;
        var certs;
        if (MyStoreExists) {
            try {
                certs = yield oStore.Certificates;
                certCnt = yield certs.Count;
            }
            catch (ex) {
                alert("Ошибка при получении Certificates или Count: " + cadesplugin.getLastError(ex));
                return;
            }
			//todo 20190628
			var today = new Date();
			
            for (var i = 1; i <= certCnt; i++) {
                var cert;
                try {
                    cert = yield certs.Item(i);
                }
                catch (ex) {
                    alert("Ошибка при перечислении сертификатов: " + cadesplugin.getLastError(ex));
                    return;
                }
				 
				
				var Thumbprint =yield cert.Thumbprint;
				
			    if (today < new Date((yield cert.ValidToDate)))
				 //todo 20190628
				{
				 // console.log("поиск отпечатка: "+Thumbprint);
				 // console.log("размер известных отпечатков: "+TrustedCerts.length);
			      var Z = TrustedCerts.indexOf(Thumbprint);	 
				  if (TrustedCerts.length==0) {Z=1;}
				  
				  
			
				var INN_En = new CertificateAdjuster().extract(yield cert.SubjectName,'INN=');
				var INN_Ru = new CertificateAdjuster().extract(yield cert.SubjectName,'ИНН=');
				
				if ((INN_En + INN_Ru).length==0) {Z=0;}
				
				if (Z>0)
				if(Thumbprint==$('#user_certificate').val())
				//if(Thumbprint)
				{ 
                var oOpt = document.createElement("OPTION");
                var dateObj = new Date();
                try {
                    var ValidToDate = new Date((yield cert.ValidToDate));  // 
                    var ValidFromDate = new Date((yield cert.ValidFromDate));  // 
					
                    oOpt.text ='' +//'№ '+(global_selectbox_counter+1)+')   '+   
					new CertificateAdjuster().GetCertInfoString(yield cert.SubjectName, ValidFromDate, ValidToDate);
                }
                catch (ex) {
                    alert("Ошибка при получении свойства SubjectName: " + cadesplugin.getLastError(ex));
                }
                try {
                    //oOpt.value = yield cert.Thumbprint;
                    oOpt.value = global_selectbox_counter
                    global_selectbox_container.push(cert);
                    global_isFromCont.push(false);
                    global_selectbox_counter++;
                }
                catch (ex) {
                    alert("Ошибка при получении свойства Thumbprint: " + cadesplugin.getLastError(ex));
                }

                lst.options.add(oOpt);
				 //todo 20190628
				 } // проверка на известный отпечаток
			   } // проверка на срок действия
            }
            
            //выбираем сертификат            
            if(lst.length>0)
            {
            	$('#'+lstId).val(0).trigger('change');
            	$('#sign_process_btn').show();
            }
            else
            {
            	$('#sign_process_error').html('<div class="alert alert-danger">Ошибка: невозможно выбрать сертификат. Обновите ваш сертификат в Личном кабинете.</div>');
            }
            
            yield oStore.Close();
        } 
	    
		return; //todo 20190628

        //В версии плагина 2.0.13292+ есть возможность получить сертификаты из 
        //закрытых ключей и не установленных в хранилище
        try {
            yield oStore.Open(cadesplugin.CADESCOM_CONTAINER_STORE);
            certs = yield oStore.Certificates;
            certCnt = yield certs.Count;
            for (var i = 1; i <= certCnt; i++) {
                var cert = yield certs.Item(i);
				
				
				 if (today > new Date((yield cert.ValidToDate))) 
                     continue;
				
                //Проверяем не добавляли ли мы такой сертификат уже?
                var found = false;
                for (var j = 0; j < global_selectbox_container.length; j++)
                {
                    if ((yield global_selectbox_container[j].Thumbprint) === (yield cert.Thumbprint))
                    {
                        found = true;
                        break;
                    }
                }
                if(found)
                    continue;
				
					 
                var oOpt = document.createElement("OPTION");
                var ValidFromDate = new Date((yield cert.ValidFromDate));
                var ValidToDate = new Date((yield cert.ValidToDate));
                oOpt.text ='# '+global_selectbox_counter+'   '+  new CertificateAdjuster().GetCertInfoString(yield cert.SubjectName, ValidFromDate, ValidToDate);
				
				if (today > new Date((yield cert.ValidToDate))) 
				{
					oOpt.text  = oOpt.text +' просрочен';					
				}
				
                oOpt.value = global_selectbox_counter;
                global_selectbox_container.push(cert);
                global_isFromCont.push(true);
                global_selectbox_counter++;
                lst.options.add(oOpt);
            }
            yield oStore.Close();

        }
        catch (ex) {
        }
        if(global_selectbox_container.length == 0) {
            document.getElementById("boxdiv").style.display = '';
        } 
    });//cadesplugin.async_spawn
}

function CreateSimpleSign_Async() {
    cadesplugin.async_spawn(function*(arg) {
        try {
            var oStore = yield cadesplugin.CreateObjectAsync("CAdESCOM.Store");
            yield oStore.Open();
        } catch (err) {
            alert('Certificate not found');
            return;
        }
        var all_certs = yield oStore.Certificates;

        if ((yield all_certs.Count) == 0) {
            document.getElementById("boxdiv").style.display = '';
            return;
        }

        var cert;
        var found = 0;
        for (var i = 1; i <= (yield all_certs.Count); i++) {
            try {
                cert = yield all_certs.Item(i);
            }
            catch (ex) {
                alert("Ошибка при перечислении сертификатов: " + cadesplugin.getLastError(ex));
                return;
            }

            var dateObj = new Date();
            try {
                var certDate = new Date((yield cert.ValidToDate));
                var Validator = yield cert.IsValid();
                var IsValid = yield Validator.Result;
                if(dateObj< certDate && (yield cert.HasPrivateKey()) && IsValid) {
                    found = 1;
                    break;
                }
                else {
                    continue;
                }
            }
            catch (ex) {
                alert("Ошибка при получении свойства SubjectName: " + cadesplugin.getLastError(ex));
            }
        }

        if (found == 0) {
            document.getElementById("boxdiv").style.display = '';
            return;
        }

        var dataToSign = document.getElementById("DataToSignTxtBox").value;
        var SignatureFieldTitle = document.getElementsByName("SignatureTitle");
        var Signature;
        try
        {
            FillCertInfo_Async(cert);
            var errormes = "";
            try {
                var oSigner = yield cadesplugin.CreateObjectAsync("CAdESCOM.CPSigner");
            } catch (err) {
                errormes = "Failed to create CAdESCOM.CPSigner: " + err.number;
                throw errormes;
            }
            if (oSigner) {
                yield oSigner.propset_Certificate(cert);
            }
            else {
                errormes = "Failed to create CAdESCOM.CPSigner";
                throw errormes;
            }

            var oSignedData = yield cadesplugin.CreateObjectAsync("CAdESCOM.CadesSignedData");
            var CADES_BES = 1;

            if (dataToSign) {
                // Данные на подпись ввели
                yield oSignedData.propset_Content(dataToSign);
                yield oSigner.propset_Options(1); //CAPICOM_CERTIFICATE_INCLUDE_WHOLE_CHAIN
                try {
                    Signature = yield oSignedData.SignCades(oSigner, CADES_BES);
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
    }); //cadesplugin.async_spawn
}


function SignCadesBES_Async(certListBoxId, data, setDisplayData) {
    cadesplugin.async_spawn(function*(arg) {
        var e = document.getElementById(arg[0]);
        var selectedCertID = e.selectedIndex;
        if (selectedCertID == -1) {
            alert("Выберите сертификат");
            return;
        }

        var certificate = global_selectbox_container[selectedCertID];

        var dataToSign = document.getElementById("DataToSignTxtBox").value;
		var detached=false;
		var chkDetached = document.getElementById("UseDetached");
		   if (chkDetached)
		   {
			  if (chkDetached.checked)
			  {				  
		        detached =true;
		      }    
		   }
		
 		
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
		
		
		$('#sign_process_btn').hide();
		$('#sign_process_btn_loading').css('visibility','visible')
		
        var SignatureFieldTitle = document.getElementsByName("SignatureTitle");
        var Signature;
        try
        {
        	
//console.log("step1");
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

//console.log("step2");
            if (oSigner) {
                yield oSigner.propset_Certificate(certificate);
            }
            else {
                errormes = "Failed to create CAdESCOM.CPSigner";
                throw errormes;
            }

//console.log("step3");
            var oSignedData = yield cadesplugin.CreateObjectAsync("CAdESCOM.CadesSignedData");
            
            dataToSignArray = [];
            signatureContainerArray = [];
            $('.data-to-sign').each(function(){            	 
            	dataToSignArray.push($(this).val());
            	signatureContainerArray.push($(this).attr('signature_container'))
            })
            
            //console.log(dataToSignArray)
            //console.log(signatureContainerArray)
            
            for(i=0;i<dataToSignArray.length;i++)
            {
            	dataToSign = dataToSignArray[i];
            	            	            	
	            if (dataToSign) 
	            {
	                // Данные на подпись ввели
	                yield oSigner.propset_Options(cadesplugin.CAPICOM_CERTIFICATE_INCLUDE_WHOLE_CHAIN);
	                yield oSignedData.propset_ContentEncoding(cadesplugin.CADESCOM_BASE64_TO_BINARY); //
	                if(typeof(setDisplayData) != 'undefined')
	                {
	                    //Set display data flag flag for devices like Rutoken PinPad
	                    yield oSignedData.propset_DisplayData(1);
	                }
					
//console.log("step4");
	                yield oSignedData.propset_Content(dataToSign);
	
	                try {
	                    Signature = yield oSignedData.SignCades(oSigner, cadesplugin.CADESCOM_CADES_BES, detached);
	                }
	                catch (err) {
	                    errormes = "Не удалось создать подпись из-за ошибки: " + cadesplugin.getLastError(err);
	                    throw errormes;
	                }
	            }
	            
	            $('#'+signatureContainerArray[i]).val(Signature)
            }
            
/*            
console.log("step5");
            document.getElementById("SignatureTxtBox").innerHTML = Signature;
            SignatureFieldTitle[0].innerHTML = "Подпись сформирована успешно:";
			console.log("SaveSign"); 
			SaveSign(Signature);
*/			
			
        }
        catch(err)
        {
            //SignatureFieldTitle[0].innerHTML = "Возникла ошибка:";
            //document.getElementById("SignatureTxtBox").innerHTML = err;
        	
        	$('#sign_process_error').html('<div class="alert alert-danger">Возникла ошибка: '+err+'</div>')
        	
        	$('#sign_process_btn').show();
        	$('#sign_process_btn_loading').css('visibility','hidden')
		
            return;
        }
        
        
        //submit sign form
        $('#signature_form').submit();
        
    }, certListBoxId); //cadesplugin.async_spawn
}

function SignCadesBES_Async_File(certListBoxId) {
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
		
		var detached=false;
		var chkDetached = document.getElementById("UseDetached");
		   if (chkDetached)
		   {
			  if (chkDetached.checked)
			  {				  
		        detached =true;
		      }    
		   }
		   
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
                    Signature = yield oSignedData.SignCades(oSigner, CADES_BES,detached);
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
			SaveSign(Signature);
        }
        catch(err)
        {
            SignatureFieldTitle[0].innerHTML = "Возникла ошибка:";
            document.getElementById("SignatureTxtBox").innerHTML = err;
        }
    }, certListBoxId); //cadesplugin.async_spawn
    }

function SignCadesEnhanced_Async(certListBoxId, sign_type) {
    cadesplugin.async_spawn(function*(arg) {
        var e = document.getElementById(arg[0]);
        var selectedCertID = e.selectedIndex;
        if (selectedCertID == -1) {
            alert("Выберите сертификат");
            return;
        }
		var detached=false;
		var chkDetached = document.getElementById("UseDetached");
		   if (chkDetached)
		   {
			  if (chkDetached.checked)
			  {				  
		        detached =true;
		      }    
		   }
        var certificate = global_selectbox_container[selectedCertID];

        var dataToSign = document.getElementById("DataToSignTxtBox").value;
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
            if (oSigner) {
                yield oSigner.propset_Certificate(certificate);
            }
            else {
                errormes = "Failed to create CAdESCOM.CPSigner";
                throw errormes;
            }

            var oSignedData = yield cadesplugin.CreateObjectAsync("CAdESCOM.CadesSignedData");
            var tspService = document.getElementById("TSPServiceTxtBox").value ;

            if (dataToSign) {
                // Данные на подпись ввели
                yield oSignedData.propset_Content(dataToSign);
                yield oSigner.propset_Options(1); //CAPICOM_CERTIFICATE_INCLUDE_WHOLE_CHAIN
                yield oSigner.propset_TSAAddress(tspService);
                try {
                    Signature = yield oSignedData.SignCades(oSigner, sign_type, detached);
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

function SignCadesXML_Async(certListBoxId) {
    cadesplugin.async_spawn(function*(arg) {
        var e = document.getElementById(arg[0]);
        var selectedCertID = e.selectedIndex;
        if (selectedCertID == -1) {
            alert("Выберите сертификат");
            return;
        }

        var certificate = global_selectbox_container[selectedCertID];

        var dataToSign = document.getElementById("DataToSignTxtBox").value;
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
            if (oSigner) {
                yield oSigner.propset_Certificate(certificate);
            }
            else {
                errormes = "Failed to create CAdESCOM.CPSigner";
                throw errormes;
            }

            var oSignedXML = yield cadesplugin.CreateObjectAsync("CAdESCOM.SignedXML");

            var signMethod = "";
            var digestMethod = "";

            var pubKey = yield certificate.PublicKey();
            var algo = yield pubKey.Algorithm;
            var algoOid = yield algo.Value;
            if (algoOid == "1.2.643.7.1.1.1.1") {   // алгоритм подписи ГОСТ Р 34.10-2012 с ключом 256 бит
                signMethod = "urn:ietf:params:xml:ns:cpxmlsec:algorithms:gostr34102012-gostr34112012-256";
                digestMethod = "urn:ietf:params:xml:ns:cpxmlsec:algorithms:gostr34112012-256";
            }
            else if (algoOid == "1.2.643.7.1.1.1.2") {   // алгоритм подписи ГОСТ Р 34.10-2012 с ключом 512 бит
                signMethod = "urn:ietf:params:xml:ns:cpxmlsec:algorithms:gostr34102012-gostr34112012-512";
                digestMethod = "urn:ietf:params:xml:ns:cpxmlsec:algorithms:gostr34112012-512";
            }
            else if (algoOid == "1.2.643.2.2.19") {  // алгоритм ГОСТ Р 34.10-2001
                signMethod = "urn:ietf:params:xml:ns:cpxmlsec:algorithms:gostr34102001-gostr3411";
                digestMethod = "urn:ietf:params:xml:ns:cpxmlsec:algorithms:gostr3411";
            }
            else {
                errormes = "Данная демо страница поддерживает XML подпись сертификатами с алгоритмом ГОСТ Р 34.10-2012, ГОСТ Р 34.10-2001";
                throw errormes;
            }
            
            var CADESCOM_XML_SIGNATURE_TYPE_ENVELOPED = 0;

            if (dataToSign) {
                // Данные на подпись ввели
                yield oSignedXML.propset_Content(dataToSign);
                yield oSignedXML.propset_SignatureType(CADESCOM_XML_SIGNATURE_TYPE_ENVELOPED);
                yield oSignedXML.propset_SignatureMethod(signMethod);
                yield oSignedXML.propset_DigestMethod(digestMethod);

                try {
                    Signature = yield oSignedXML.Sign(oSigner);
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

function FillCertInfo_Async(certificate, certBoxId, isFromContainer)
{
    var BoxId;
    var field_prefix;
    if(typeof(certBoxId) == 'undefined' || certBoxId == "CertListBox")
    {
        BoxId = 'cert_info';
        field_prefix = '';
    }else if (certBoxId == "CertListBox1") {
        BoxId = 'cert_info1';
        field_prefix = 'cert_info1';
    } else if (certBoxId == "CertListBox2") {
        BoxId = 'cert_info2';
        field_prefix = 'cert_info2';
    } else {
        BoxId = certBoxId;
        field_prefix = certBoxId;
    }
    cadesplugin.async_spawn (function*(args) {
        var Adjust = new CertificateAdjuster();
		
		var thumbprint = yield args[0].Thumbprint;

        var ValidToDate = new Date((yield args[0].ValidToDate));
        var ValidFromDate = new Date((yield args[0].ValidFromDate));
        var Validator;
        var IsValid = false;
        //если попадется сертификат с неизвестным алгоритмом
        //тут будет исключение. В таком сертификате просто пропускаем такое поле
        try {
            Validator = yield args[0].IsValid();
            IsValid = yield Validator.Result;
        } catch(e) {

        }
        var hasPrivateKey = yield args[0].HasPrivateKey();
        var Now = new Date();

        document.getElementById(args[1]).style.display = '';
        document.getElementById(args[2] + "subject").innerHTML = "Владелец: <b>" + Adjust.GetCertName(yield args[0].SubjectName) + "<b>";
        document.getElementById(args[2] + "issuer").innerHTML = "Издатель: <b>" + Adjust.GetIssuer(yield args[0].IssuerName) + "<b>";
        document.getElementById(args[2] + "from").innerHTML = "Выдан: <b>" + Adjust.GetCertDate(ValidFromDate) + " UTC<b>";
        document.getElementById(args[2] + "till").innerHTML = "Действителен до: <b>" + Adjust.GetCertDate(ValidToDate) + " UTC<b>";
        var pubKey = yield args[0].PublicKey();
        var algo = yield pubKey.Algorithm;
        var fAlgoName = yield algo.FriendlyName;
        document.getElementById(args[2] + "algorithm").innerHTML = "Алгоритм ключа: <b>" + fAlgoName + "<b>";
        if( hasPrivateKey ) {
            var oPrivateKey = yield args[0].PrivateKey;
            var sProviderName = yield oPrivateKey.ProviderName;
            document.getElementById(args[2] + "provname").innerHTML = "Криптопровайдер: <b>" + sProviderName + "<b>";
        }
        if(Now < ValidFromDate) {
            document.getElementById(args[2] + "status").innerHTML = "Статус: <span style=\"color:red; font-weight:bold; font-size:16px\"><b>Срок действия не наступил</b></span>";
        } else if( Now > ValidToDate){
            document.getElementById(args[2] + "status").innerHTML = "Статус: <span style=\"color:red; font-weight:bold; font-size:16px\"><b>Срок действия истек</b></span>";
        } else if( !hasPrivateKey ){
            document.getElementById(args[2] + "status").innerHTML = "Статус: <span style=\"color:red; font-weight:bold; font-size:16px\"><b>Нет привязки к закрытому ключу</b></span>";
        } else if( !IsValid ){
            document.getElementById(args[2] + "status").innerHTML = "Статус: <span style=\"color:red; font-weight:bold; font-size:16px\"><b>Ошибка при проверке цепочки сертификатов</b></span>";         
        } else {
            document.getElementById(args[2] + "status").innerHTML = "Статус: <b> Действителен<b>";
        }

        if(args[3])
        {
            document.getElementById(field_prefix + "location").innerHTML = "Установлен в хранилище: <b>Нет</b>";            
        } else {
            document.getElementById(field_prefix + "location").innerHTML = "Установлен в хранилище: <b>Да</b>";
        }
		
		 document.getElementById("thumbprint").value = thumbprint; 
		

    }, certificate, BoxId, field_prefix, isFromContainer);//cadesplugin.async_spawn
}

function Encrypt_Async() {
    cadesplugin.async_spawn (function*() {
        document.getElementById("DataEncryptedIV1").innerHTML = "";
        document.getElementById("DataEncryptedIV2").innerHTML = "";
        document.getElementById("DataEncryptedDiversData1").innerHTML = "";
        document.getElementById("DataEncryptedDiversData2").innerHTML = "";
        document.getElementById("DataEncryptedBox1").innerHTML = "";
        document.getElementById("DataEncryptedBox2").innerHTML = "";
        document.getElementById("DataEncryptedKey1").innerHTML = "";
        document.getElementById("DataEncryptedKey2").innerHTML = "";
        document.getElementById("DataDecryptedBox1").innerHTML = "";
        document.getElementById("DataDecryptedBox2").innerHTML = "";

        //Get First certificate
        var e = document.getElementById('CertListBox1');
        if (e.selectedIndex == -1) {
            alert("Select first certificate");
            return;
        }
        var selectedCertID = e[e.selectedIndex].value;
        var certificate1 = global_selectbox_container[selectedCertID];

        //Get second Certificate
        var e = document.getElementById('CertListBox2');
        if (e.selectedIndex == -1) {
            alert("Select second certificate");
            return;
        }
        var selectedCertID = e[e.selectedIndex].value;
        var certificate2 = global_selectbox_container[selectedCertID];

        var dataToEncr1 = Base64.encode(document.getElementById("DataToEncrTxtBox1").value);
        var dataToEncr2 = Base64.encode(document.getElementById("DataToEncrTxtBox2").value);

        if(dataToEncr1 === "" || dataToEncr2 === "") {
            errormes = "Empty data to encrypt";
            alert(errormes);
            throw errormes;
        }

        try
        {
            var errormes = "";

            try {
                var oSymAlgo = yield cadesplugin.CreateObjectAsync("cadescom.symmetricalgorithm");
            } catch (err) {
                errormes = "Failed to create cadescom.symmetricalgorithm: " + err;
                alert(errormes);
                throw errormes;
            }

            yield oSymAlgo.GenerateKey();

            var oSesKey1 = yield oSymAlgo.DiversifyKey();
            var oSesKey1DiversData = yield oSesKey1.DiversData;
            var oSesKey1IV = yield oSesKey1.IV;
            var EncryptedData1 = yield oSesKey1.Encrypt(dataToEncr1, 1);
            document.getElementById("DataEncryptedDiversData1").innerHTML = oSesKey1DiversData;
            document.getElementById("DataEncryptedIV1").innerHTML = oSesKey1IV;
            document.getElementById("DataEncryptedBox1").innerHTML = EncryptedData1;

            var oSesKey2 = yield oSymAlgo.DiversifyKey();
            var oSesKey2DiversData = yield oSesKey2.DiversData;
            var oSesKey2IV = yield oSesKey2.IV;
            var EncryptedData2 = yield oSesKey2.Encrypt(dataToEncr2, 1);
            document.getElementById("DataEncryptedDiversData2").innerHTML = oSesKey2DiversData;
            document.getElementById("DataEncryptedIV2").innerHTML = oSesKey2IV;
            document.getElementById("DataEncryptedBox2").innerHTML = EncryptedData2;

            var ExportedKey1 = yield oSymAlgo.ExportKey(certificate1);
            document.getElementById("DataEncryptedKey1").innerHTML = ExportedKey1;

            var ExportedKey2 = yield oSymAlgo.ExportKey(certificate2);
            document.getElementById("DataEncryptedKey2").innerHTML = ExportedKey2;

            alert("Данные зашифрованы успешно:");
        }
        catch(err)
        {
            alert("Ошибка при шифровании данных:" + err);
            throw("Ошибка при шифровании данных:" + err);
        }
    });//cadesplugin.async_spawn
}

function Decrypt_Async(certListBoxId) {
    cadesplugin.async_spawn (function*(arg) {
        document.getElementById("DataDecryptedBox1").innerHTML = "";
        document.getElementById("DataDecryptedBox2").innerHTML = "";

        var e = document.getElementById(arg[0]);
        var selectedCertID = e[e.selectedIndex].value;
        if (selectedCertID == -1) {
            alert("Выберите сертификат");
            return;
        }

        var certificate = global_selectbox_container[selectedCertID];

        var dataToDecr1 = document.getElementById("DataEncryptedBox1").value;
        var dataToDecr2 = document.getElementById("DataEncryptedBox2").value;
        var field;
        if(certListBoxId == 'CertListBox1')
            field ="DataEncryptedKey1";
        else
            field ="DataEncryptedKey2";

        var EncryptedKey = document.getElementById(field).value;
        try
        {
            FillCertInfo_Async(certificate, 'cert_info_decr');
            var errormes = "";

            try {
                var oSymAlgo = yield cadesplugin.CreateObjectAsync("cadescom.symmetricalgorithm");
            } catch (err) {
                errormes = "Failed to create cadescom.symmetricalgorithm: " + err;
                alert(errormes);
                throw errormes;
            }

            yield oSymAlgo.ImportKey(EncryptedKey, certificate);

            var oSesKey1DiversData = document.getElementById("DataEncryptedDiversData1").value;
            var oSesKey1IV = document.getElementById("DataEncryptedIV1").value;
            yield oSymAlgo.propset_DiversData(oSesKey1DiversData);
            var oSesKey1 = yield oSymAlgo.DiversifyKey();
            yield oSesKey1.propset_IV(oSesKey1IV);
            var EncryptedData1 = yield oSesKey1.Decrypt(dataToDecr1, 1);
            document.getElementById("DataDecryptedBox1").innerHTML = Base64.decode(EncryptedData1);

            var oSesKey2DiversData = document.getElementById("DataEncryptedDiversData2").value;
            var oSesKey2IV = document.getElementById("DataEncryptedIV2").value;
            yield oSymAlgo.propset_DiversData(oSesKey2DiversData);
            var oSesKey2 = yield oSymAlgo.DiversifyKey();
            yield oSesKey2.propset_IV(oSesKey2IV);
            var EncryptedData2 = yield oSesKey2.Decrypt(dataToDecr2, 1);
            document.getElementById("DataDecryptedBox2").innerHTML = Base64.decode(EncryptedData2);

            alert("Данные расшифрованы успешно:");
        }
        catch(err)
        {
            alert("Ошибка при шифровании данных:" + err);
            throw("Ошибка при шифровании данных:" + err);
        }
    }, certListBoxId);//cadesplugin.async_spawn
}

function RetrieveCertificate_Async()
{
    cadesplugin.async_spawn (function*(arg) {
        try {
            var PrivateKey = yield cadesplugin.CreateObjectAsync("X509Enrollment.CX509PrivateKey");
        }
        catch (e) {
            alert('Failed to create X509Enrollment.CX509PrivateKey: ' + cadesplugin.getLastError(e));
            return;
        }

        yield PrivateKey.propset_ProviderName("Crypto-Pro GOST R 34.10-2001 Cryptographic Service Provider");
        yield PrivateKey.propset_ProviderType(80);//75
        yield PrivateKey.propset_KeySpec(1); //XCN_AT_KEYEXCHANGE

        try {
            var CertificateRequestPkcs10 = yield cadesplugin.CreateObjectAsync("X509Enrollment.CX509CertificateRequestPkcs10");
        }
        catch (e) {
            alert('Failed to create X509Enrollment.CX509CertificateRequestPkcs10: ' + cadesplugin.getLastError(e));
            return;
        }

        yield CertificateRequestPkcs10.InitializeFromPrivateKey(0x1, PrivateKey, "");

        try {
            var DistinguishedName = yield cadesplugin.CreateObjectAsync("X509Enrollment.CX500DistinguishedName");
        }
        catch (e) {
            alert('Failed to create X509Enrollment.CX500DistinguishedName: ' + cadesplugin.getLastError(e));
            return;
        }

        var CommonName = "Test Certificate";
        yield DistinguishedName.Encode("CN=\""+CommonName.replace(/"/g, "\"\"")+"\";");

        yield CertificateRequestPkcs10.propset_Subject(DistinguishedName);

        var KeyUsageExtension = yield cadesplugin.CreateObjectAsync("X509Enrollment.CX509ExtensionKeyUsage");
        var CERT_DATA_ENCIPHERMENT_KEY_USAGE = 0x10;
        var CERT_KEY_ENCIPHERMENT_KEY_USAGE = 0x20;
        var CERT_DIGITAL_SIGNATURE_KEY_USAGE = 0x80;
        var CERT_NON_REPUDIATION_KEY_USAGE = 0x40;

        yield KeyUsageExtension.InitializeEncode(
                    CERT_KEY_ENCIPHERMENT_KEY_USAGE |
                    CERT_DATA_ENCIPHERMENT_KEY_USAGE |
                    CERT_DIGITAL_SIGNATURE_KEY_USAGE |
                    CERT_NON_REPUDIATION_KEY_USAGE);

        yield (yield CertificateRequestPkcs10.X509Extensions).Add(KeyUsageExtension);

        try {
            var Enroll = yield cadesplugin.CreateObjectAsync("X509Enrollment.CX509Enrollment");
        }
        catch (e) {
            alert('Failed to create X509Enrollment.CX509Enrollment: ' + cadesplugin.getLastError(e));
            return;
        }
        
        var cert_req;
        try {
            yield Enroll.InitializeFromRequest(CertificateRequestPkcs10);
            cert_req = yield Enroll.CreateRequest(0x1);
        } catch (e) {
            alert('Failed to generate KeyPair or reguest: ' + cadesplugin.getLastError(e));
            return;    
        }

        var params = 'CertRequest=' + encodeURIComponent(cert_req) +
                     '&Mode=' + encodeURIComponent('newreq') +
                     '&TargetStoreFlags=' + encodeURIComponent('0') +
                     '&SaveCert=' + encodeURIComponent('no');

        var xmlhttp = getXmlHttp();
        xmlhttp.open("POST", "https://www.cryptopro.ru/certsrv/certfnsh.asp", true);
        xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        var response;
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4) {
                if(xmlhttp.status == 200) {
                    cadesplugin.async_spawn (function*(arg) {
                        var response = arg[0];
                        var cert_data = "";

                        if(!isIE())
                        {
                            var start = response.indexOf("var sPKCS7");
                            var end = response.indexOf("sPKCS7 += \"\"") + 13;
                            cert_data = response.substring(start, end);
                        }
                        else
                        {
                            var start = response.indexOf("sPKCS7 & \"") + 9;
                            var end = response.indexOf("& vbNewLine\r\n\r\n</Script>");
                            cert_data = response.substring(start, end);
                            cert_data = cert_data.replace(new RegExp(" & vbNewLine",'g'),";");
                            cert_data = cert_data.replace(new RegExp("&",'g'),"+");
                            cert_data = "var sPKCS7=" + cert_data + ";";
                        }

                        eval(cert_data);

                        try {
                            var Enroll = yield cadesplugin.CreateObjectAsync("X509Enrollment.CX509Enrollment");
                        }
                        catch (e) {
                            alert('Failed to create X509Enrollment.CX509Enrollment: ' + cadesplugin.getLastError(e));
                            return;
                        }

                        yield Enroll.Initialize(0x1);
                        yield Enroll.InstallResponse(0, sPKCS7, 0x7, "");
                        document.getElementById("boxdiv").style.display = 'none';
                        if(location.pathname.indexOf("simple")>=0) {
                            location.reload();
                        }
                        else if(location.pathname.indexOf("symalgo_sample.html")>=0){
                            FillCertList_Async('CertListBox1');
                            FillCertList_Async('CertListBox2');
                        }
                        else{
                            FillCertList_Async('CertListBox','');// todo 20190628 передать отпечатки для фильтрации
							}
                    }, xmlhttp.responseText);//cadesplugin.async_spawn
                }
            }
        }
        xmlhttp.send(params);
    });//cadesplugin.async_spawn
}

function CheckForPlugInUEC_Async()
{
    var isUECCSPInstalled = false;

    cadesplugin.async_spawn(function *() {
        var oAbout = yield cadesplugin.CreateObjectAsync("CAdESCOM.About");

        var UECCSPVersion;
        var CurrentPluginVersion = yield oAbout.PluginVersion;
        if( typeof(CurrentPluginVersion) == "undefined")
            CurrentPluginVersion = yield oAbout.Version;

        var PluginBaseVersion = "1.5.1633";
        var arr = PluginBaseVersion.split('.');

        var isActualVersion = true;

        if((yield CurrentPluginVersion.MajorVersion) == parseInt(arr[0]))
        {
            if((yield CurrentPluginVersion.MinorVersion) == parseInt(arr[1]))
            {
                if((yield CurrentPluginVersion.BuildVersion) == parseInt(arr[2]))
                {
                    isActualVersion = true;
                }
                else if((yield CurrentPluginVersion.BuildVersion) < parseInt(arr[2]))
                {
                    isActualVersion = false;
                }
            }else if((yield CurrentPluginVersion.MinorVersion) < parseInt(arr[1]))
            {
                    isActualVersion = false;
            }
        }else if((yield CurrentPluginVersion.MajorVersion) < parseInt(arr[0]))
        {
            isActualVersion = false;
        }

        if(!isActualVersion)
        {
            document.getElementById('PluginEnabledImg').setAttribute("src", "images/yellow_dot.png");
            document.getElementById('PlugInEnabledTxt').innerHTML = "Плагин загружен, но он не поддерживает УЭК.";
        }
        else
        {
            document.getElementById('PluginEnabledImg').setAttribute("src", "images/green_dot.png");
            document.getElementById('PlugInEnabledTxt').innerHTML = "Плагин загружен.";

            try
            {
                var oUECard = yield cadesplugin.CreateObjectAsync("CAdESCOM.UECard");
                UECCSPVersion = yield oUECard.ProviderVersion;
                isUECCSPInstalled = true;
            }
            catch (err)
            {
                UECCSPVersion = "Нет информации";
            }

            if(!isUECCSPInstalled)
            {
                document.getElementById('PluginEnabledImg').setAttribute("src", "images/yellow_dot.png");
                document.getElementById('PlugInEnabledTxt').innerHTML = "Плагин загружен. Не установлен УЭК CSP.";
            }
        }
        document.getElementById('PlugInVersionTxt').innerHTML = "Версия плагина: " + (yield CurrentPluginVersion.toString());
        document.getElementById('CSPVersionTxt').innerHTML = "Версия УЭК CSP: " + (yield UECCSPVersion.toString());
    }); //cadesplugin.async_spawn
}

function FoundCertInStore_Async(cerToFind) {
    return new Promise(function(resolve, reject){
        cadesplugin.async_spawn(function *(args) {

            if((typeof cerToFind == "undefined") || (cerToFind == null))
                args[0](false);

            var oStore = yield cadesplugin.CreateObjectAsync("CAdESCOM.store");
            if (!oStore) {
                alert("store failed");
                args[0](false);
            }
            try {
                yield oStore.Open();
            }
            catch (ex) {
                alert("Certificate not found");
                args[0](false);
            }

            var Certificates = yield oStore.Certificates;
            var certCnt = yield Certificates.Count;
            if(certCnt==0)
            {
                oStore.Close();
                args[0](false);
            }

            var ThumbprintToFind = yield cerToFind.Thumbprint;

            for (var i = 1; i <= certCnt; i++) {
                var cert;
                try {
                    cert = yield Certificates.Item(i);
                }
                catch (ex) {
                    alert("Ошибка при перечислении сертификатов: " + cadesplugin.getLastError(ex));
                    args[0](false);
                }

                try {
                    var Thumbprint = yield cert.Thumbprint;
                    if(Thumbprint == ThumbprintToFind) {
                        var dateObj = new Date();
                        var ValidToDate = new Date(yield cert.ValidToDate);
                        var HasPrivateKey = yield cert.HasPrivateKey();
                        var IsValid = yield cert.IsValid();
                        IsValid = yield IsValid.Result;

                        if(dateObj<ValidToDate && HasPrivateKey && IsValid) {
                            args[0](true);
                        }
                    }
                    else {
                        continue;
                    }
                }
                catch (ex) {
                    alert("Ошибка при получении свойства Thumbprint: " + cadesplugin.getLastError(ex));
                    args[0](false);
                }
            }
            oStore.Close();

            args[0](false);

        }, resolve, reject);
    });
}

function getUECCertificate_Async() {
    return new Promise(function(resolve, reject)
        {
            showWaitMessage("Выполняется загрузка сертификата, это может занять несколько секунд...");
            cadesplugin.async_spawn(function *(args) {
                try {
                    var oCard = yield cadesplugin.CreateObjectAsync("CAdESCOM.UECard");
                    var oCertTemp = yield oCard.Certificate;

                    if(typeof oCertTemp == "undefined")
                    {
                        document.getElementById("cert_info1").style.display = '';
                        document.getElementById("certerror").innerHTML = "Сертификат не найден или отсутствует.";
                        throw "";
                    }

                    if(oCertTemp==null)
                    {
                        document.getElementById("cert_info1").style.display = '';
                        document.getElementById("certerror").innerHTML = "Сертификат не найден или отсутствует.";
                        throw "";
                    }

                    if(yield FoundCertInStore_Async(oCertTemp)) {
                        FillCertInfo_Async(oCertTemp);
                        g_oCert = oCertTemp;
                    }
                    else {
                        document.getElementById("cert_info1").style.display = '';
                        document.getElementById("certerror").innerHTML = "Сертификат не найден в хранилище MY.";
                        throw "";
                    }
                    args[0]();
                }
                catch (e) {
                    var message = cadesplugin.getLastError(e);
                    if("The action was cancelled by the user. (0x8010006E)" == message) {
                        document.getElementById("cert_info1").style.display = '';
                        document.getElementById("certerror").innerHTML = "Карта не найдена или отсутствует сертификат на карте.";
                    }
                    args[1]();
                }
            }, resolve, reject);
        });
}

function createSignature_Async() {
    return new Promise(function(resolve, reject){
        cadesplugin.async_spawn(function *(args) {
            var signedMessage = "";
			
            try {
                var oSigner = yield cadesplugin.CreateObjectAsync("CAdESCOM.CPSigner");
                yield oSigner.propset_Certificate(g_oCert);
                var CAPICOM_CERTIFICATE_INCLUDE_WHOLE_CHAIN = 1;
                yield oSigner.propset_Options(CAPICOM_CERTIFICATE_INCLUDE_WHOLE_CHAIN);

                var oSignedData = yield cadesplugin.CreateObjectAsync("CAdESCOM.CadesSignedData");
                yield oSignedData.propset_Content("DataToSign");

                var CADES_BES = 1;
                signedMessage = yield oSignedData.SignCades(oSigner, CADES_BES);
                args[0](signedMessage);
            }
            catch (e) {
                showErrorMessage("Ошибка: Не удалось создать подпись. Код ошибки: " + cadesplugin.getLastError(e));
                args[1]("");
            }
            args[0](signedMessage);
        }, resolve, reject);
    });
}

function verifyCert_Async() {
    if (!g_oCert) {
        removeWaitMessage();
        return;
    }
    createSignature_Async().then(
        function(signedMessage){
            document.getElementById("SignatureTxtBox").innerHTML = signedMessage;
            var x = document.getElementsByName("SignatureTitle");
            x[0].innerHTML = "Подпись сформирована успешно:";
            removeWaitMessage();
        },
        function(signedMessage){
            removeWaitMessage();
        }
    );
}

function isIE() {
    var retVal = (("Microsoft Internet Explorer" == navigator.appName) || // IE < 11
        navigator.userAgent.match(/Trident\/./i)); // IE 11
    return retVal;
}
function Signatura_CreateSign_Async(Thumbprint, detached, data, isBase64Encoded, setDisplayData) {
    cadesplugin.async_spawn(function*(arg) {

        var thumbprint = arg[0];
        console.log("поиск thumbprint=" + thumbprint);
        try {
            var oStore = yield cadesplugin.CreateObjectAsync("CAdESCOM.Store");

            if (!oStore) {
                alert("Create store failed");
                return;
            }
            yield oStore.Open();
        } catch (e) {
            console.log("сбой: " + cadesplugin.getLastError(e));
        }

        var CAPICOM_CERTIFICATE_FIND_SHA1_HASH = 0;
        console.log("запрос сертификатов ...");
        var all_certs = yield oStore.Certificates;
        console.log("Find...");
        var oCerts = yield all_certs.Find(CAPICOM_CERTIFICATE_FIND_SHA1_HASH, thumbprint);
        console.log("Find.end");
        var certificate = yield oCerts.Item(1);

        console.log("certificate.Thumbprint=" + certificate.Thumbprint);


        var dataToSign = data;

        if (!isBase64Encoded) {
            dataToSign = Base64.encode(data);
        }

        var SignatureFieldTitle = document.getElementsByName("SignatureTitle");
        var Signature;
        try {
            console.log("step1");
            //FillCertInfo_Async(certificate);
            var errormes = "";
            try {
                var oSigner = yield cadesplugin.CreateObjectAsync("CAdESCOM.CPSigner");
            } catch (err) {
                errormes = "Failed to create CAdESCOM.CPSigner: " + err.number;

                yield oStore.Close();
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
            } else {
                errormes = "Failed to create CAdESCOM.CPSigner";

                yield oStore.Close();
                throw errormes;
            }

            console.log("step3");
            var oSignedData = yield cadesplugin.CreateObjectAsync("CAdESCOM.CadesSignedData");
            if (dataToSign) {
                // Данные на подпись ввели
                yield oSigner.propset_Options(cadesplugin.CAPICOM_CERTIFICATE_INCLUDE_WHOLE_CHAIN);
                yield oSignedData.propset_ContentEncoding(cadesplugin.CADESCOM_BASE64_TO_BINARY); //
                if (typeof(setDisplayData) != 'undefined') {
                    //Set display data flag flag for devices like Rutoken PinPad
                    yield oSignedData.propset_DisplayData(1);
                }

                console.log("step4");
                yield oSignedData.propset_Content(dataToSign);

                try {
                    Signature = yield oSignedData.SignCades(oSigner, cadesplugin.CADESCOM_CADES_BES, detached);
                } catch (err) {
                    errormes = "Не удалось создать подпись из-за ошибки: " + cadesplugin.getLastError(err);

                    yield oStore.Close();
                    throw errormes;
                }
            }
            console.log("step5 Signature="+Signature);
			 
 			 var input = document.getElementById("SignatureTxtBox");
              if (input)  { input.innerHTML = Signature;} 
			  
			  var input = document.getElementById("SignatureFieldTitle");
              if (input)  { input.innerHTML = "Подпись сформирована успешно:";}  
			  
			 
			 

            yield oStore.Close();

            return Signature;
        } catch (err) {
            SignatureFieldTitle[0].innerHTML = "Возникла ошибка:";
            document.getElementById("SignatureTxtBox").innerHTML = err;
        }

        yield oStore.Close();
    }, Thumbprint); //cadesplugin.async_spawn
}


function decryptData(dataToDecrypt) {
    return new Promise(function(resolve, reject) {
		console.log("async_spawn...");
        cadesplugin.async_spawn(function*() {
            try {
		        console.log("oEnvelopedData...");
                var oEnvelopedData = yield cadesplugin.CreateObjectAsync("CAdESCOM.CPEnvelopedData");
                yield oEnvelopedData.propset_ContentEncoding(1);
                yield oEnvelopedData.propset_Content(dataToDecrypt);
		        console.log("Decrypt...");
                yield oEnvelopedData.Decrypt(dataToDecrypt);  
				console.log("Decrypted...");
                resolve(yield oEnvelopedData.Content);
            } catch (fatalError) {
                console.log('Ошибка  => ', fatalError);
                reject(fatalError.message);
            } 
        });
    });
}

    


function DecryptEnvelopedBase64_Async(EnvelopedBase64){  
cadesplugin.async_spawn(function*(arg) {
  var EnvelopedBase64 = arg[0]; 
        try
        { 
		   var oEnvelopedData = yield cadesplugin.CreateObjectAsync("CAdESCOM.CPEnvelopedData");

                    yield oEnvelopedData.propset_ContentEncoding(cadesplugin.CADESCOM_BASE64_TO_BINARY);
                    yield oEnvelopedData.propset_Content(EnvelopedBase64);

                    var x = yield oEnvelopedData.Content;
                    var ContentEncoding = yield oEnvelopedData.ContentEncoding;
 
                    console.log('CONTENT=', x); // Content ok
                    console.log('ContentEncoding=',ContentEncoding); //Encoding 1
 
 
 
					var z = yield oEnvelopedData.Decrypt(EnvelopedBase64);
					var z =  oEnvelopedData.Decrypt(EnvelopedBase64);
				    console.log('z=',z ); 
                     
					  //document.getElementById("DataDecryptexBase64").innerHTML = Base64.decode(z);
                
					return;
					
            var errormes = "";
            try {
              envelopedData = yield cadesplugin.CreateObjectAsync("CAdESCOM.CPEnvelopedData");
            } catch (err) {
                errormes = "Failed to create CAdESCOM.CPEnvelopedData: " + err.number;
                throw errormes;
            }  

            if (envelopedData) { 
				yield envelopedData.propset_ContentEncoding(cadesplugin.CADESCOM_BASE64_TO_BINARY);
              //yield envelopedData.propset_Content(EnvelopedBase64);					
            }
            else {
                errormes = "Failed to create CAdESCOM.CPEnvelopedData";
                throw errormes;
            }
    
                try { 
                    console.log("envelopedData.Decrypt..."+EnvelopedBase64);
					  envelopedData.Decrypt(EnvelopedBase64).then((result) => {
                        console.log('Result ', envelopedData.Content);
                    });
					
                    var    data =  yield envelopedData.Decrypt(EnvelopedBase64);
                    console.log("envelopedData.Decrypted... ["+data+"]");
                    console.log("envelopedData.Content... ["+envelopedData.Content+"]");
                    document.getElementById("DataDecryptexBase64").innerHTML = (data); 
                    
                    //document.getElementsByName('TimeTitle')[0].innerHTML = "Время выполнения: " + (EndTime - StartTime) + " мс";
                }
                catch (err) {
                    errormes = "Не удалось выполнить из-за ошибки: " + cadesplugin.getLastError(err);
                    console.log("envelopedData.Decrypt Error="+errormes);
                    throw errormes;
                } 
        }
        catch(err)
        { 
            document.getElementById("DataDecryptexBase64").innerHTML = err;
        }
    }, EnvelopedBase64); //cadesplugin.async_spawn
   
}


async_resolve();
