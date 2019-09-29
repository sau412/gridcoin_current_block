<?php
// Gridcoinresearch show current block info
require_once("settings.php");

// Send query to gridcoin client
function grc_rpc_send_query($query) {
        global $grc_rpc_host,$grc_rpc_port,$grc_rpc_login,$grc_rpc_password;
        $ch=curl_init("http://$grc_rpc_host:$grc_rpc_port");

        curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
        curl_setopt($ch,CURLOPT_POST,TRUE);
        curl_setopt($ch,CURLOPT_USERPWD,"$grc_rpc_login:$grc_rpc_password");

        curl_setopt($ch, CURLOPT_POSTFIELDS,$query);
        $result=curl_exec($ch);
        curl_close($ch);

        return $result;
}

function grc_rpc_get_info() {
        $query='{"id":1,"method":"getinfo","params":[]}';
        $result=grc_rpc_send_query($query);
        $data=json_decode($result);
        return $data->result;
}

function grc_rpc_block_hash($block) {
        $query='{"id":1,"method":"getblockhash","params":['.$block.']}';
        $result=grc_rpc_send_query($query);
        $data=json_decode($result);
        return $data->result;
}

function grc_rpc_block($hash) {
        $query='{"id":1,"method":"getblock","params":["'.$hash.'"]}';
        $result=grc_rpc_send_query($query);
        $data=json_decode($result);
        return $data->result;
}

// Get balance
$info=grc_rpc_get_info();
$block=$info->blocks;
$difficulty=$info->difficulty->{"proof-of-stake"};

// Get current block hash
$blockhash_info=grc_rpc_block_hash($block);
// Get current block info
$blockinfo=grc_rpc_block($blockhash_info);
$address=$blockinfo->GRCAddress;
$version=$blockinfo->ClientVersion;
$cpid=$blockinfo->CPID;
$mint=$blockinfo->mint;
$interest=$blockinfo->Interest;

// Template with results
echo <<<_END
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8"/>
<title>Current Gridcoin Block</title>
</head>
<body>
<center style='valign:middle;'>
<p>Current Gridcoin block</p>
<h1 style='font-size:1000%'>$block</h1>
<p>Staked by <b>$address</b> (CPID <b>$cpid</b>)</p><p>Client version <b>$version</b></p><p>Mint <b>$mint</b> GRC interest <b>$interest</b> GRC</p><p>Current difficulty <b>$difficulty</b></p>
</center>
<script>
setTimeout(function() {
  location.reload();
}, 10000);
</script>
</body>
</html>
_END;

?>