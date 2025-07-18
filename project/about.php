<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Shalash Flat Rent</title>
  <link rel="stylesheet" href="css/style2.css">
</head>

<body>

  <?php 
    $act = 'about';
    include 'header.php'; 
  ?>

  <section class="layout">
    <?php include 'nav.php'; ?>

    <main class="main-content">
      <h2>About Shalash Flat Rentals</h2>

      <section>
        <h3>The Agency</h3>
        <figure class="company"><img src="images/company.jpg" alt="Shalash Flat Rentals"></figure>
        <p>Shalash Flat Rentals is a well-established real estate agency in the historic town of Birzeit,
          Palestine.<br> Shalash Flat Rentals was established in 2018 with the vision of simplifying the process of
          renting for tenants and landlords alike.<br> Through its diligence, Shalash Flat Rentals has since become a name that
          everyone knows in the local property <br> market, distinguished by transparency, reliability, and commitment to customer satisfaction.
        </p>
        
        <h4>History and Recognition</h4>
        <p>
          With humble beginnings, the agency expanded services to include not only Birzeit but surrounding towns as well.<br>
          It has received several local awards for service excellence, <strong>including:</strong></P>
        <ul>
          <li>Best Local Rental Service – 2022 (Birzeit Business Awards)</li>
          <li>Customer Trust Seal – 2023 (Palestine Real Estate Union)</li>
        </ul>

        <h4>Management Hierarchy </h4>
        <ul>
          <li>Founder & CEO: Mohamad Shalash</li>
          <li>Operations Manager: Qusai Shalash</li>
          <li>Customer Service Head: Maysam Rami</li>
          <li>IT & Support Lead: Abed Thabet</li>
        </ul>

      </section>

      <section>
        <h3>The City : Birzeit</h3>
        <p>
          Birzeit is small but vibrant Palestinian town in the central West Bank, north of Ramallah.<br>
          It is famous for its rich cultural legacy and educational significance with the globally famous Birzeit University.
        </p>
        <h4>General Facts</h4>
        <ul>
          <li>Population: Approximately 7,000 residents</li>
          <li>Location: ~10 km north of Ramallah</li>
          <li>Weather: Mediterranean climate — hot dry summers and cool rainy winters</li>
          <li>Famous For: 
            <ul>
              <li>Birzeit University - one of the oldest and most prestigious universities</li>
              <li>Production of olive oil and soap</li>
              <li>Palestinian architecture and hospitality</li>
              <li>Annual festivals and cultural events</li>
            </ul> 
          </li>
        </ul>
        <h4>People of Note</h4>
        <p>Several Palestinian intellectuals, politicians, and artists are associated with the university and the town.</p>
        <h4>More Information</h4>
        <ul>
          <li><a href="https://www.birzeit.edu">Birzeit University</a></li>
          <li><a href="https://en.wikipedia.org/wiki/Birzeit">Birzeit on Wikipedia</a></li>
        </ul>
      </section>

      <section>
        <h3>Main Business Activities</h3>
        <ul>
          <li> Management of Rental Listings (homes, apartments, and studios)</li>
          <li>Drafting & Assistance with Rental Agreements</li>
          <li>Tenant-Owner Matchmaking</li>
          <li>Promotion & Photography Services for Property</li>
          <li>24/7 Customer Support for Rental Problems</li>
          <li>Coordination of Property Maintenance</li>
          <li>Consultation & Price Estimates on Rental Market</li>
        </ul>
      </section>
    </main>
  </section>

  <?php include 'footer.php'; ?>

</body>

</html>