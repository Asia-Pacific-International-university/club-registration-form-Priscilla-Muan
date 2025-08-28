
<!-- // Club Registration Form Processing
// TODO: Add your PHP processing code here starting in Step 3
// Club Registration Form Processing -->


<!-- Step 3 Requirements:
- Process form data using $_POST
- Display submitted information back to user
- Handle name, email, and club fields

Step 4 Requirements:
- Add validation for all fields
- Check for empty fields
- Validate email format
- Display appropriate error messages

Step 5 Requirements:
- Store registration data in arrays
- Display list of all registrations
- Use loops to process array data

Step 6 Requirements:
- Add enhanced features like:
  - File storage for persistence
  - Additional form fields
  - Better error handling
  - Search functionality -->

<?php
// Club Registration Form Processing

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $club = $_POST['club'];

    // HTML output with Bootstrap-like styling
    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Registration Confirmation</title>
        <style>
            body { 
                font-family: Arial, sans-serif; 
                margin: 20px; 
                background-color: #f4f4f4; 
            }
            .container {
                max-width: 600px;
                margin: 0 auto;
                background: white;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }
            h1 { color: #333; }
            .info { margin: 15px 0; }
            .label { font-weight: bold; }
            .back-button {
                display: inline-block;
                padding: 10px 20px;
                background-color: #5c5cdc;
                color: white;
                text-decoration: none;
                border-radius: 4px;
                margin-top: 20px;
            }
            .back-button:hover {
                background-color: #2d1083;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <h1>Registration Confirmation</h1>
            <div class='info'>
                <p><span class='label'>Name:</span> " . htmlspecialchars($name) . "</p>
                <p><span class='label'>Email:</span> " . htmlspecialchars($email) . "</p>
                <p><span class='label'>Selected Club:</span> " . htmlspecialchars($club) . "</p>
            </div>
            <a href='index.html' class='back-button'>Back to Registration</a>
        </div>
    </body>
    </html>";
}
?>