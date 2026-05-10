<?php
session_start();
require_once '../config/database.php';

check_admin_login();

// Get meals for the select dropdown
$meals = $conn->query("SELECT id, name FROM meals ORDER BY name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Weekly Specials - DailyBite</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .sidebar {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            color: white;
            padding: 20px;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
        }
        .sidebar a:hover {
            background: rgba(255,255,255,0.2);
        }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h3 class="mb-4">🥗 DailyBite</h3>
    <h5>Admin Panel</h5>
    <hr class="bg-white">
    
    <a href="dashboard.php">📊 Dashboard</a>
    <a href="manage-meals.php">🍽️ Manage Meals</a>
    <a href="manage-orders.php">📦 Manage Orders</a>
    <a href="manage-specials.php" class="active">🔥 Weekly Specials</a>
    
    <hr class="bg-white">
    <p class="small">Welcome, <strong><?php echo $_SESSION['admin_username']; ?></strong></p>
    <a href="logout.php" class="btn btn-danger btn-sm w-100">Logout</a>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">
    <h1 class="mb-4">🔥 Manage Weekly Specials</h1>
    
    <!-- ADD NEW SPECIAL FORM -->
    <div class="card shadow mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">➕ Add Weekly Special</h5>
        </div>
        <div class="card-body">
            <form id="addSpecialForm">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Select Meal</label>
                        <select class="form-select" name="meal_id" required>
                            <option value="">Choose a meal...</option>
                            <?php while ($meal = $meals->fetch_assoc()): ?>
                                <option value="<?php echo $meal['id']; ?>"><?php echo $meal['name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Discount (%)</label>
                        <input type="number" class="form-control" name="discount_percent" min="1" max="100" required>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Week Start</label>
                        <input type="date" class="form-control" name="week_start" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Week End</label>
                        <input type="date" class="form-control" name="week_end" required>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-success">Add Special</button>
                <div id="addMessage" class="mt-3"></div>
            </form>
        </div>
    </div>

    <!-- SPECIALS LIST -->
    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">📋 Weekly Specials</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Meal</th>
                            <th>Discount</th>
                            <th>Week Start</th>
                            <th>Week End</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="specialsTable">
                        <!-- Populated by JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Load specials
async function loadSpecials() {
    try {
        const response = await fetch('../api/weekly-specials.php');
        const data = await response.json();
        
        if (data.success) {
            const tbody = document.getElementById('specialsTable');
            tbody.innerHTML = '';
            
            data.data.forEach(special => {
                const row = `
                    <tr>
                        <td>#${special.id}</td>
                        <td>${special.name}</td>
                        <td><span class="badge bg-danger">${special.discount_percent}%</span></td>
                        <td>${special.week_start}</td>
                        <td>${special.week_end}</td>
                        <td>
                            <button class="btn btn-sm btn-danger" onclick="deleteSpecial(${special.id})">Delete</button>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

// Add special
document.getElementById('addSpecialForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    data.meal_id = parseInt(data.meal_id);
    data.discount_percent = parseInt(data.discount_percent);
    
    try {
        const response = await fetch('../api/weekly-specials.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        const message = document.getElementById('addMessage');
        
        if (result.success) {
            message.innerHTML = '<div class="alert alert-success">Special added successfully!</div>';
            this.reset();
            loadSpecials();
            setTimeout(() => message.innerHTML = '', 3000);
        } else {
            message.innerHTML = `<div class="alert alert-danger">${result.message}</div>`;
        }
    } catch (error) {
        console.error('Error:', error);
    }
});

// Delete special
async function deleteSpecial(id) {
    if (confirm('Are you sure?')) {
        try {
            const response = await fetch('../api/weekly-specials.php', {
                method: 'DELETE',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({id})
            });
            
            const data = await response.json();
            if (data.success) {
                loadSpecials();
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }
}

// Initial load
loadSpecials();
</script>

</body>
</html>
