<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
session_start();

if(!isset($_SESSION['user_id'])){ header('Location: auth.php'); exit; }

$conn = new mysqli('localhost','root','','cjsa_files');
if($conn->connect_error) die("DB failed: ".$conn->connect_error);

$user_id = $_SESSION['user_id'];
$folder_id = isset($_GET['folder']) ? (int)$_GET['folder'] : null;

// CREATE FOLDER
if(isset($_POST['create_folder'])){
    $name = $conn->real_escape_string($_POST['folder_name']);
    $conn->query("INSERT INTO folders (user_id,parent_id,name) VALUES ($user_id,".($folder_id ?? "NULL").",'$name')");
    header("Location: dashboard.php".($folder_id?"?folder=$folder_id":"")); exit;
}

// DELETE FOLDER
if(isset($_GET['delete_folder'])){
    $del_id = (int)$_GET['delete_folder'];
    $conn->query("DELETE FROM folders WHERE id=$del_id AND user_id=$user_id");
    header("Location: dashboard.php".($folder_id?"?folder=$folder_id":"")); exit;
}

// FETCH FOLDERS
$folders = $conn->query("SELECT * FROM folders WHERE user_id=$user_id AND parent_id ".($folder_id?"=$folder_id":"IS NULL")." ORDER BY name ASC");

// Parent folder for back button
$parent_id = null;
if($folder_id){
    $parent_folder = $conn->query("SELECT parent_id FROM folders WHERE id=$folder_id")->fetch_assoc();
    $parent_id = $parent_folder['parent_id'] ?? null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Folder Manager</title>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
<style>
body {
    background-color: #000;
    font-family: Arial, sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

.container {
    background-color: #1f2937; /* dark gray box */
    padding: 40px;
    border-radius: 12px;
    width: 90%;
    max-width: 900px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    color: #fff;
    text-align: center;
}

h2 {
    font-size: 2rem;
    margin-bottom: 25px;
    font-weight: bold;
}

input[type="text"] {
    padding: 10px;
    border-radius: 8px;
    border: none;
    width: 200px;
    margin-right: 10px;
}

button {
    padding: 10px 15px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
    transition: all 0.2s;
}

button.create {
    background-color: #facc15;
    color: #000;
}

button.create:hover {
    background-color: #eab308;
}

a.back-btn {
    background-color: #6b7280;
    color: #fff;
    padding: 10px 15px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
}

a.back-btn:hover {
    background-color: #4b5563;
}

.folder-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
    margin-top: 30px;
}

.folder-item {
    background-color: #374151;
    padding: 15px;
    border-radius: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    transition: all 0.2s;
}

.folder-item:hover {
    background-color: #4b5563;
    transform: translateY(-3px);
}

.folder-item span {
    display: flex;
    align-items: center;
    gap: 5px;
}

.delete-btn {
    background-color: #dc2626;
    padding: 5px 8px;
    border-radius: 6px;
    color: #fff;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: bold;
    transition: all 0.2s;
}

.delete-btn:hover {
    background-color: #b91c1c;
}

.logout-btn {
    background-color: #dc2626;
    padding: 10px 20px;
    border-radius: 8px;
    color: #fff;
    text-decoration: none;
    font-weight: bold;
    display: inline-block;
    margin-top: 30px;
    transition: all 0.2s;
}

.logout-btn:hover {
    background-color: #b91c1c;
}
</style>
<script>
function openFolder(folderId){
    window.location.href = "dashboard.php?folder=" + folderId;
}
</script>
</head>
<body>
<div class="container">
    <h2>Folder Manager</h2>

    <!-- Controls -->
    <div>
        <form method="POST" class="inline-flex">
            <input type="text" name="folder_name" placeholder="New Folder" required>
            <button type="submit" name="create_folder" class="create">Create</button>
        </form>
        <?php if($folder_id): ?>
            <a href="dashboard.php<?= $parent_id ? "?folder=$parent_id" : "" ?>" class="back-btn ml-2">Back</a>
        <?php endif; ?>
    </div>

    <!-- Folders -->
    <div class="folder-grid">
        <?php while($folder = $folders->fetch_assoc()): ?>
            <div class="folder-item" ondblclick="openFolder(<?= $folder['id'] ?>)">
                <span>📁 <?= htmlspecialchars($folder['name']) ?></span>
                <a href="?delete_folder=<?= $folder['id'] ?>" onclick="return confirm('Delete folder?')" class="delete-btn">Delete</a>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Logout -->
    <a href="logout.php" class="logout-btn">Logout</a>
</div>
</body>
</html>
