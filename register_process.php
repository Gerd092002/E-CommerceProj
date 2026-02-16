<?php
    // 1. KONEKSYON UG INITIALIZATION
    include 'connection.php';

    // I-check kung ang form gi-submit gamit ang POST method
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Pagkuha sa data gikan sa form
        $username = $_POST['username'];
        $email = $_POST['email'];
        // I-hash ang password para sa security (Importante kini para sa password_verify sa login)
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $usertype = $_POST['usertype'];

        // 2. PREPARED STATEMENT (Mas secure kaysa sa direkta nga query)
        // Gigamit ang '?' isip placeholders para malikayan ang SQL Injection
        $sql = "INSERT INTO tbl_user (userName, email, userPassword, userType) VALUES (?, ?, ?, ?)";
        
        if ($stmt = $conn->prepare($sql)) {
            // I-bind ang mga variables sa placeholders ("ssss" nagpasabot nga 4 ka strings)
            $stmt->bind_param("ssss", $username, $email, $password, $usertype);
            
            if ($stmt->execute()) {
                // Kon malampuson, ipadala ang user sa login page (siguroha nga .php kini kon naay logic)
                header("Location: index.php"); 
                exit();
            } else {
                echo "Error sa pag-execute: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error sa pag-prepare: " . $conn->error;
        }

        $conn->close();
    } 
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register - Adidadidadas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
      .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
      .register-card { transition: all 0.3s ease; }
      .register-card:hover { transform: translateY(-5px); }
    </style>
  </head>
  <body class="bg-gray-50 min-h-screen">
    
    <header class="gradient-bg shadow-lg">
      <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center">
          <div class="flex items-center gap-3">
            <h1 class="text-3xl font-bold text-white">Adidadidadas</h1>
          </div>
          <a href="index.php" class="text-white hover:text-gray-200 font-medium flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Back to Store
          </a>
        </div>
      </div>
    </header>

    <main class="container mx-auto px-4 py-12">
      <div class="max-w-md mx-auto">
        <div class="text-center mb-12">
          <h2 class="text-4xl font-bold text-gray-800 mb-4"> Join <span class="text-purple-600">Adidadidadas</span> </h2>
          <p class="text-gray-600">Create your account to start shopping</p>
        </div>

        <div class="register-card bg-white rounded-2xl shadow-xl p-8 w-full border border-gray-100">
          <div class="flex items-center gap-3 mb-8">
            <div class="bg-purple-100 p-3 rounded-full">
              <i class="fas fa-user-plus text-purple-600 text-2xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-800">Create Account</h3>
          </div>

          <form action="" method="post" class="space-y-6">
            <div>
              <label for="username" class="block text-gray-700 font-medium mb-2">
                <i class="fas fa-user-circle mr-2 text-purple-500"></i> Username
              </label>
              <div class="relative">
                <input type="text" id="username" name="username" required class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 bg-gray-50 text-gray-800 transition" placeholder="Choose a username" />
                <i class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
              </div>
            </div>

            <div>
              <label for="email" class="block text-gray-700 font-medium mb-2">
                <i class="fas fa-envelope mr-2 text-purple-500"></i> Email Address
              </label>
              <div class="relative">
                <input type="email" id="email" name="email" required class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 bg-gray-50 text-gray-800 transition" placeholder="Enter your email" />
                <i class="fas fa-at absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
              </div>
            </div>

            <div>
              <label for="password" class="block text-gray-700 font-medium mb-2">
                <i class="fas fa-lock mr-2 text-purple-500"></i> Password
              </label>
              <div class="relative">
                <input type="password" id="password" name="password" required class="w-full pl-12 pr-12 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 bg-gray-50 text-gray-800 transition" placeholder="Enter your password" />
                <i class="fas fa-key absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <button type="button" onclick="togglePassword()" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-purple-600">
                  <i id="eyeIcon" class="fas fa-eye-slash"></i>
                </button>
              </div>
            </div>

            <div>
              <label for="usertype" class="block text-gray-700 font-medium mb-2">
                <i class="fas fa-user-tag mr-2 text-purple-500"></i> Account Type
              </label>
              <div class="relative">
                <select name="usertype" id="usertype" required class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 bg-gray-50 text-gray-800 appearance-none">
                  <option value="">Select account type</option>
                  <option value="Admin">Admin</option>
                  <option value="Staff">Staff</option>
                  <option value="Customer">Customer</option>
                </select>
                <i class="fas fa-users absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
              </div>
            </div>

            <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-pink-500 text-white py-3 rounded-xl font-bold text-lg hover:shadow-lg transition transform hover:-translate-y-1">
              <i class="fas fa-user-plus mr-2"></i> Create Account
            </button>
          </form>

          <div class="mt-8 pt-6 border-t border-gray-200 text-center">
            <p class="text-gray-600"> Already have an account?
              <a href="index.php" class="text-purple-600 hover:text-purple-800 font-bold ml-2"> Login here <i class="fas fa-arrow-right ml-1"></i> </a>
            </p>
          </div>
        </div>
      </div>
    </main>

    <footer class="bg-gray-900 text-white mt-16">
      <div class="container mx-auto px-4 py-8 text-center">
        <p class="text-gray-400">&copy; 2025 Adidadidadas. Premium footwear and apparel.</p>
      </div>
    </footer>

    <script>
      // 3. JAVASCRIPT TOGGLE: Sirado nga mata (eye-slash) ang default
      function togglePassword() {
        const passwordInput = document.getElementById("password");
        const eyeIcon = document.getElementById("eyeIcon");

        if (passwordInput.type === "password") {
          passwordInput.type = "text";
          eyeIcon.classList.replace("fa-eye-slash", "fa-eye");
        } else {
          passwordInput.type = "password";
          eyeIcon.classList.replace("fa-eye", "fa-eye-slash");
        }
      }
    </script>
  </body>
</html>