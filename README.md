Working on the Basic E-commerce Product Catalog with Checkout Cart was a fantastic 
deep dive into full-stack development with PHP and MySQL. We really focused on 
building something secure and performant from the ground up. One of the biggest 
lessons came from implementing the security layers. Making sure every single user input 
was properly sanitized was a real challenge, but it drove home how critical it is to guard 
against threats like SQL injection. It definitely reinforced our commitment to building 
robust defenses. 

For the user authentication, we used PHP's password_hash() with bcrypt for 
registration and password_verify() for logins. Handling those credentials securely wasn't 
just a checkbox—it made the standard practices for web security feel much more 
concrete. The project also gave us a chance to really think through database design. We 
mapped out an ERD to connect the main tables—users, products, orders, and 
customers—in our MySQL database. Getting those relationships right was key for the 
role-based access control, which smoothly separates what Admins, Staff, and 
Customers can see and do. 

We also kept performance in mind. By adding indexed keys and cutting down on 
repetitive queries, we made sure the dashboard remained responsive for everyone. And 
using TailwindCSS for the frontend helped us create a clean, consistent interface that 
works well on any device. All in all, this project sharpened our problem-solving skills, 
especially around weaving security and performance into the core build. It left us feeling 
confident in our ability to develop a functional, secure, and user-friendly system for 
handling e-commerce data and transactions.
