<?php
require(__DIR__ ."/../models/Model.php");
require(__DIR__ ."/../connection/connection.php");

$data = [];
$data[0] = ["name" => "Sports", "description" => "Latest articles in current sporting events"];
$data[1] = ["name" => "Technology", "description" => "Discover the latest tech field innovations"];
$data[2] = ["name" => "Pop Culture", "description" => "Keep up with the latest pop culture news"];
$data[3] = ["name" => "Politics", "description" => "Latest local and global political news"];
$data[4] = ["name" => "Medical Field", "description" => "Get informed on the latest discoveries in the medical field"];

$sql = "INSERT INTO categories (name, description) VALUES (?, ?)";
$stmt = $conn->prepare($sql);

foreach ($data as $category) {
    $stmt->bind_param("ss", $category['name'], $category['description']);
    if ($stmt->execute()) {
        echo "Inserted category: "  . $category['name'] . "\n";
    } else {
        echo "Error";
    }
}

$stmt->close();
$conn->close();