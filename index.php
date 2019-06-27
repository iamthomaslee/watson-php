<?php
// The API Key can be obtained from https://cloud.ibm.com/catalog/services/text-to-speech for free.
$apikey = "";
// create a filename with timestam
$filename = "/" . time() . ".mp3";
// echo audio tag when the speech file has been created successfully
$displayAudio = false;
// initialize and clear out text 
$text = $inputText = "";

// if textarea has input text, put the value to $text
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST["inputText"])) {
        $text = htmlspecialchars($_POST["inputText"]);
    }
}

// initialize curl
$curl = curl_init();
// curl_setopt options can be found from https://www.php.net/manual/en/function.curl-setopt.php
curl_setopt($curl, CURLOPT_URL, 'https://stream.watsonplatform.net/text-to-speech/api/v1/synthesize');
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_POSTFIELDS, "{\"text\":\"" . $text . "\"}");
curl_setopt($curl, CURLOPT_POST, 1);
// disabling SSL verification -- https://cloud.ibm.com/apidocs/text-to-speech?code=node#disabling-ssl
curl_setopt ($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_USERPWD, 'apikey:'. $apikey);

// set headers
$headers = array();
$headers[] = 'Content-Type: application/json';
$headers[] = 'Accept: audio/mp3';
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($curl);
$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
if (curl_errno($curl)) {
    echo 'Error:' . curl_error($curl);
}

curl_close($curl);
if ($status == 200) {
    // display audio player on screen
    file_put_contents(dirname(__FILE__) . $filename, $result);
    $displayAudio = true;
}
?>
<html>
<head>
</head>
<body>
    <h1>Text to Speech using IBM Watson</h1>
    <p>This is a sample PHP page that converts text to speech (mp3) using IBM Watson.</p>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
        <h2>Input Text</h2>
        <textarea type="" name="inputText" rows="10" cols="50"><?php echo $text;?></textarea><br/>
        <input type="submit" value="Convert" style="height:2em;margin-top:1em;">
    </form>
    <?php
    if ($displayAudio == true) {
        echo "<audio controls>";
        echo "  <source src=." . $filename . " type='audio/mpeg'/>";
        echo "</audio>";
    }
    ?>
</body>
</html>