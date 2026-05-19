<?php 
// Check if this is first run (no admin configured)
$admin_file = 'config/admin.txt';
if(!file_exists($admin_file)){
    header("Location: admin/setup.php");
    exit();
}

include("includes/header.php"); 
?>

<section class="hero">

    <div class="hero-overlay">

        <h2>
            Welcome to Malibhu View Resort
        </h2>

        <p>
            Experience luxury resort booking
            with modern online reservation.
        </p>

        <div class="hero-buttons">

            <a href="user/login.php"
            class="btn-gold">

            Login

            </a>

            <a href="user/register.php"
            class="btn-gold">

            Register

            </a>

        </div>

    </div>

</section>

<section class="features">

    <h2>Why Choose Us?</h2>

    <div class="feature-grid">

        <div class="feature-card">

            <img src="images/gallery/luxury_pool.jpg" alt="Luxury Pool">

            <h3>Luxury Pool</h3>

            <p>
                Enjoy premium swimming pool
                experience.
            </p>

        </div>

        <div class="feature-card">

            <img src="images/gallery/event_venue.jpg" alt="Event Venue">

            <h3>Event Venue</h3>

            <p>
                Perfect for weddings and
                celebrations.
            </p>

        </div>

        <div class="feature-card">

            <img src="images/gallery/relaxing_resort.jpg" alt="Relaxing Resort">

            <h3>Relaxing Resort</h3>

            <p>
                Experience relaxing and modern
                resort ambiance.
            </p>

        </div>

    </div>

</section>

<?php include("includes/footer.php"); ?>