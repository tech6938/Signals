<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Us - {{ config('app.name', 'Your Company') }}</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      line-height: 1.6;
      color: #333;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      padding: 20px;
    }

    .container {
      max-width: 800px;
      margin: 0 auto;
      background: rgba(255, 255, 255, 0.95);
      border-radius: 20px;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      backdrop-filter: blur(10px);
    }

    .header {
      background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
      color: white;
      padding: 40px 30px;
      text-align: center;
    }

    .header h1 {
      font-size: 2.5em;
      margin-bottom: 10px;
      font-weight: 700;
    }

    .header p {
      font-size: 1.2em;
      opacity: 0.9;
    }

    .content {
      padding: 30px;
    }

    .section {
      margin-bottom: 40px;
    }

    .section h2 {
      color: #2c3e50;
      font-size: 1.8em;
      margin-bottom: 20px;
      border-bottom: 3px solid #4facfe;
      padding-bottom: 10px;
      position: relative;
    }

    .section h2::after {
      content: '';
      position: absolute;
      bottom: -3px;
      left: 0;
      width: 50px;
      height: 3px;
      background: #00f2fe;
    }

    .section p {
      font-size: 1.1em;
      margin-bottom: 15px;
      text-align: justify;
    }

    .services-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      margin-top: 20px;
    }

    .service-card {
      background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
      color: white;
      padding: 25px;
      border-radius: 15px;
      text-align: center;
      transform: translateY(0);
      transition: all 0.3s ease;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .service-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
    }

    .service-card h3 {
      font-size: 1.3em;
      margin-bottom: 10px;
    }

    .service-card p {
      font-size: 0.95em;
      opacity: 0.9;
    }

    .contact-info {
      background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
      padding: 25px;
      border-radius: 15px;
      margin-top: 20px;
    }

    .contact-item {
      display: flex;
      align-items: center;
      margin-bottom: 15px;
      color: #2c3e50;
    }

    .contact-item:last-child {
      margin-bottom: 0;
    }

    .contact-icon {
      width: 40px;
      height: 40px;
      background: #4facfe;
      color: white;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 15px;
      font-weight: bold;
    }

    .contact-details {
      flex: 1;
    }

    .contact-details strong {
      display: block;
      font-size: 1.1em;
      margin-bottom: 3px;
    }

    .contact-details span {
      color: #666;
    }

    @media (max-width: 768px) {
      body {
        padding: 10px;
      }

      .header {
        padding: 30px 20px;
      }

      .header h1 {
        font-size: 2em;
      }

      .content {
        padding: 20px;
      }

      .services-grid {
        grid-template-columns: 1fr;
      }

      .contact-item {
        flex-direction: column;
        text-align: center;
      }

      .contact-icon {
        margin-bottom: 10px;
        margin-right: 0;
      }
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="header">
      <h1>{{ config('app.name', 'Your Company Name') }}</h1>
      <p>Excellence in Every Solution</p>
    </div>

    <div class="content">
      <!-- Company Overview Section -->
      <div class="section">
        <h2>About Our Company</h2>
        <p>
          Welcome to {{ config('app.name', 'Your Company') }}, where innovation meets excellence.
          Founded with a vision to transform businesses through cutting-edge technology solutions,
          we have been at the forefront of digital transformation for over a decade.
        </p>
        <p>
          Our team of dedicated professionals brings together years of experience in software development,
          digital marketing, and business consulting. We believe in creating solutions that not only meet
          your immediate needs but also scale with your growing business requirements.
        </p>
        <p>
          At {{ config('app.name', 'Your Company') }}, we are committed to delivering exceptional value
          to our clients through innovative approaches, quality deliverables, and unwavering support.
          Your success is our success, and we work tirelessly to ensure your business goals are achieved.
        </p>
      </div>

      <!-- Services Section -->
      <div class="section">
        <h2>Our Services</h2>
        <p>We offer a comprehensive range of services designed to help your business thrive in the digital age:</p>

        <div class="services-grid">
          <div class="service-card">
            <h3>Web Development</h3>
            <p>Custom web applications built with modern frameworks and technologies to drive your business forward.</p>
          </div>

          <div class="service-card">
            <h3>Mobile Apps</h3>
            <p>Native and cross-platform mobile applications that provide seamless user experiences across all devices.</p>
          </div>

          <div class="service-card">
            <h3>Cloud Solutions</h3>
            <p>Scalable cloud infrastructure and services to optimize your operations and reduce costs.</p>
          </div>

          <div class="service-card">
            <h3>Digital Marketing</h3>
            <p>Strategic marketing campaigns to boost your online presence and drive meaningful engagement.</p>
          </div>

          <div class="service-card">
            <h3>Consulting</h3>
            <p>Expert business and technology consulting to guide your digital transformation journey.</p>
          </div>

          <div class="service-card">
            <h3>Support & Maintenance</h3>
            <p>Ongoing support and maintenance services to keep your systems running smoothly 24/7.</p>
          </div>
        </div>
      </div>

      <!-- Contact Information Section -->
      <div class="section">
        <h2>Get in Touch</h2>
        <p>Ready to take your business to the next level? We'd love to hear from you!</p>

        <div class="contact-info">
          <div class="contact-item">
            <div class="contact-icon">📧</div>
            <div class="contact-details">
              <strong>Email</strong>
              <span>info@yourcompany.com</span>
            </div>
          </div>

          <div class="contact-item">
            <div class="contact-icon">📱</div>
            <div class="contact-details">
              <strong>Phone</strong>
              <span>+1 (555) 123-4567</span>
            </div>
          </div>

          <div class="contact-item">
            <div class="contact-icon">📍</div>
            <div class="contact-details">
              <strong>Address</strong>
              <span>123 Business Street, Suite 100<br>City, State 12345</span>
            </div>
          </div>

          <div class="contact-item">
            <div class="contact-icon">🌐</div>
            <div class="contact-details">
              <strong>Website</strong>
              <span>www.yourcompany.com</span>
            </div>
          </div>

          <div class="contact-item">
            <div class="contact-icon">⏰</div>
            <div class="contact-details">
              <strong>Business Hours</strong>
              <span>Monday - Friday: 9:00 AM - 6:00 PM<br>Saturday: 10:00 AM - 4:00 PM</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

</html>