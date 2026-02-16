<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome para sa mga icons -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
    <title>Login - Adidadidadas</title>
    <style>
      .gradient-bg {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      }
      .login-card {
        transition: all 0.3s ease;
      }
      .login-card:hover {
        transform: translateY(-5px);
      }
    </style>
  </head>
  <body class="bg-gray-50 min-h-screen">
    <!-- nag add Same header  -->
    <header class="gradient-bg shadow-lg">
      <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center">
          <div class="flex items-center gap-3">
            <h1 class="text-3xl font-bold text-white">Adidadidadas</h1>
          </div>

          <!-- Back to home button -->
          <a
            href="index.php"
            class="text-white hover:text-gray-200 font-medium flex items-center gap-2"
          >
            <i class="fas fa-arrow-left"></i>
            Back to Store
          </a>
        </div>
      </div>
    </header>

    <!-- Main Login Content -->
    <main class="container mx-auto px-4 py-12">
      <div class="max-w-md mx-auto">
        <!-- Page Header -->
        <div class="text-center mb-12">
          <h2 class="text-4xl font-bold text-gray-800 mb-4">
            Login to <span class="text-purple-600">Adidadidadas</span>
          </h2>
          <p class="text-gray-600">Sign in to access your account</p>
        </div>

        <!-- Login Form Card -->
        <div
          class="login-card bg-white rounded-2xl shadow-xl p-8 w-full border border-gray-100"
        >
          <div class="flex items-center gap-3 mb-8">
            <div class="bg-purple-100 p-3 rounded-full">
              <i class="fas fa-user text-purple-600 text-2xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-800">Account Login</h3>
          </div>

          <form action="login_process.php" method="post" class="space-y-6">
            <!-- Username Field -->
            <div>
              <label
                for="username"
                class="block text-gray-700 font-medium mb-2"
              >
                <i class="fas fa-user-circle mr-2 text-purple-500"></i>
                Username or Email
              </label>
              <div class="relative">
                <input
                  type="text"
                  id="username"
                  name="username"
                  required
                  class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-gray-50 text-gray-800 transition duration-300"
                  placeholder="Enter your username or email"
                />
                <i
                  class="fas fa-envelope absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"
                ></i>
              </div>
            </div>

            <!-- Password Field -->
            <div>
              <label
                for="password"
                class="block text-gray-700 font-medium mb-2"
              >
                <i class="fas fa-lock mr-2 text-purple-500"></i>
                Password
              </label>
              <div class="relative">
                <input
                  type="password"
                  id="password"
                  name="password"
                  required
                  class="w-full pl-12 pr-12 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-gray-50 text-gray-800 transition duration-300"
                  placeholder="Enter your password"
                />
                <i
                  class="fas fa-key absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"
                ></i>
                <button
                  type="button"
                  onclick="togglePassword()"
                  class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-purple-600"
                >
                  <i class="fas fa-eye"></i>
                </button>
              </div>
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex justify-between items-center">
              <label class="flex items-center space-x-2">
                <input
                  type="checkbox"
                  name="remember"
                  class="rounded text-purple-600 focus:ring-purple-500"
                />
                <span class="text-gray-700">Remember me</span>
              </label>
              <a
                href="#"
                class="text-purple-600 hover:text-purple-800 font-medium text-sm"
              >
                Forgot Password?
              </a>
            </div>

            <!-- Login Button -->
            <button
              type="submit"
              class="w-full bg-gradient-to-r from-purple-600 to-pink-500 text-white py-3 rounded-xl font-bold text-lg hover:from-purple-700 hover:to-pink-600 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1"
            >
              <i class="fas fa-sign-in-alt mr-2"></i>
              Login
            </button>
          </form>

          <!-- Register Link -->
          <div class="mt-8 pt-6 border-t border-gray-200 text-center">
            <p class="text-gray-600">
              Don't have an account?
              <a
                href="register.php"
                class="text-purple-600 hover:text-purple-800 font-bold ml-2"
              >
                Create Account <i class="fas fa-arrow-right ml-1"></i>
              </a>
            </p>
          </div>
        </div>
      </div>
    </main>

    <!-- nag add Same footer sa index.php -->
    <footer class="bg-gray-900 text-white mt-16">
      <div class="container mx-auto px-4 py-8">
        <div class="text-center">
          <p class="text-gray-400">
            &copy; 2025 Adidadidadas. Premium footwear and apparel.
          </p>
          <div class="flex justify-center gap-6 mt-4">
            <a href="#" class="text-gray-400 hover:text-white"
              ><i class="fab fa-facebook"></i
            ></a>
            <a href="#" class="text-gray-400 hover:text-white"
              ><i class="fab fa-instagram"></i
            ></a>
            <a href="#" class="text-gray-400 hover:text-white"
              ><i class="fab fa-twitter"></i
            ></a>
          </div>
        </div>
      </div>
    </footer>

    <script>
      // Password visibility toggle
      function togglePassword() {
        const passwordInput = document.getElementById("password");
        const eyeIcon = document.querySelector("#password + button i");

        if (passwordInput.type === "password") {
          passwordInput.type = "text";
          eyeIcon.classList.remove("fa-eye");
          eyeIcon.classList.add("fa-eye-slash");
        } else {
          passwordInput.type = "password";
          eyeIcon.classList.remove("fa-eye-slash");
          eyeIcon.classList.add("fa-eye");
        }
      }
    </script>
  </body>
</html>
