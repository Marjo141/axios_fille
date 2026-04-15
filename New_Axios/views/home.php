<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../assests/css/index.css">
</head>
<body>
    <div class="container">
        <h1>HOME PAGE</h1>
        
        <div class="con">
            <div class="form-column">
                <div class="form-group">
                    <form id="formBooking">
                        <input type="text" id="fname" name="fname" placeholder="Firstname"><br>
                        <input type="text" id="lname" name="lname" placeholder="Lastname"><br>
                        <input type="date" id="ci" name="ci"><br>
                        <input type="date" id="co" name="co"><br>
                        <input type="text" id="an" name="an" placeholder="Additional Needs"><br>
                        <input type="submit" id="bookingSubmit" value="Submit">
                    </form>
                    <form id="displayData">
                        <input type="number" id="bookingID" placeholder="Input booking ID">
                        <input type="submit" value="Display Info">
                    </form>
                </div>
            </div>
            <div class="table-wrap">
                <table>
                <thead>
                    <tr>
                        <th>Firstname</th>
                        <th>Lastname</th>
                        <th>Total Price</th>
                        <th>Deposit Paid</th>
                        <th>Checkin</th>
                        <th>Checkout</th>
                        <th>Additional Needs</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="tbody">
                    
                </tbody>
            </table>
            </div>
        </div>

        <hr>
        <button id="logout">Logout</button>
    </div>
</body>
    <script src="../assests/js/axios.min.js"></script>
    <script src="../assests/js/home.js"></script>
</html>