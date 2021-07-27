var ajaxLoginReq;

function login() {
	if (getElement("loginUserName").value.length == 0) {
		alert("You must enter a user name before proceeding.");
		getElement("userName").focus();
	} else {
		ajaxLoginReq = getAjaxRequest();
		if (ajaxLoginReq) {
			var urlString = "/nintendorising/admin/login.php?logreq=login" +
				"&uname=" + escape(getElement("loginUserName").value) +
				"&pass=" + escape(getElement("loginPassword").value);
			ajaxLoginReq.open("GET",urlString,true);
			ajaxLoginReq.onreadystatechange = loggedIn;
			ajaxLoginReq.send(null);
		}
	}
}

function logout() {
	ajaxLoginReq = getAjaxRequest();
	if (ajaxLoginReq) {
		var urlString = "/nintendorising/admin/login.php?logreq=logout";
		ajaxLoginReq.open("GET",urlString,true);
		ajaxLoginReq.onreadystatechange = loggedIn;
		ajaxLoginReq.send(null);
	}
}

function getLoginStatus() {
	ajaxLoginReq = getAjaxRequest();
	if (ajaxLoginReq) {
		var urlString = "/nintendorising/admin/login.php";
		ajaxLoginReq.open("GET",urlString,true);
		ajaxLoginReq.onreadystatechange = loggedIn;
		ajaxLoginReq.send(null);
	}
}

function loggedIn() {
	getElement("loginBox").innerHTML = ajaxLoginReq.responseText;
}

function checkRegistration(formName) {
	var valid = true;
	if (formName.userName.value.search(/(\W)/) > -1) {
		alert("You cannot user characters other than alphanumeric and underscore '_' for the username");
		formName.userName.focus();
		valid = false;
	}
	
	if (formName.userName.value.length < 1) {
		alert("Your username must be between 1 and 32 characters in length");
		formName.userName.focus();
		valid = false;
	}
	
	if (formName.realName.value.length > 0 && formName.realName.value.search(/[^A-Za-z\s]/) > -1) {
		alert("Your real name can only contain alphabetic characters and spaces");
		formName.realName.focus();
		valid = false;
	}
	
	if (formName.password.value != formName.conpass.value) {
		alert("The password and confirmation passwords do not match");
		formName.password.focus();
		valid = false;
	}
	
	if (formName.password.value.length < 4) {
		alert("Your password should be at least four (4) characters long");
		formName.password.focus();
		valid = false;
	}
	
	if (formName.email.value.length < 1) {
		alert("You need to provide an email address during the registration process");
		formName.email.focus();
	}
	
	if (!formName.email.value.match(/^[A-Za-z0-9._%+-]+@[A-Za-z0.9.-]+\.[A-Za-z]{2,4}$/)) {
		alert("Invalid email.  Please check to make sure you haven't typed it incorrectly");
		formName.password.focus();
		valid = false;
	}
	return valid;
}
