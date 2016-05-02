<?php
header('Content-type: text/plain; charset=utf-8');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");

function output($list, $listed)
{
    echo '{"list" : "' . $list . '", "blacklisted" : "' . $listed . '"}';
    flush();
    ob_flush();
    usleep(100000);
}

function dnsbllookup($ip)
{
    $data         = file_get_contents("http://www1.ngtech.co.il/rbl/rbl.csv");
    $rows         = explode("\n", $data);
    $dnsbl_lookup = array();

    foreach ($rows as $row) {
        $s = str_getcsv($row);
        #  print_r($s);
        if (count($s) > 1 && $s[1] == "1") {
            array_push($dnsbl_lookup, $s[0]);
        }
    }

    //  A fixed array option
    //  $dnsbl_lookup = array("dnsbl-1.uceprotect.net","dnsbl-2.uceprotect.net","dnsbl-3.uceprotect.net","dnsbl.dronebl.org","dnsbl.sorbs.net","zen.spamhaus.org","noptr.spamrats.com"); // Add your preferred list of DNSBL's
    $listed         = "";
    $listed_counter = 0;
    if ($ip) {
        $reverse_ip = implode(".", array_reverse(explode(".", $ip)));
        foreach ($dnsbl_lookup as $host) {
            if (checkdnsrr($reverse_ip . "." . $host . ".", "A")) {
                $listed .= $reverse_ip . '.' . $host . ' <font color="red">Listed</font><br />';
                $listed_counter++;
                output($host, "true");
            } else {
                output($host, "false");
            }

        }
    }
    if ($listed_counter == 0) {
        output("notlisted.ngtech.co.il", "false");
    }
}

$ip = $_GET['ip'];
if (isset($_GET['ip']) && $_GET['ip'] != null) {
    if (filter_var($ip, FILTER_VALIDATE_IP)) {
        echo dnsbllookup($ip);
    } else {
        echo "Please enter a valid IP";
    }
}
?>

