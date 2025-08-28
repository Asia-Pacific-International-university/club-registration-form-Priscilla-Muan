<?php
/* 
 Club Registration Form Processing*/

session_start();

// initialize storage
if (!isset($_SESSION['registrations'])) {
    $_SESSION['registrations'] = [];
}

// allowed clubs - adjust to match your index.html options
$allowedClubs = ['programming','art','sports','music','drama'];

// map club value => label with emoji (used for display)
$clubLabels = [
    'programming' => "🖥️ Programming Club",
    'art'         => "🎨 Art Club",
    'sports'      => "⚽ Sports Club",
    'music'       => "🎵 Music Club",
    'drama'       => "&#127916;Drama Club",
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];

    // fetch + sanitize
    $name  = trim($_POST['name']  ?? '');
    $email = trim($_POST['email'] ?? '');
    $club  = trim($_POST['club']  ?? '');

    // validation
    if ($name === '') {
        $errors[] = 'Name is required';
    } elseif (mb_strlen($name) < 3) {
        $errors[] = 'Name must be at least 3 characters long';
    }

    if ($email === '') {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format';
    }

    if ($club === '' || !in_array($club, $allowedClubs, true)) {
        $errors[] = 'Please select a valid club';
    }

    if (!empty($errors)) {
        // error page
        echo '<!DOCTYPE html>
        <html lang="en">
        <head>
          <meta charset="utf-8">
          <meta name="viewport" content="width=device-width,initial-scale=1">
          <title>Registration Error</title>
          <style>
            body{
              font-family:Arial;
              margin:20px;
              background:#f4f4f4
            } 
            .container{
              max-width:700px;
              margin:0 auto;
              background:#fff;
              padding:20px;
              border-radius:8px;
              box-shadow:0 2px 10px rgba(0,0,0,0.1)
            } 
            .error{
              color:#dc3545;
              background:#f8d7da;
              padding:10px;
              border-radius:4px;
              margin-bottom:10px
            } 
            .back-button{
              display:inline-block;
              padding:10px 20px;
              background:#5c5cdc;
              color:#fff;
              text-decoration:none;
              border-radius:4px;
              margin-top:20px
            } 
            .back-button:hover{
              background:#2d1083
            }
          </style>
        </head>
      <body>
        <div class="container">
          <h1>Registration Error</h1>
          <div class="error">
          <ul>';
          foreach ($errors as $e) {
            echo '<li>' . htmlspecialchars($e) . '</li>';
          }
          echo '</ul>
          </div>
            <a class="back-button" href="index.html">Back to Registration</a>
        </div>
      </body>
      </html>';
        exit;
    }

    // store registration
    $registration = [
        'name'  => $name,
        'email' => $email,
        'club'  => $club,
        'date'  => date('Y-m-d H:i:s')
    ];
    $_SESSION['registrations'][] = $registration;

    // success + list
        echo '<!DOCTYPE html>
        <html lang="en">
        <head>
          <meta charset="utf-8">
          <meta name="viewport" content="width=device-width,initial-scale=1">
          <title>Registration Confirmation</title>
          <style>
            body{
              font-family:Arial;
              margin:20px;
              background:#f4f4f4}
              .container{
              max-width:900px;
              margin:0 auto;
              background:#fff;
              padding:20px;
              border-radius:8px;
              box-shadow:0 2px 10px rgba(0,0,0,0.1)
            }
            .success{
              background:#d4edda;
              border:1px solid #c3e6cb;
              color:#155724;
              padding:15px;
              border-radius:4px;
              margin-bottom:20px
            }
            table{
              width:100%;border-collapse:collapse;
              margin-top:20px
            }
            th,td{
              padding:10px;
              text-align:left;
              border-bottom:1px solid #ddd
            }
            th{
              background:#f5f5f5;
              font-weight:600
            }
            tr:hover{
              background:#f9f9f9
            }
            .back-button{
              display:inline-block;
              padding:10px 20px;
              background:#5c5cdc;
              color:#fff;
              text-decoration:none;
              border-radius:4px;
              margin-top:20px
            }
            .back-button:hover{
              background:#2d1083
            }
          </style>
        </head>
        <body>
          <div class="container">
            <div class="success">
              <h1>Registration Successful!</h1>
              <p><strong>Name:</strong> ' . htmlspecialchars($registration['name']) . '</p>
              <p><strong>Email:</strong> ' . htmlspecialchars($registration['email']) . '</p>
              <p><strong>Club:</strong> ' . htmlspecialchars($registration['club']) . '</p>
            </div>';

          echo '<h2>All Registrations (' . count($_SESSION['registrations']) . ')</h2>
          <table>
            <thead>
              <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Club</th>
                <th>Registered At</th>
              </tr>
            </thead>
          <tbody>';

          foreach ($_SESSION['registrations'] as $i => $r) {
              $clubDisplay = $clubLabels[$r['club']] ?? htmlspecialchars($r['club']);
              echo 
                '<tr>
                  <td>' . ($i + 1) . '</td>
                  <td>' . htmlspecialchars($r['name']) . '</td>
                  <td>' . htmlspecialchars($r['email']) . '</td>
                  <td>' . $clubDisplay . '</td>
                  <td>'. htmlspecialchars($r['date']) . '</td>
                </tr>';
          }

        echo '</tbody>
          </table>
          <a class="back-button" href="index.html">New Registration</a>
        </div>
      </body>
      </html>';
    exit;
}

// non-POST visitors: redirect back
header('Location: index.html');
exit;
?>

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
