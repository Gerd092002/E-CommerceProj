<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register - Adidadidadas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
      /* Custom gradient para sa header */
      .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
      /* Animation para sa card inig hover */
      .register-card { transition: all 0.3s ease; }
      .register-card:hover { transform: translateY(-5px); }
    </style>
  </head>
  <body class="bg-gray-50 min-h-screen">
    
    <header class="gradient-bg shadow-lg">
      <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center">
          <div class="flex items-center gap-3">
            <?php
              $logo_path = 'uploads/LOGO.png';
              // Check kung naa ba ang logo file sa folder
              if (file_exists($logo_path)) {
                echo '<img src="' . $logo_path . '" alt="Adidadidadas Logo" class="h-12 w-auto rounded-lg border-2 border-white shadow-lg">';
              } 
            ?>
            <h1 class="text-3xl md:text-4xl font-bold text-white">Adidadidadas</h1>
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

          <form action="register_process.php" method="post" class="space-y-6">
            
            <div>
              <label for="username" class="block text-gray-700 font-medium mb-2">
                <i class="fas fa-user-circle mr-2 text-purple-500"></i> Username
              </label>
              <div class="relative">
                <input type="text" id="username" name="username" required class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 bg-gray-50 text-gray-800 transition duration-300" placeholder="Choose a username" />
                <i class="fas fa-user absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
              </div>
            </div>

            <div>
              <label for="email" class="block text-gray-700 font-medium mb-2">
                <i class="fas fa-envelope mr-2 text-purple-500"></i> Email Address
              </label>
              <div class="relative">
                <input type="email" id="email" name="email" required class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 bg-gray-50 text-gray-800 transition duration-300" placeholder="Enter your email" />
                <i class="fas fa-at absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
              </div>
            </div>

            <div>
              <label for="password" class="block text-gray-700 font-medium mb-2">
                <i class="fas fa-lock mr-2 text-purple-500"></i> Password
              </label>
              <div class="relative">
                <input type="password" id="password" name="password" required class="w-full pl-12 pr-12 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 bg-gray-50 text-gray-800 transition duration-300" placeholder="Enter your password" />
                <i class="fas fa-key absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                
                <button type="button" onclick="togglePassword()" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-purple-600">
                  <i id="eyeIcon" class="fas fa-eye-slash"></i>
                </button>
              </div>
            </div>

            <div>
              <label class="block text-gray-700 font-medium mb-2">
                <i class="fas fa-user-tag mr-2 text-purple-500"></i> Account Type
              </label>
              <div class="relative">
                <input type="text" name="usertype" value="Customer" readonly 
                       class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl bg-gray-100 text-gray-500 cursor-not-allowed outline-none font-semibold transition duration-300" />
                <i class="fas fa-users absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
              </div>
              <p class="text-[10px] text-gray-400 mt-1 italic">* All new accounts are set to Customer by default.</p>
            </div>

            <button type="submit" name="btn_create_account" class="w-full bg-gradient-to-r from-purple-600 to-pink-500 text-white py-4 rounded-xl font-bold text-lg hover:from-purple-700 hover:to-pink-600 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
              <i class="fas fa-user-plus mr-2"></i> Create Account
            </button>
          </form>

          <div class="mt-8 pt-6 border-t border-gray-200 text-center">
            <p class="text-gray-600"> Already have an account?
              <a href="login.php" class="text-purple-600 hover:text-purple-800 font-bold ml-2"> Login here <i class="fas fa-arrow-right ml-1"></i> </a>
            </p>
          </div>
        </div>
      </div>
    </main>

    <footer class="bg-gray-900 text-white mt-16">
      <div class="container mx-auto px-4 py-8 text-center">
        <p class="text-gray-400">&copy; 2025 Adidadidadas. Premium footwear and apparel.</p>
        <div class="flex justify-center gap-6 mt-4">
          <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-facebook"></i></a>
          <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-instagram"></i></a>
          <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-twitter"></i></a>
        </div>
      </div>
    </footer>

    <script>
      function togglePassword() {
        const passwordInput = document.getElementById("password");
        const eyeIcon = document.getElementById("eyeIcon");
        
        if (passwordInput.type === "password") {
          // Ipakita ang password (text) ug ablihan ang mata
          passwordInput.type = "text";
          eyeIcon.classList.replace("fa-eye-slash", "fa-eye");
        } else {
          // Itago ang password ug sirad-an ang mata
          passwordInput.type = "password";
          eyeIcon.classList.replace("fa-eye", "fa-eye-slash");
        }
      }
    </script>
  </body>
</html>