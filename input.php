<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        form {
            width: 300px;
            height: 500px;
            background-color: lightblue;
            padding: 10px;
        }

        label {
            display: block;
            margin-bottom: 20px;
        }

        input, select {
            width: 100%;
            height: 30px;
            margin-bottom: 20px;
        }

    </style>

</head>
<body>

<form action="./input.php" method="POST">
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
        <option value="4999">Outstanding</option>
    </select>
    <label>Grade Average</label>
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
    
</body>
</html>