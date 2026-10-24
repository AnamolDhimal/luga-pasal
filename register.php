<?php
require_once 'config/db.php';
require_once 'config/languages.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    if (empty($name) || empty($email) || empty($password) || empty($role)) {
        $error = "All required fields must be filled.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $phone, $hashedPassword, $role]);
            $userId = $pdo->lastInsertId();

            // Initialize vendor profile if the user is a seller or brand
            if ($role === 'brand' || $role === 'casual_seller') {
                $stmtVendor = $pdo->prepare("INSERT INTO vendor_profiles (vendor_id, store_name) VALUES (?, ?)");
                // Default store name to the user's name for now
                $stmtVendor->execute([$userId, $name . "'s Store"]);
            }

            header('Location: login.php?registered=1');
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = "Email already exists.";
            } else {
                $error = "An error occurred: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | Luga Pasal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brandLime: '#CCFF00',
                        matteBlack: '#141414',
                        offWhite: '#FAFAFA',
                        inputGray: '#EFEFEF',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { background-color: #FAFAFA; font-family: 'Inter', sans-serif; }
        .floating-label-group { position: relative; }
        .floating-label {
            position: absolute;
            left: 1rem;
            top: 1rem;
            transition: all 0.2s ease;
            pointer-events: none;
            color: #71717a;
            background-color: #EFEFEF;
            padding: 0 4px;
        }
        .floating-label-input:focus ~ .floating-label,
        .floating-label-input:not(:placeholder-shown) ~ .floating-label {
            top: -0.5rem;
            left: 0.75rem;
            font-size: 0.75rem;
            color: #0A0A0A;
            font-weight: 600;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-[380px] space-y-8">
        <div class="text-left">
            <h1 class="text-3xl font-bold text-matteBlack tracking-tight">Sign Up</h1>
        </div>

        <?php if (isset($error)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-6 text-sm rounded">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST" id="signupForm" class="space-y-6">
            <!-- Full Name -->
            <div class="floating-label-group">
                <input type="text" name="name" id="name" placeholder=" " required
                       class="floating-label-input w-full h-[56px] px-4 py-3 bg-inputGray rounded-xl outline-none focus:ring-2 focus:ring-matteBlack transition-all">
                <label class="floating-label">Full Name</label>
            </div>

            <!-- Email -->
            <div class="floating-label-group">
                <input type="email" name="email" id="email" placeholder=" " required
                       class="floating-label-input w-full h-[56px] px-4 py-3 bg-inputGray rounded-xl outline-none focus:ring-2 focus:ring-matteBlack transition-all">
                <label class="floating-label">Email Address</label>
            </div>

            <!-- Phone Number -->
            <div class="flex items-center bg-inputGray rounded-xl overflow-hidden h-[56px] outline-none focus-within:ring-2 focus:ring-matteBlack transition-all">
                <div class="flex items-center px-3 border-r border-gray-300 bg-gray-200 text-sm font-medium cursor-pointer">
                    <span>🇳🇵 +977</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
                <input type="text" name="phone" placeholder="Phone Number" required
                       class="w-full px-4 py-3 bg-transparent outline-none text-matteBlack">
            </div>

            <!-- Password -->
            <div class="floating-label-group relative">
                <input type="password" name="password" id="password" placeholder=" " required
                       class="floating-label-input w-full h-[56px] px-4 py-3 bg-inputGray rounded-xl outline-none focus:ring-2 focus:ring-matteBlack transition-all">
                <label class="floating-label">Password</label>
                <button type="button" id="togglePassword" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-matteBlack transition">
                    <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-6.064 7-9.542 7-4.478 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
            </div>

            <!-- Password Validation Checklist -->
            <div id="password-validation" class="space-y-2 px-1">
                <div class="flex items-center text-xs transition-all duration-300" id="rule-length">
                    <span class="mr-2 opacity-0">✅</span> <span class="text-gray-500">Must be between 8-30 characters</span>
                </div>
                <div class="flex items-center text-xs transition-all duration-300" id="rule-upper">
                    <span class="mr-2 opacity-0">✅</span> <span class="text-gray-500">Must contain at least one uppercase letter</span>
                </div>
                <div class="flex items-center text-xs transition-all duration-300" id="rule-lower">
                    <span class="mr-2 opacity-0">✅</span> <span class="text-gray-500">Must contain at least one lowercase letter</span>
                </div>
                <div class="flex items-center text-xs transition-all duration-300" id="rule-number">
                    <span class="mr-2 opacity-0">✅</span> <span class="text-gray-500">Must contain at least one number</span>
                </div>
                <div class="flex items-center text-xs transition-all duration-300" id="rule-special">
                    <span class="mr-2 opacity-0">✅</span> <span class="text-gray-500">Must contain at least one special character</span>
                </div>
            </div>

            <!-- Role Selection (Custom for Luga Pasal) -->
            <div class="space-y-2">
                <label class="text-sm font-medium text-gray-700">Account Type</label>
                <select name="role" required class="w-full h-[56px] px-4 py-3 bg-inputGray rounded-xl outline-none focus:ring-2 focus:ring-matteBlack transition-all appearance-none cursor-pointer">
                    <option value="buyer">Buyer</option>
                    <option value="casual_seller">Casual Seller</option>
                    <option value="brand">Brand Store</option>
                </select>
            </div>

            <!-- Terms -->
            <div class="flex items-start space-x-3 text-sm text-gray-600">
                <input type="checkbox" id="terms" required class="mt-1 w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <label for="terms">I have read and agree to the <a href="#" class="text-blue-600 font-bold hover:underline">Terms of use</a></label>
            </div>

            <!-- Submit -->
            <button type="submit" id="submitBtn" disabled class="w-full h-[56px] bg-gray-400 text-white font-semibold rounded-xl transition-all duration-300 disabled:cursor-not-allowed disabled:opacity-50">
                Sign up
            </button>
        </form>

        <footer class="text-center text-[10px] text-gray-400 leading-relaxed">
            This site is protected by reCAPTCHA and the <a href="#" class="text-blue-600">Google Privacy Policy</a> and <a href="#" class="text-blue-600">Terms of Service</a> apply.
        </footer>
    </div>

    <script>
        const passwordInput = document.getElementById('password');
        const togglePassword = document.getElementById('togglePassword');
        const termsCheckbox = document.getElementById('terms');
        const submitBtn = document.getElementById('submitBtn');
        const form = document.getElementById('signupForm');

        // Password Visibility Toggle
        togglePassword.addEventListener('click', () => {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
        });

        // Validation Rules
        const rules = {
            length: (val) => val.length >= 8 && val.length <= 30,
            upper: (val) => /[A-Z]/.test(val),
            lower: (val) => /[a-z]/.test(val),
            number: (val) => /[0-9]/.test(val),
            special: (val) => /[^A-Za-z0-9]/.test(val),
        };

        function validatePassword() {
            const val = passwordInput.value;
            let allValid = true;

            Object.keys(rules).forEach(rule => {
                const el = document.getElementById(`rule-${rule}`);
                const isValid = rules[rule](val);
                const checkmark = el.querySelector('span:first-child');
                const text = el.querySelector('span:last-child');

                if (isValid) {
                    checkmark.classList.remove('opacity-0');
                    text.classList.remove('text-gray-500');
                    text.classList.add('text-green-500');
                } else {
                    checkmark.classList.add('opacity-0');
                    text.classList.remove('text-green-500');
                    text.classList.add('text-gray-500');
                }
                if (!isValid) allValid = false;
            });

            return allValid;
        }

        function checkForm() {
            const isPasswordValid = validatePassword();
            const isTermsChecked = termsCheckbox.checked;
            const isNameFilled = document.getElementById('name').value.trim() !== '';
            const isEmailFilled = document.getElementById('email').value.trim() !== '';

            if (isPasswordValid && isTermsChecked && isNameFilled && isEmailFilled) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('bg-gray-400');
                submitBtn.classList.add('bg-matteBlack', 'hover:bg-gray-800');
            } else {
                submitBtn.disabled = true;
                submitBtn.classList.add('bg-gray-400');
                submitBtn.classList.remove('bg-matteBlack', 'hover:bg-gray-800');
            }
        }

        passwordInput.addEventListener('input', checkForm);
        termsCheckbox.addEventListener('change', checkForm);
        document.getElementById('name').addEventListener('input', checkForm);
        document.getElementById('email').addEventListener('input', checkForm);
    </script>
</body>
</html>
