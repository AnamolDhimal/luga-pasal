<?php
session_start();
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $stmt = $pdo->prepare("SELECT user_id, name, password, role FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            header('Location: index.php');
            exit;
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>
<?php include 'includes/header.php'; ?>
<div class="max-w-md mx-auto mt-16 p-8 bg-white rounded-xl shadow-lg border border-gray-100">
    <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">Welcome Back</h2>

    <?php if (isset($_GET['registered'])): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-3 mb-6 text-sm">
            Account created successfully! Please login.
        </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-6 text-sm">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form action="login.php" method="POST" class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
            <input type="email" name="email" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input type="password" name="password" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
        <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-md font-bold hover:bg-blue-700 transition-colors duration-200 shadow-md">Login</button>
    </form>
    <p class="text-center text-gray-600 mt-6 text-sm">
        Don't have an account? <a href="register.php" class="text-blue-600 font-semibold hover:underline">Register here</a>
    </p>
</div>
<?php include 'includes/footer.php'; ?>
