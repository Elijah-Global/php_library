<?php

use LibrarySystem\Entities\Book;
use LibrarySystem\Entities\Magazine;
use LibrarySystem\Entities\Member;
use LibrarySystem\Entities\Actions\Library;

require_once 'src/Entities/LibraryItem.php';
require_once 'src/Entities/Borrowable.php';
require_once 'src/Entities/Book.php';
require_once 'src/Entities/Magazine.php';
require_once 'src/Entities/Member.php';
require_once 'src/Entities/Actions/Borrowing.php';
require_once 'src/Entities/Actions/Library.php';

session_start();
// Initialize or restore library from session
if (!isset($_SESSION['library'])) {
    $library = new Library();
    $book1 = new Book("The Great Gatsby", "F. Scott Fitzgerald", "978-0743273565", 3);
    $book2 = new Book("1984", "George Orwell", "978-0451524935", 2);
    $book3 = new Book("To Kill a Mockingbird", "Harper Lee", "978-0446310789", 4);
    $library->addItem($book1);
    $library->addItem($book2);
    $library->addItem($book3);
    $magazine1 = new Magazine("National Geographic", "Various", "NG001", 123, 2);
    $magazine2 = new Magazine("Time", "Various", "TM001", 456, 3);
    $magazine3 = new Magazine("Scientific American", "Various", "SA001", 789, 1);
    $library->addItem($magazine1);
    $library->addItem($magazine2);
    $library->addItem($magazine3);
    $member1 = new Member("M001", "Ola Tunde", "ola@example.com");
    $member2 = new Member("M002", "Janet Joy", "janet@example.com");
    $member3 = new Member("M003", "Bola Johnson", "bola@example.com");
    $library->addMember($member1);
    $library->addMember($member2);
    $library->addMember($member3);
    $_SESSION['library'] = serialize($library);
} else {
    $library = unserialize($_SESSION['library']);
}

$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'add_book') {
            $book = new Book($_POST['title'], $_POST['author'], $_POST['isbn'], (int)$_POST['copies']);
            $library->addItem($book);
            $message = 'Book added successfully!';
        } elseif ($action === 'add_magazine') {
            $magazine = new Magazine($_POST['title'], $_POST['author'], $_POST['id'], (int)$_POST['issue'], (int)$_POST['copies']);
            $library->addItem($magazine);
            $message = 'Magazine added successfully!';
        } elseif ($action === 'add_member') {
            $member = new Member($_POST['member_id'], $_POST['name'], $_POST['email']);
            $library->addMember($member);
            $message = 'Member added successfully!';
        } elseif ($action === 'borrow_item') {
            if ($library->borrowItem($_POST['item_id'], $_POST['member_id'])) {
                $message = 'Item borrowed successfully!';
            } else {
                $error = 'Failed to borrow item. Check Item ID and Member ID, or item availability.';
            }
        } elseif ($action === 'return_item') {
            $index = (int)$_POST['borrowing_index'];
            $borrowings = array_values($library->getBorrowings() ?? []);
            if (isset($borrowings[$index])) {
                if ($library->returnItem($borrowings[$index])) {
                    $message = 'Item returned successfully!';
                } else {
                    $error = 'Failed to return item.';
                }
            } else {
                $error = 'Invalid borrowing index.';
            }
        }
        $_SESSION['library'] = serialize($library);
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PHP Library System</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f7f7f7; margin: 0; padding: 0; }
        .container { max-width: 900px; margin: 40px auto; background: #fff; padding: 30px 40px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);}
        h1 { color: #2c3e50; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th, td { padding: 10px 12px; border-bottom: 1px solid #eaeaea; }
        th { background: #f0f0f0; text-align: left; }
        .success { color: #27ae60; }
        .error { color: #c0392b; }
        .status-returned { color: #2980b9; }
        .status-borrowed { color: #e67e22; }
        .section { margin-bottom: 40px; }
        form input, form button { margin: 0 5px 10px 0; padding: 6px 10px; }
        form button { background: #2980b9; color: #fff; border: none; border-radius: 4px; cursor: pointer; }
        form button:hover { background: #1c5d8c; }
    </style>
</head>
<body>
<div class="container">
    <h1>PHP Library System</h1>
    <?php if ($message): ?>
        <div class="success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php
    // Borrowing Report
    echo '<div class="section"><h2>Borrowing Report</h2>';
    echo '<table><tr><th>#</th><th>Item</th><th>Borrowed By</th><th>Due Date</th><th>Status</th></tr>';
    $borrowings = method_exists($library, 'getBorrowings') ? $library->getBorrowings() : [];
    $i = 0;
    foreach ($borrowings as $borrowing) {
        $item = $borrowing->getItem();
        $member = $borrowing->getMember();
        $dueDate = $borrowing->getDueDate()->format('Y-m-d');
        $status = $borrowing->getReturnDate() ? "Returned" : "Borrowed";
        $statusClass = $status === "Returned" ? "status-returned" : "status-borrowed";
        echo "<tr><td>$i</td><td>" . htmlspecialchars($item->getDetails()) . "</td><td>" . htmlspecialchars($member->getName()) . "</td><td>" . htmlspecialchars($dueDate) . "</td><td class=\"$statusClass\">$status</td></tr>";
        $i++;
    }
    echo '</table></div>';
    // Library Items
    echo '<div class="section"><h2>All Library Items</h2>';
    echo '<table><tr><th>Details</th></tr>';
    foreach ($library->getAllItems() as $item) {
        echo '<tr><td>' . htmlspecialchars($item->getDetails()) . '</td></tr>';
    }
    echo '</table></div>';
    // Total items
    echo '<div class="section"><strong>Total items in library: ' . count($library->getAllItems()) . '</strong></div>';
    ?>
    <div class="section">
        <h2>Add New Book</h2>
        <form method="post" action="">
            <input type="hidden" name="action" value="add_book">
            <input type="text" name="title" placeholder="Title" required>
            <input type="text" name="author" placeholder="Author" required>
            <input type="text" name="isbn" placeholder="ISBN" required>
            <input type="number" name="copies" placeholder="Available Copies" min="1" required>
            <button type="submit">Add Book</button>
        </form>
    </div>
    <div class="section">
        <h2>Add New Magazine</h2>
        <form method="post" action="">
            <input type="hidden" name="action" value="add_magazine">
            <input type="text" name="title" placeholder="Title" required>
            <input type="text" name="author" placeholder="Author" required>
            <input type="text" name="id" placeholder="ID" required>
            <input type="number" name="issue" placeholder="Issue Number" min="1" required>
            <input type="number" name="copies" placeholder="Available Copies" min="1" required>
            <button type="submit">Add Magazine</button>
        </form>
    </div>
    <div class="section">
        <h2>Add New Member</h2>
        <form method="post" action="">
            <input type="hidden" name="action" value="add_member">
            <input type="text" name="member_id" placeholder="Member ID" required>
            <input type="text" name="name" placeholder="Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <button type="submit">Add Member</button>
        </form>
    </div>
    <div class="section">
        <h2>Borrow Item</h2>
        <form method="post" action="">
            <input type="hidden" name="action" value="borrow_item">
            <input type="text" name="item_id" placeholder="Item ID" required>
            <input type="text" name="member_id" placeholder="Member ID" required>
            <button type="submit">Borrow</button>
        </form>
    </div>
    <div class="section">
        <h2>Return Item</h2>
        <form method="post" action="">
            <input type="hidden" name="action" value="return_item">
            <input type="number" name="borrowing_index" placeholder="Borrowing Index (see # above)" min="0" required>
            <button type="submit">Return</button>
        </form>
    </div>
</div>
</body>
</html>