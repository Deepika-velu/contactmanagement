<?php
include 'db.php';

$message = "";

// ADD CONTACT
if (isset($_POST['add'])) {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    $stmt = $conn->prepare(
        "INSERT INTO contacts (name, email, phone, address)
         VALUES (?, ?, ?, ?)"
    );

    $stmt->bind_param("ssss", $name, $email, $phone, $address);

    if ($stmt->execute()) {
        $message = "Contact added successfully!";
    } else {
        $message = "Error adding contact!";
    }

    $stmt->close();
}

// UPDATE CONTACT
if (isset($_POST['update'])) {

    $id = (int)$_POST['id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    $stmt = $conn->prepare(
        "UPDATE contacts
         SET name=?, email=?, phone=?, address=?
         WHERE id=?"
    );

    $stmt->bind_param(
        "ssssi",
        $name,
        $email,
        $phone,
        $address,
        $id
    );

    if ($stmt->execute()) {
        $message = "Contact updated successfully!";
    } else {
        $message = "Error updating contact!";
    }

    $stmt->close();
}

// DELETE CONTACT
if (isset($_GET['delete'])) {

    $id = (int)$_GET['delete'];

    $stmt = $conn->prepare(
        "DELETE FROM contacts WHERE id=?"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: index.php?deleted=1");
    exit();
}

if (isset($_GET['deleted'])) {
    $message = "Contact deleted successfully!";
}

// EDIT CONTACT
$editContact = null;

if (isset($_GET['edit'])) {

    $id = (int)$_GET['edit'];

    $stmt = $conn->prepare(
        "SELECT * FROM contacts WHERE id=?"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $result = $stmt->get_result();
    $editContact = $result->fetch_assoc();

    $stmt->close();
}

// VIEW CONTACTS
$result = $conn->query(
    "SELECT * FROM contacts ORDER BY id DESC"
);

function e($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Contact Management System</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>

<header>
    <h1>Contact Management System</h1>
    <p>Manage your contacts easily</p>
</header>

<div class="container">

    <div class="form-box">

        <h2>
            <?php echo $editContact ? "Edit Contact" : "Add New Contact"; ?>
        </h2>

        <p class="message">
            <?php echo e($message); ?>
        </p>

        <form method="POST" onsubmit="return validateForm()">

            <?php if ($editContact) { ?>

                <input type="hidden" name="id"
                       value="<?php echo e($editContact['id']); ?>">

            <?php } ?>

            <input type="text" name="name"
                   placeholder="Enter Name"
                   value="<?php echo e($editContact['name'] ?? ''); ?>"
                   required>

            <input type="email" name="email"
                   placeholder="Enter Email"
                   value="<?php echo e($editContact['email'] ?? ''); ?>"
                   required>

            <input type="tel" name="phone"
                   placeholder="Enter Phone Number"
                   value="<?php echo e($editContact['phone'] ?? ''); ?>"
                   required>

            <textarea name="address"
                      placeholder="Enter Address"
                      required><?php echo e($editContact['address'] ?? ''); ?></textarea>

            <?php if ($editContact) { ?>

                <button type="submit" name="update">
                    Update Contact
                </button>

                <a href="index.php" class="cancel">Cancel</a>

            <?php } else { ?>

                <button type="submit" name="add">
                    Add Contact
                </button>

            <?php } ?>

        </form>

    </div>

    <div class="table-box">

        <h2>Contact List</h2>

        <table>

            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Actions</th>
            </tr>

            <?php if ($result->num_rows > 0) { ?>

                <?php while ($row = $result->fetch_assoc()) { ?>

                    <tr>

                        <td><?php echo e($row['id']); ?></td>

                        <td><?php echo e($row['name']); ?></td>

                        <td><?php echo e($row['email']); ?></td>

                        <td><?php echo e($row['phone']); ?></td>

                        <td><?php echo e($row['address']); ?></td>

                        <td>
                            <a class="edit"
                               href="index.php?edit=<?php echo $row['id']; ?>">
                               Edit
                            </a>

                            <a class="delete"
                               href="index.php?delete=<?php echo $row['id']; ?>"
                               onclick="return confirm('Are you sure you want to delete this contact?')">
                               Delete
                            </a>
                        </td>

                    </tr>

                <?php } ?>

            <?php } else { ?>

                <tr>
                    <td colspan="6">No contacts found.</td>
                </tr>

            <?php } ?>

        </table>

    </div>

</div>

<footer>
    <p>© 2026 Contact Management System</p>
</footer>

<script src="script.js"></script>

</body>
</html>