<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

$user_id = $_SESSION["user_id"];

// Fetch user details
$user_stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();
$username = $user["username"];

// Fetch transactions
$sql = "SELECT * FROM transactions WHERE user_id = ? ORDER BY date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Calculate balance
$balance = 0;
while ($row = $result->fetch_assoc()) {
    if ($row['type'] === 'credit') {
        $balance += $row['amount'];
    } else {
        $balance -= $row['amount'];
    }
}

// Re-run query for display
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard</title>
  <link rel="shortcut icon" href="assets\wallet.png" type="image/x-icon">
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css"
    rel="stylesheet"
    integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7"
    crossorigin="anonymous"
  />
  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq"
    crossorigin="anonymous"
  ></script>
  <style>
  body {
  font-family: Arial, sans-serif;
  margin: 0;
  padding: 0;
  min-height: 100dvh;
  width: 100dvw;
  display: flex;
  flex-direction: column;
  align-items: center;
  background: linear-gradient(135deg, rgb(0, 0, 0));
}

#navbar {
  height: fit-content;
  padding: 0.5rem;
  background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.99);
  align-items: center;
  z-index: 1000;
}

#main {
  width: 35%;
  background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
  padding: 2em;
  padding-top: 0.5rem;
  border-radius: 1em;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.99);
  display: flex;
  flex-direction: column;
  align-items: start;
  position: absolute;
  top: 25%;
  left: 5%;
  color: whitesmoke;
  z-index: 1;
}

#main form {
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: start;
  font-size: 1.2rem;
}

#main span {
  width: 100%;
  display: flex;
  justify-content:space-between;
  align-items:center;
  font-size: 1.2rem;
}

.history {
  width: 40%;
  background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
  padding: 2em;
  border-radius: 1em;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.99);
  display: flex;
  flex-direction: column;
  align-items: start;
  position: absolute;
  top: 25%;
  right: 5%;
  color: whitesmoke;
  overflow: visible;
}

.history .table-container {
  width: 100%;
  overflow-y: visible;
  overflow-x: hidden;
}

/* Table styling */
table {
  width: 100%;
  border-collapse: collapse;
  table-layout: fixed;
}

th, td {
  border: 1px solid #ddd;
  padding: 8px;
  color: white;
  background-color: rgba(255, 255, 255, 0.05);
  word-wrap: break-word;
}

th {
  position: sticky;
  top: 0;
  background-color: #203a43;
  z-index: 2;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  #main,
  .history {
    position: static;
    width: 90%;
    margin: 1rem 0;
  }

  #main {
    margin-top: 4.5rem; /* Push below fixed navbar */
  }

  .history .table-container {
    overflow-x: auto;
    overflow-y: auto;
    max-height: 400px;
  }

  table {
    min-width: 600px; /* Force horizontal scroll if content overflows */
  }
}

  </style>
</head>
<body>
  <nav class="navbar bg-body-tertiary fixed-top" id="navbar">
    <div class="container-fluid">
    <span Style="display:flex; color:whitesmoke;"><img src="assets\wallet.png" width="30" height="30"><h2>&nbspSpendWise</h2></span>
      
      <h4 class="text-light">
        <strong>Balance:</strong> ₹<?php echo number_format($balance, 2); ?>
      </h4>
    </div>
  </nav>

  <div id="main">
    <span class="w-100 mt-0 mb-3">
    <h5 class="text-light">
        
        <?php 
          $username = htmlspecialchars($username);
          echo ucfirst(strtolower($username)); 
        ?>
      </h5>
      <a href="logout.php" class="btn btn-danger">Logout</a>
    </span>
    <form id="transactionForm" method="post" action="add_transaction.php">
      <select name="type" id="type" class="form-select" required>
        <option value="credit">Credit</option>
        <option value="expense">Expense</option>
      </select><br />
      <label for="amount">Amount: </label>
      <input type="number" step="0.01" name="amount" required id="amount" class="my-3 form-control"><br />
      <label for="desp">Description: </label>
      <input type="text" name="description" id="desp" class="form-control my-3"><br />
      <button type="submit" class="btn btn-success w-100">Add</button>
    </form>
  </div>

  <div class="history">
    <h3>Transaction History</h3>
    <div class="table-container">
      <table border="1" cellpadding="5">
        <tr>
          <th>Date</th>
          <th>Time</th>
          <th>Type</th>
          <th>Amount</th>
          <th>Description</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <?php
          $datetime = new DateTime($row["date"]);
          $date = $datetime->format('Y-m-d');
          $time = $datetime->format('H:i');
        ?>
        <tr>
          <td><?php echo $date; ?></td>
          <td><?php echo $time; ?></td>
          <td><?php echo ucfirst($row["type"]); ?></td>
          <td><?php echo ($row["type"] === "expense" ? "-" : "+") . "₹" . $row["amount"]; ?></td>
          <td><?php echo $row["description"]; ?></td>
        </tr>
        <?php endwhile; ?>
      </table>
    </div>
  </div>
</body>
</html>
