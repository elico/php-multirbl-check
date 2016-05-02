function fetchIp() {
    var ipElement = document.getElementById("ip_address");
    var ip = ipElement.value;
    //  console.log(ip);
    checkIp(ip);
}

function checkIp(ipAddress) {
    //  console.log(ipAddress);
    var last_response_len = false;
    document.getElementById("testResults").innerHTML = "Starting the test...<br />\n----<br />\n";
    $.ajax('rbl_check_json_stream.php?ip=' + ipAddress, {
            xhrFields: {
                onprogress: function(e) {
		    document.getElementById("testStatus").innerHTML = "<font color='orange'>Test In Progress</font><br />\n";
                    var this_response, response = e.currentTarget.response;
                    if (last_response_len === false) {
                        this_response = response;
                        last_response_len = response.length;
                    } else {
                        this_response = response.substring(last_response_len);
                        last_response_len = response.length;
                    }
                    console.log(this_response);
                    this_response = JSON.parse(this_response);
                    console.log(this_response);
                    //  console.log(this_response.list);
                    if (this_response.blacklisted == "true") {
                        // console.log(this_response.list, "listed");
                        document.getElementById("testResults").innerHTML = document.getElementById("testResults").innerHTML + "Listed at: <font color='red'>" + this_response.list + "</font><br />\n";
                    }
                    if (this_response.list == "notlisted.ngtech.co.il") {
                        // console.log(this_response.list, "NOTLISTED");
                        document.getElementById("testResults").innerHTML = document.getElementById("testResults").innerHTML + "<font color='green'>Was not Listed by any blacklist</font><br />\n";
                    }
                }
            }
        })
        .done(function(data) {
	    document.getElementById("testStatus").innerHTML = "<font color='GREEN'>Test Completed</font><br />\n";
            console.log('Complete response = ' + data);
        })
        .fail(function(data) {
            console.log('Error: ', data);
        });
    // console.log('Request Sent');
}

