<?php
require_once 'config/database.php';

echo "<h2>DailyBite Database Setup</h2>";

// Create demo admin user
$admin_username = 'admin';
$admin_password = password_hash('password123', PASSWORD_DEFAULT);
$admin_email = 'admin@dailybite.com';

// Check if admin already exists
$check_admin = $conn->query("SELECT COUNT(*) as count FROM admin_users WHERE username = '$admin_username'");
$admin_exists = $check_admin->fetch_assoc()['count'] > 0;

if (!$admin_exists) {
    $insert_admin = "INSERT INTO admin_users (username, password, email) VALUES ('$admin_username', '$admin_password', '$admin_email')";
    if ($conn->query($insert_admin)) {
        echo "<p>✅ Admin user created successfully</p>";
        echo "<p><strong>Username:</strong> admin</p>";
        echo "<p><strong>Password:</strong> password123</p>";
    } else {
        echo "<p>❌ Error creating admin user</p>";
    }
} else {
    echo "<p>✅ Admin user already exists</p>";
}

// Add demo meals
$demo_meals = [
    [
        'name' => 'Grilled Salmon & Quinoa',
        'description' => 'Fresh grilled salmon fillet served with fluffy quinoa and steamed vegetables.',
        'diet_type' => 'High Protein',
        'calories' => 450,
        'protein' => 35,
        'carbs' => 42,
        'fat' => 12,
        'image_url' => 'https://via.placeholder.com/300?text=Salmon+Bowl',
        'price' => 14.99
    ],
    [
        'name' => 'Vegan Buddha Bowl',
        'description' => 'Colorful mix of chickpeas, kale, sweet potato, and tahini dressing.',
        'diet_type' => 'Vegan',
        'calories' => 380,
        'protein' => 18,
        'carbs' => 52,
        'fat' => 14,
        'image_url' => 'https://via.placeholder.com/300?text=Buddha+Bowl',
        'price' => 12.99
    ],
    [
        'name' => 'Keto Chicken Salad',
        'description' => 'Succulent grilled chicken over mixed greens with avocado and cheese.',
        'diet_type' => 'Keto',
        'calories' => 320,
        'protein' => 40,
        'carbs' => 8,
        'fat' => 16,
        'image_url' => 'https://via.placeholder.com/300?text=Keto+Salad',
        'price' => 13.99
    ],
    [
        'name' => 'Mediterranean Wrap',
        'description' => 'Whole wheat wrap with hummus, feta, tomatoes, and Mediterranean vegetables.',
        'diet_type' => 'Vegetarian',
        'calories' => 420,
        'protein' => 16,
        'carbs' => 58,
        'fat' => 12,
        'image_url' => 'https://via.placeholder.com/300?text=Med+Wrap',
        'price' => 11.99
    ],
    [
        'name' => 'Protein Power Bowl',
        'description' => 'Brown rice, grilled chicken, broccoli, and peanut sauce - athlete approved!',
        'diet_type' => 'High Protein',
        'calories' => 520,
        'protein' => 45,
        'carbs' => 58,
        'fat' => 10,
        'image_url' => 'https://via.placeholder.com/300?text=Power+Bowl',
        'price' => 13.99
    ]
];

$meals_added = 0;
foreach ($demo_meals as $meal) {
    $check_meal = $conn->query("SELECT COUNT(*) as count FROM meals WHERE name = '" . sanitize($meal['name']) . "'");
    $meal_exists = $check_meal->fetch_assoc()['count'] > 0;

    if (!$meal_exists) {
        $insert_meal = "INSERT INTO meals (name, description, diet_type, calories, protein, carbs, fat, image_url, price) 
                        VALUES ('" . sanitize($meal['name']) . "', '" . sanitize($meal['description']) . "', '" . sanitize($meal['diet_type']) . "', 
                        {$meal['calories']}, {$meal['protein']}, {$meal['carbs']}, {$meal['fat']}, '{$meal['image_url']}', {$meal['price']})";
        
        if ($conn->query($insert_meal)) {
            $meals_added++;
        }
    }
}

echo "<p>✅ $meals_added demo meals added to database</p>";

// Add demo specials
$today = date('Y-m-d');
$week_start = date('Y-m-d', strtotime('Monday this week'));
$week_end = date('Y-m-d', strtotime('Sunday this week'));

// Get first 2 meals for specials
$meals = $conn->query("SELECT id FROM meals LIMIT 2");
$meal_ids = [];
while($row = $meals->fetch_assoc()) {
    $meal_ids[] = $row['id'];
}

$specials_added = 0;
if (count($meal_ids) > 0) {
    // Check if special already exists
    $check_special = $conn->query("SELECT COUNT(*) as count FROM weekly_specials WHERE week_start = '$week_start'");
    $specials_exist = $check_special->fetch_assoc()['count'] > 0;

    if (!$specials_exist) {
        foreach($meal_ids as $index => $meal_id) {
            $discount = $index == 0 ? 20 : 15;
            $insert_special = "INSERT INTO weekly_specials (meal_id, discount_percent, week_start, week_end) 
                               VALUES ($meal_id, $discount, '$week_start', '$week_end')";
            if ($conn->query($insert_special)) {
                $specials_added++;
            }
        }
    }
}

echo "<p>✅ $specials_added weekly specials created</p>";

echo "<hr>";
echo "<p style='color: green; font-weight: bold;'>Setup Complete! ✅</p>";
echo "<p><a href='admin/login.php'>Go to Admin Login</a> | <a href='index.php'>View Website</a></p>";
?>
