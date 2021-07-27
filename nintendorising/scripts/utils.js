var binaryStream;
var fileName;
var uploadUrl;
var elementToEnable;
var uploadAjaxRequest;

function getAjaxRequest() {
	var ajaxRequest = null;
	try {
		ajaxRequest = new XMLHttpRequest();
	} catch (e) {
		try {
			ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try {
				ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e) {
				alert("You browser does not support AJAX.  This website will not work!");
			}
		}
	}
 	return ajaxRequest;
}

function getElement(elementId) {
	if (document.getElementById) {
		return document.getElementById(elementId);
	} else if (document.all) {
		return document.all[elementId];
	} else if (document.layers) {
		return document.layers[elementId];
	}
}

function getStyle(elementId, cssProperty) {
	var element = getElement(elementId);
	var strValue = "";
	if (document.defaultView && document.defaultView.getComputedStyle) {
		strValue = document.defaultView.getComputedStyle(element,null).getPropertyValue(cssProperty);
	} else if (elementId.currentStyle) {
		cssProperty = cssProperty.replace(/\-(\w)/g, function (strMatch, p1){
			return p1.toUpperCase();
		});
		strValue = oElm.currentStyle[strCssRule];
	}
	
	return strValue;
}

function uploadFile(filePath, elementToDisable, handlerUrl) {
	if (filePath == null) return;
	fileName = filePath;
	uploadUrl = handlerUrl;
	elementToEnable = elementToDisable;
		
	// Disable the file browser input field until the upload is complete.
	elementToDisable.disabled = true;
	
	// Get permission to handle files stored locally.
	try {
		netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
	} catch (e) {
		alert("Your browser will not allow you to upload files.  Please change its security settings to allow uploads.");
	}
	
	// Open the file
	var file = Components.classes["@mozilla.org/file/local;1"].createInstance(Components.interfaces.nsILocalFile);
	file.initWithPath(filePath);
	
	// Create an input stream to read data from the file.
	var fileStream = Components.classes["@mozilla.org/network/file-input-stream;1"].createInstance(Components.interfaces.nsIFileInputStream);
	fileStream.init(file, 0x01, 4, null);
	
	var bufferedStream = Components.classes["@mozilla.org/network/buffered-input-stream;1"].getService();
	bufferedStream.QueryInterface(Components.interfaces.nsIBufferedInputStream);
	bufferedStream.init(fileStream, 1000);
	bufferedStream.QueryInterface(Components.interfaces.nsIInputStream);
	
	binaryStream = Components.classes["@mozilla.org/binaryinputstream;1"].createInstance(Components.interfaces.nsIBinaryInputStream);
	binaryStream.setInputStream(fileStream);
	
	// Upload after 1 second
	setTimeout("ajaxUpload()",1000);
}

function ajaxUpload() {
	// Get permission to handle files stored locally - again.
	try {
		netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
	} catch (e) {
		alert("Your browser will not allow you to upload files.  Please change its security settings to allow uploads.");
	}
	
	uploadAjaxRequest = getAjaxRequest();
	if (!uploadAjaxRequest) {
		alert("Your browser does not support AJAX requests.");
		return;
	}
	
	var boundaryString = "nrising";
	var seperator = "--" + boundaryString + "\n";
	var request = seperator + 'Content-Disposition: form-data; name="myfile"; filename="' 
		+ fileName + '"' + '\n' + 'Content-Type: application/octet-stream' + '\n\n' +
		escape(binaryStream.readBytes(binaryStream.available())) + '\n' + seperator;
	
	uploadAjaxRequest.onreadystatechange = requestDone;	
	uploadAjaxRequest.open("POST",uploadUrl,true);
	uploadAjaxRequest.setRequestHeader("Content-type", "multipart/form-data; boundary=\"" + boundaryString + "\"");
	uploadAjaxRequest.setRequestHeader("Connection", "close");
	uploadAjaxRequest.setRequestHeader("Content-length", request.length);
	uploadAjaxRequest.send(request);
}

function requestDone() {
	elementToEnable.disabled = false;
	uploadDone(uploadAjaxRequest.responseText);
}
