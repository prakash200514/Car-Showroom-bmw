<?php
session_start();
include 'partials/header.php';
?>

<div style="background: url('https://images.unsplash.com/photo-1542362567-b07e54358753?auto=format&fit=crop&w=1920&q=80') center/cover no-repeat; padding: 100px 20px; text-align: center; color: white; position: relative;">
    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7);"></div>
    <div style="position: relative; z-index: 1; max-width: 800px; margin: 0 auto;">
        <h1 style="font-size: 3.5rem; font-weight: 900; margin-bottom: 20px; text-transform: uppercase;">Contact Us</h1>
        <p style="font-size: 1.2rem; line-height: 1.6; color: #ccc;">Get in touch with our expert team for any inquiries, test drive bookings, or service appointments.</p>
    </div>
</div>

<div class="container" style="max-width: 1200px; margin: 60px auto; padding: 0 20px;">
    <div class="grid grid-2" style="gap: 50px;">
        
        <!-- Contact Form -->
        <div style="background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: 1px solid #eee;">
            <h2 style="font-size: 1.8rem; margin-bottom: 25px; color: #111;">Send us a message</h2>
            <form action="" method="POST">
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; color: #555; font-weight: 600;">Full Name</label>
                    <input type="text" name="name" required style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 6px; outline: none; transition: border-color 0.3s;">
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; color: #555; font-weight: 600;">Email Address</label>
                    <input type="email" name="email" required style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 6px; outline: none; transition: border-color 0.3s;">
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; color: #555; font-weight: 600;">Phone Number</label>
                    <input type="text" name="phone" style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 6px; outline: none; transition: border-color 0.3s;">
                </div>
                <div style="margin-bottom: 25px;">
                    <label style="display: block; margin-bottom: 8px; color: #555; font-weight: 600;">Message</label>
                    <textarea name="message" rows="5" required style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 6px; outline: none; transition: border-color 0.3s; resize: vertical;"></textarea>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 14px; font-size: 1.1rem;">Send Message</button>
            </form>
        </div>

        <!-- Contact Info & Map -->
        <div>
            <div style="margin-bottom: 40px;">
                <h2 style="font-size: 1.8rem; margin-bottom: 25px; color: #111;">Showroom Details</h2>
                
                <div style="display: flex; align-items: flex-start; margin-bottom: 20px;">
                    <div style="background: var(--bmw-blue); color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px; flex-shrink: 0;">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div>
                        <h4 style="margin: 0 0 5px 0; font-size: 1.1rem;">Address</h4>
                        <p style="margin: 0; color: #666; line-height: 1.6;">
                            7/2234 a1 mainroad thimmarajapuram,<br>
                            Palayamkottai, Tirunelveli,<br>
                            Tamil Nadu, India
                        </p>
                    </div>
                </div>

                <div style="display: flex; align-items: flex-start; margin-bottom: 20px;">
                    <div style="background: var(--bmw-blue); color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px; flex-shrink: 0;">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <div>
                        <h4 style="margin: 0 0 5px 0; font-size: 1.1rem;">Phone</h4>
                        <p style="margin: 0; color: #666; line-height: 1.6;">+91 1800-BMW-CARE</p>
                    </div>
                </div>

                <div style="display: flex; align-items: flex-start;">
                    <div style="background: var(--bmw-blue); color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px; flex-shrink: 0;">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div>
                        <h4 style="margin: 0 0 5px 0; font-size: 1.1rem;">Email</h4>
                        <p style="margin: 0; color: #666; line-height: 1.6;">contact@bmwshowroom.com</p>
                    </div>
                </div>
            </div>

            <!-- Google Map -->
            <div style="border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1); height: 350px;">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3943.821!2d77.728!3d8.718!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3b0411ed026bd6ad%3A0xc6ca70a9af0ba550!2sPalayamkottai%2C%20Tirunelveli%2C%20Tamil%20Nadu!5e0!3m2!1sen!2sin!4v1690000000000!5m2!1sen!2sin" 
                    width="100%" 
                    height="100%" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>

    </div>
</div>

<style>
    input:focus, textarea:focus {
        border-color: var(--bmw-blue) !important;
        box-shadow: 0 0 0 3px rgba(28, 107, 186, 0.1);
    }
</style>

<?php include 'partials/footer.php'; ?>
