<?php
// create curl resource
$ch = curl_init();
$username = 'ws-php-assignment-8';
$password = 'Qs8[x<mx0|';
$domain = 'php-assignment-8.ws';

$time = time();
$method = 'GET';
$path = '/v1/user/self/zone/' . $domain . '/record';
$api = 'https://rest.websupport.sk';
$apiKey = '8b2fb78b-9e7c-47d4-8676-11f19e9307c3';
$secret = '6327b8b782d93ec5a6b04164dd0561f76ca6b1e6';
$canonicalRequest = sprintf('%s %s %s', $method, $path, $time);
$signature = hash_hmac('sha1', $canonicalRequest, $secret);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, sprintf('%s:%s', $api, $path));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ':' . $signature);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Date: ' . gmdate('Ymd\THis\Z', $time),
]);

$response = curl_exec($ch);
$data = json_decode($response);

curl_close($ch);
?>

<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>WS Domains</title>
</head>
<body>
<?php
session_start();
if (!empty($_SESSION['status'])) {
    if ($_SESSION['status'] == 'success') {
        echo "<div style='background-color: green'><p style='color: white; margin-left: 1%'>Record added successfully.</p></div>";
    } else {
        echo "<div style='background-color: red'><p style='color: white; margin-left: 1%'>" . $_SESSION['errorContent'] . "</p></div>";
    }
} else if (!empty($_SESSION['deleted'])) {
    echo "<div style='background-color: green'><p style='color: white; margin-left: 1%'>Record deleted successfully.</p></div>";
}
session_destroy();

?>
<h1>List of records</h1>
<div class="table">
    <?php
    echo "<table>
<thead>
<th>Id</th>
<th>Type</th>
<th>Prio</th>
<th>Port</th>
<th>Weight</th>
<th>Name</th>
<th>Content</th>
<th>ttl</th>
<th>Note</th>
</thead>
<tbody>";
    foreach ($data->items as $item) {
        $prio = isset($item->prio) ? $item->prio : "";
        $port = isset($item->port) ? $item->port : "";
        $weight = isset($item->weight) ? $item->weight : "";

        echo "<tr>";
        echo "<td>$item->id</td><td>$item->type</td><td>$prio</td><td>$port</td><td>$weight</td><td>$item->name</td><td>$item->content</td><td>$item->ttl</td><td>$item->note</td>";
        echo "</tr>";
    }

    echo "</tbody></table>";
    ?>
</div>
<div class="form" style="float: left">
    <h1>Create new record</h1>
    <form id="form" action="create.php" method="post">
        <label for="type">Choose a type:</label>
        <select name="type" id="type" onchange="appendInputs(this)">
            <option selected disabled>Select a type</option>
            <option value="A">A</option>
            <option value="AAAA">AAAA</option>
            <option value="MX">MX</option>
            <option value="ANAME">ANAME</option>
            <option value="CNAME">CNAME</option>
            <option value="NS">NS</option>
            <option value="TXT">TXT</option>
            <option value="SRV">SRV</option>
        </select>
        <div id="container"></div>
        <input type="submit" value="Create">
    </form>
</div>
<div class="delete" style="float: left; margin-left: 10%">
    <h1>Delete Record</h1>
    <form id="form" action="delete.php" method="post">
        <label for="id">Choose a record:</label>
        <select name="id" id="id">
            <option selected disabled>Select a record</option>
            <?php
            foreach ($data->items as $item) {
                echo '<option value = "' . $item->id . '">' . $item->id . ' ' . $item->type . '</option>';
            }
            ?>
        </select>
        <div id="container"></div>
        <input type="submit" value="Delete">
    </form>
</div>
</body>
<script>
    function appendInputs(object) {
        var value = object.value
        var container = document.getElementById("container");
        // Clear previous contents of the container
        while (container.hasChildNodes()) {
            container.removeChild(container.lastChild);
        }
        var inputs = {
            A: ["name", "content", "ttl"],
            AAAA: ["name", "content", "ttl"],
            MX: ["name", "content", "prio", "ttl"],
            ANAME: ["name", "content", "ttl"],
            CNAME: ["name", "content", "ttl"],
            NS: ["name", "content", "ttl"],
            TXT: ["name", "content", "ttl"],
            SRV: ["name", "content", "prio", "port", "weight", "ttl"],
        }
        inputs[value].forEach(async function (row) {
            container.appendChild(document.createTextNode(row));
            // Create an <input> element, set its type and name attributes
            var input = document.createElement("input");
            if (row == "prio" || row == "port" || row == "weight" || row == "ttl") {
                input.type = "number";
            } else {
                input.type = "text";
            }
            if (row != "ttl") {
                input.required = true
            }
            if (value == "ANAME" && row == "name") {
                input.required = false
            }
            input.name = row
            container.appendChild(input);
            // Append a line break
            container.appendChild(document.createElement("br"));
        })
    }
</script>
</html>
