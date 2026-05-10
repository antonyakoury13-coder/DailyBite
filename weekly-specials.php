<?php
require_once 'config/database.php';

$today = date('Y-m-d');
$specials = $conn->query("
    SELECT ws.*, m.name, m.price, m.description, m.image_url
    FROM weekly_specials ws
    JOIN meals m ON ws.meal_id = m.id
    WHERE ws.week_start <= '$today' AND ws.week_end >= '$today'
    ORDER BY ws.discount_percent DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weekly Specials - DailyBite</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .special-card {
            border: none;
            border-radius: 10px;
            overflow: hidden;
            position: relative;
            transition: transform 0.3s;
        }
        .special-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .special-card img {
            height: 250px;
            object-fit: cover;
        }
        .discount-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: linear-gradient(135deg, #dc3545 0%, #ff6b6b 100%);
            padding: 10px 15px;
            border-radius: 50%;
            color: white;
            font-weight: bold;
            font-size: 1.2em;
            text-align: center;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .price-original {
            text-decoration: line-through;
            color: #999;
            font-size: 0.9em;
        }
        .price-new {
            color: #dc3545;
            font-size: 1.5em;
            font-weight: bold;
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">🥗 DailyBite</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="meals.php">Meals</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="weekly-specials.php">Weekly Specials</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin/login.php">Admin</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container py-5">
    <h1 class="text-center mb-5">
        🔥 This Week's Specials
    </h1>

    <?php if ($specials->num_rows == 0): ?>
        <div class="alert alert-info text-center">
            <h4>No specials this week</h4>
            <p>Check back soon for exciting deals!</p>
            <a href="meals.php" class="btn btn-success">Browse All Meals</a>
        </div>
    <?php else: ?>
        <div class="row">
            <?php while ($special = $specials->fetch_assoc()): 
                $discounted_price = $special['price'] * (1 - $special['discount_percent'] / 100);
                $you_save = $special['price'] - $discounted_price;
            ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card special-card shadow">
                    <div class="discount-badge">
                        -<?php echo $special['discount_percent']; ?>%
                    </div>
                    <img src="<?php echo $special['image_url']; ?>" class="card-img-top" alt="<?php echo $special['name']; ?>">
                    
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $special['name']; ?></h5>
                        <p class="card-text small text-muted"><?php echo substr($special['description'], 0, 100); ?>...</p>
                        
                        <div class="mb-3">
                            <p class="price-original">Regular: $<?php echo number_format($special['price'], 2); ?></p>
                            <p class="price-new">Now: $<?php echo number_format($discounted_price, 2); ?></p>
                            <p class="small text-success">You save: $<?php echo number_format($you_save, 2); ?></p>
                        </div>
                        
                        <p class="small text-muted">
                            Valid: <?php echo date('M d', strtotime($special['week_start'])); ?> - <?php echo date('M d', strtotime($special['week_end'])); ?>
                        </p>
                        
                        <a href="meal-details.php?id=<?php echo $special['meal_id']; ?>" class="btn btn-danger w-100">
                            Order Now 🛒
                        </a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>

    <div class="text-center mt-5">
        <a href="meals.php" class="btn btn-success btn-lg">← Back to All Meals</a>
    </div>
</div>

<!-- FOOTER -->
<footer class="bg-dark text-white text-center py-3 mt-5">
    © 2026 DailyBite - Healthy Restaurant Platform
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
