<?php
// process.php

session_start();

class RegistrationManager {
    private $dataFile;
    private $registrations;
    
    public function __construct() {
        $this->dataFile = __DIR__ . '/data/registrations.json';
        $this->loadRegistrations();
    }
    
    private function loadRegistrations() {
        if (file_exists($this->dataFile)) {
            $this->registrations = json_decode(file_get_contents($this->dataFile), true) ?? [];
        } else {
            $this->registrations = [];
        }
        // keep session in sync
        $_SESSION['registrations'] = $this->registrations;
    }
    
    public function saveRegistration(array $registration) {
        $this->registrations[] = $registration;
        $_SESSION['registrations'] = $this->registrations;
        
        if (!is_dir(dirname($this->dataFile))) {
            mkdir(dirname($this->dataFile), 0777, true);
        }
        
        file_put_contents($this->dataFile, json_encode($this->registrations, JSON_PRETTY_PRINT));
    }
    
    public function all() : array {
        return $this->registrations;
    }
    
    public function search(string $query) : array {
        if (trim($query) === '') return $this->registrations;
        $q = strtolower($query);
        return array_filter($this->registrations, function($reg) use ($q) {
            return strpos(strtolower($reg['name']), $q) !== false ||
                   strpos(strtolower($reg['email']), $q) !== false ||
                   strpos(strtolower($reg['club']), $q) !== false;
        });
    }
}

$manager = new RegistrationManager();

// club map (emoji + label) - values must match <option value="..."> in [index.html](http://_vscodecontentref_/0)
$clubLabels = [
    'programming' => "&#128187; Programming Club",
    'art'         => "&#x1F3A8; Art Club",
    'sports'      => "&#x26BD; Sports Club",
    'music'       => "&#127926; Music Club",
    'drama'       => "&#127916; Drama Club",
];
$allowedClubs = array_keys($clubLabels);

// helper: render table rows or a single "no results" message row
function render_table_rows(array $results, array $clubLabels, string $query = '') {
    if (empty($results)) {
        $msg = $query === '' 
            ? 'No registrations yet.' 
            : 'No registrations found for "' . htmlspecialchars($query, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '".';
        echo '<tr><td colspan="5" style="text-align:center;color:#666;padding:16px;">' . $msg . '</td></tr>';
        return;
    }

    foreach (array_values($results) as $i => $r) {
        $clubDisplay = $clubLabels[$r['club']] ?? htmlspecialchars($r['club']);
        echo '<tr>
                <td>' . ($i + 1) . '</td>
                <td>' . htmlspecialchars($r['name']) . '</td>
                <td>' . htmlspecialchars($r['email']) . '</td>
                <td>' . $clubDisplay . '</td>
                <td>' . htmlspecialchars($r['date']) . '</td>
              </tr>';
    }
}

// quick AJAX search response (returns table rows)
if (isset($_GET['search'])) {
    $q = trim((string)$_GET['search']);
    $results = $manager->search($q);
    render_table_rows($results, $clubLabels, $q);
    exit;
}

// ensure session registrations initialized
if (!isset($_SESSION['registrations'])) {
    $_SESSION['registrations'] = $manager->all();
}

$searchBox = '<div class="search-box"><input type="text" id="searchInput" placeholder="Search registrations..." onkeyup="searchRegistrations(this.value)"></div>';

$searchScript = '<script>
function searchRegistrations(query) {
    fetch("?search=" + encodeURIComponent(query))
        .then(response => response.text())
        .then(html => {
            const tbody = document.querySelector("tbody");
            if (tbody) tbody.innerHTML = html;
        })
        .catch(err => console.error("Search error:", err));
}
</script>';

// handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];

    $name  = trim($_POST['name']  ?? '');
    $email = trim($_POST['email'] ?? '');
    $club  = trim($_POST['club']  ?? '');

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
        // simple error page
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
                    background:#f4f4f4;
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
                    <div class="error"><ul>';
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

    // prepare registration and persist (session + file)
    $registration = [
        'name'  => $name,
        'email' => $email,
        'club'  => $club,
        'date'  => date('Y-m-d H:i:s')
    ];
    $manager->saveRegistration($registration);

    // render success + list (include search)
    $all = $manager->all();

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
                background:#f4f4f4
            }
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
                border:1px solid #6baadaff;
                color:#155724;
                padding:15px;
                border-radius:4px;
                margin-bottom:20px}
            table{
                width:100%;
                border-collapse:collapse;
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
                color:#fff;t
                ext-decoration:none;
                border-radius:4px;
                margin-top:20px
            }
            .back-button:hover{
                background:#2d1083
            }
            .search-box{
                margin:20px 0
            }
            .search-box input{
                width:100%;
                padding:10px;
                border:1px solid #ddd;
                border-radius:4px
            }
        </style>
            ' . $searchScript . '
    </head>
    <body>
        <div class="container">
            <div class="success">
                <h1>Registration Successful!</h1>
                <p><strong>Name:</strong> ' . htmlspecialchars($registration['name']) . '</p>
                <p><strong>Email:</strong> ' . htmlspecialchars($registration['email']) . '</p>
                <p><strong>Club:</strong> ' . ($clubLabels[$registration['club']] ?? htmlspecialchars($registration['club'])) . '</p>
            </div>';

    echo $searchBox;

    echo '<h2>All Registrations (' . count($all) . ')</h2>
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
        // render rows (handles empty result message)
                render_table_rows($all, $clubLabels);
                    echo 
            '</tbody>
        </table>
            <a class="back-button" href="index.html">New Registration</a>
        </div>
    </body>
    </html>';
    exit;
}

// non-POST visitors: redirect back to the form
header('Location: index.html');
exit;               
?>