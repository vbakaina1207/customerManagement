<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $company_id = $_POST['company_id'];
    $company_name = $_POST['company_name'];
    $contact_person = $_POST['contact_person'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $stmt = $conn->prepare("UPDATE clients SET company_name = :company_name, contact_person = :contact_person, phone = :phone, address = :address, edited_at = NOW() WHERE company_id = :company_id and created_by = :user_id");
    $stmt->bindParam(':company_name', $company_name);
    $stmt->bindParam(':contact_person', $contact_person);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':company_id', $company_id);
    $stmt->bindParam(':user_id', $user_id);

    if ($stmt->execute()) {
        header('Location: clients.php');
    } else {
        echo "Client editing error.";
    }
}

$company_id = $_GET['id'] ?? '';
$stmt = $conn->prepare("SELECT * FROM clients WHERE company_id = :company_id AND (created_by = :user_id OR :user_id = 1)");
$stmt->bindParam(':company_id', $company_id);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$client = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$client) {
    header('Location: clients.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit client</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php require '../nav.html'; ?>
<div class="container mt-5 mb-5">
    <h2>Edit a client</h2>
    <form action="edit_client.php" method="post" class="mb-3 mt-3">        
        <input type="hidden" name="company_id" class="form-control" value="<?php echo $client['company_id']; ?>">           
        <div class="form-group">   
            <label>Company name:</label>
            <input type="text" name="company_name" class="form-control" value="<?php echo $client['company_name']; ?>" required><br>        
        </div>
        <div class="form-group">  
            <label>The contact person:</label> 
            <input type="text" name="contact_person" class="form-control" value="<?php echo $client['contact_person']; ?>" required><br>            
        </div>    
        <div class="form-group">  
            <label>Telephone:</label>             
            <input type="text" name="phone" class="form-control" value="<?php echo $client['phone']; ?>" required><br>
        </div>
        <div class="form-group">
            <label>Address:</label> 
            <textarea name="address" class="form-control" required><?php echo $client['address']; ?></textarea><br>
        </div>        
        <input type="submit" class="btn btn-primary" value="Save changes">           
</form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>

