<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <h1>Currency Converter</h1>
    <div>
    <form method="post" >
        <h2>Enter Amount</h2>
        <input type="number" name="amt" required />
        <label>
            <p>From</p>
            <select name="fromCur" required id="">
                <option>Select</option>
                <option>USD</option>
                <option>INR</option>
                <option>GBP</option>
                <option>EUR</option>
            </select>
        </label>
        <br />
        <br />
        <label>
            <p>To</p>
            <select name="toCur" required id="">
                <option>Select</option>
                <option>USD</option>
                <option>INR</option>
                <option>GBP</option>
                <option>EUR</option>
            </select>
        </label>
        <input type="submit" name="submit" /> <br>
        <?php
    if (isset($_POST['submit'])) {
        $conn = mysqli_connect("localhost","root","Samarth","checkdb");
        if ($conn->connect_error) {
            die("Connection failed: "
                . $conn->connect_error);
        }
        $amt = $_POST['amt'];
        $toCur = $_POST['toCur'];
        // $fromCur = $_POST['fromCur'];
        $url = "http://api.exchangeratesapi.io/v1/latest?access_key=6b5de549cd3e342e3c919600a0ecabe8&format=1&base=$fromCur&symbols=$toCur";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        $result = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($result, true);
        // print_r($result);
        $final_amt = $result['rates'][$toCur];
        echo $amt * $final_amt;
        //json decode
        $jsonObj = json_decode($result);
        if (isset($jsonObj->conversion_result)) {
            $conversionResult = $jsonObj->conversion_result;
            echo "<h2>Conversion Result:</h2>";
            echo "<p>$amount $fromCurrency is equal to $conversionResult $toCurrency</p>";
        // print_r($final_amt);
        // $sql = "INSERT INTO currencyTable (amount,from) values ($amt,$toCur)"
        // $x= mysqli_query($conn,$sql);
        $insert_query = "INSERT INTO conversion_history (to_currency, amount, result) VALUES ('$toCur',$amt, $result)";
        $stmt = mysqli_prepare($conn, $insert_query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssds", $fromCurrency, $toCurrency, $amount, $conversionResult);
            if (mysqli_stmt_execute($stmt)) {
                // The conversion result has been saved to the database
                mysqli_stmt_close($stmt);
            }
            else {
                // Handle database insertion error
                echo "<h2 style='color: red;'>Error:</h2>";
                echo "<p style='color: red;'>Failed to save conversion result to the database.</p>";

            }
        }
        else {
            // Handle database query preparation error
            echo "<h2 style='color: red;'>Error:</h2>";
            echo "<p style='color: red;'>Database query preparation failed.</p>";
        }
    }
    else{
        echo "<h2 style='color: red;'>Error:</h2>";
        echo "<p style='color: red;'>Unable to fetch conversion rate. Please check your input and try again.</p>";
    }
    mysqli_close($con);
}
else {
    // Handle cases where the form is not submitted
    echo "<h2>Error:</h2>";
    echo "<p>Invalid request.</p>";
}
    ?>
        </div>
</body>
</html>