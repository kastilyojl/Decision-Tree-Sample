<?php
    require('./dataset.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DataSet</title>
    <link rel="stylesheet" href="style.css">

</head>
<body>

    <div class="container">
        <div class="left">
            <form action="./dataset.php" method="POST">
                <label>Documents</label>
                <select name="documents">
                    <option hidden>Select</option>
                    <option value="1">Complete</option>
                    <option value="0">Incomplete</option>
                </select>
                <label>Tuition Fee</label>
                <select name="tuition">
                    <option hidden>Select</option>
                    <option value="5000">Paid</option>
                    <option value="4999">Outstanding Balance</option>
                </select>
                <label>Grade Average <br> <span class="average" >[Passed] >= 2.50 < [Failed]</span></label>
                <input type="text" placeholder="e.g. 2.5" name="grade">
                <label>Clearance</label>
                <select name="clearance">
                    <option hidden>Select</option>
                    <option value="1">Accomplished</option>
                    <option value="0">Not Accomplished</option>
                </select>
                <label>Course Selection</label>
                <select name="cs">
                    <option hidden>Select</option>
                    <option value="1">Selection Open</option>
                    <option value="0">Not Applicable</option>
                </select>
                <input type="submit" name="submit">
            </form>
        </div>
        <div class="right">
            <div class="img-container">
                <img src="./sample data set.png" alt="">
            </div>
            <div class="prediction-container">
                <?php
                    if (isset($_GET['prediction'])) {
                        echo "<p>Prediction: " . $_GET['prediction'] . "</p>";
                    }
                ?>
            </div>
        </div>
    </div>

</body>
</html>