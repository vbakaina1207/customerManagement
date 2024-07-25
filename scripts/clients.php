<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Adding a new client
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['submit'])) {
    $company_name = $_POST['company_name'];
    $contact_person = $_POST['contact_person'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $stmt = $conn->prepare("INSERT INTO clients (company_name, contact_person, phone, address, created_by) VALUES (:company_name, :contact_person, :phone, :address, :created_by)");
    $stmt->bindParam(':company_name', $company_name);
    $stmt->bindParam(':contact_person', $contact_person);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':created_by', $user_id);

    if ($stmt->execute()) {
        echo '<div class="alert alert-success">Client added.</div>';
    } else {
        echo '<div class="alert alert-danger">Error adding client.</div>';
    }
    $_POST['submit'] = true;
}

// Getting clients

$stmt = $conn->prepare("SELECT distinct c.company_id, c.company_name, c.contact_person, c.phone, c.address, c.created_at, c.edited_at, u.name AS created_by,  u.user_id FROM clients c INNER JOIN users u ON u.user_id = c.created_by ORDER BY u.user_id");

$stmt->execute();
$clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client management</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../style/style.css">
</head>
<body>
<?php require '../nav.html'; ?>
<?php if(isset($_SESSION['user_id'])): 
     echo '<div class="alert alert-danger">Welcome ' . $_SESSION['name'] . '!</div>';
    endif;    
?>

<div class="container mt-5 mb-5">
    <h2>Customer Review</h2>
    <table class="table table-striped table-hover table-responsive">
        <thead>
            <tr>
                <th>Company</th>
                 <th>Contact person</th>
                 <th>Phone</th>
                 <th>Address</th>
                 <th>Date of creation</th>
                 <th>Date of editing</th>
                 <th>Created by</th>
                 <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clients as $client): ?>
                <tr>
                    <td><?php echo htmlspecialchars($client['company_name']); ?></td>
                    <td><?php echo htmlspecialchars($client['contact_person']); ?></td>
                    <td><?php echo htmlspecialchars($client['phone']); ?></td>
                    <td><?php echo htmlspecialchars($client['address']); ?></td>
                    <td><?php echo htmlspecialchars($client['created_at']); ?></td>                    
                    <td><?php echo htmlspecialchars($client['edited_at']); ?></td>
                    <td><?php echo htmlspecialchars($client['created_by']); ?></td>
                    <td>                        
                        <?php if ($client['user_id'] == $user_id): ?>
                            <a href="edit_client.php?id=<?php echo $client['company_id']; ?>" class="btn btn-sm btn-primary btn-edit m-1">Edit</a>
                            <a href="delete_client.php?id=<?php echo $client['company_id']; ?>" class="btn btn-sm btn-danger btn-edit m-1" onclick="return confirm('Are you sure?')">Delete</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Add a client</h2>
    <form action="clients.php" method="post" class="mb-3  mt-3">
        <div class="form-group">
            <label>Company name:</label>
            <input type="text" name="company_name" class="form-control" required>
        </div>
        <div class="form-group">
            <label>The contact person:</label>
            <input type="text" name="contact_person" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Telephone:</label>
            <input type="text" name="phone" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Address:</label>
            <textarea name="address" class="form-control" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Add a client</button>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
