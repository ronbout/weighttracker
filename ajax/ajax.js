// ajax.js
// functions related to javascript communicating with server

//Browser Support Code
function Ajax(){
	// first get the request object
	try 
	{
		// Opera 8.0+, Firefox, Safari
		this.ajaxRequest = new XMLHttpRequest();
	}
	catch (e)
	{
		// Internet Explorer Browsers
		try	
		{
			this.ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
		}	
		catch (e)	
		{
			try
			{
				this.ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e)
			{
				// Something went wrong
				alert("problem with http request object");
				return false;
			}
		}
	}
}
Ajax.prototype.loadRequest = function(resp_fn) {
	var that = this;
	this.ajaxRequest.onreadystatechange = function(){ 
		if (that.ajaxRequest.readyState == 4)
			if (that.ajaxRequest.status == 200)
				resp_fn(that.ajaxRequest.responseText);
	}
}

Ajax.prototype.sendRequest = function(url) {
	this.ajaxRequest.open("GET", url, true);
	this.ajaxRequest.send(null);
}

function runAjax(url, fnDef)
{
	var url = "/wt/ajax/" + url;
	var request = new Ajax;
	request.loadRequest(fnDef);
	request.sendRequest(url);
}




