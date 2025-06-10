{{-- filepath: c:\xampp\htdocs\laravel-newsletter\newsletter\resources\views\mail\newsletter-sent-successfully.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Newsletter Sent Successfully - TechExpo</title>
    <style>
        /* Reset styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
        }
        
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        /* Header */
        .header {
            background: linear-gradient(135deg, #229799 0%, #37a3a5 50%, #00BCD4 100%);
            padding: 30px 20px;
            text-align: center;
            color: white;
        }
        
        .logo {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #e2ebf4;
        }
        
        .tagline {
            font-size: 14px;
            opacity: 0.9;
            color: #e2ebf4;
        }
        
        /* Success Section */
        .success-section {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 8px;
            padding: 30px 20px;
            margin: 20px;
            text-align: center;
        }
        
        .success-icon {
            font-size: 48px;
            color: #155724;
            margin-bottom: 15px;
        }
        
        .success-title {
            font-size: 24px;
            color: #155724;
            margin-bottom: 10px;
            font-weight: bold;
        }
        
        .success-message {
            font-size: 16px;
            color: #155724;
            margin-bottom: 20px;
        }
        
        /* Newsletter Info */
        .newsletter-info {
            background-color: #fafaff;
            padding: 25px 20px;
            margin: 20px;
            border-radius: 8px;
            border-left: 4px solid #229799;
        }
        
        .info-title {
            font-size: 18px;
            color: #2c3e50;
            margin-bottom: 15px;
            font-weight: bold;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 5px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .info-label {
            font-weight: 600;
            color: #5a6c7d;
        }
        
        .info-value {
            color: #2c3e50;
        }
        
        /* Stats Section */
        .stats-section {
            background-color: #ffffff;
            padding: 25px 20px;
            margin: 20px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }
        
        .stats-title {
            font-size: 18px;
            color: #2c3e50;
            margin-bottom: 20px;
            font-weight: bold;
            text-align: center;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
        }
        
        .stat-card {
            text-align: center;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #229799;
            display: block;
        }
        
        .stat-label {
            font-size: 14px;
            color: #6c757d;
            margin-top: 5px;
        }
        
        /* Action Buttons */
        .action-section {
            text-align: center;
            padding: 25px 20px;
            background-color: #ffffff;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #229799 0%, #37a3a5 100%);
            color: #e2ebf4;
            text-decoration: none;
            border-radius: 25px;
            font-weight: bold;
            margin: 0 10px 10px 0;
            transition: transform 0.2s;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            color: #e2ebf4;
            text-decoration: none;
        }
        
        .btn-outline {
            background: transparent;
            border: 2px solid #229799;
            color: #229799;
        }
        
        /* Footer */
        .footer {
            background-color: #1a1f2c;
            color: #e2ebf4;
            padding: 20px;
            text-align: center;
        }
        
        .footer-brand {
            font-size: 16px;
            font-weight: bold;
            color: #e2ebf4;
            margin-bottom: 10px;
        }
        
        .footer-links {
            margin-bottom: 10px;
        }
        
        .footer-links a {
            color: #00BCD4;
            text-decoration: none;
            margin: 0 10px;
        }
        
        .footer-links a:hover {
            text-decoration: underline;
        }
        
        .footer-text {
            font-size: 14px;
            color: #95a5a6;
            line-height: 1.5;
        }
        
        /* Responsive */
        @media (max-width: 600px) {
            .container {
                margin: 0;
                box-shadow: none;
            }
            
            .header {
                padding: 20px 15px;
            }
            
            .newsletter-info,
            .stats-section,
            .success-section {
                margin: 10px;
                padding: 20px 15px;
            }
            
            .info-item {
                flex-direction: column;
                gap: 5px;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .btn {
                display: block;
                margin: 10px 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo">
                ⟨TechExpo/⟩
            </div>
            <div class="tagline">
                Newsletter Management System
            </div>
        </div>
        
        <!-- Success Message -->
        <div class="success-section">
            <div class="success-icon">✅</div>
            <div class="success-title">Newsletter Sent Successfully!</div>
            <div class="success-message">
                Your newsletter "{{ $newsletter->title ?? 'Newsletter' }}" has been successfully sent to all subscribers.
            </div>
        </div>
        
        <!-- Newsletter Information -->
        <div class="newsletter-info">
            <div class="info-title">📋 Newsletter Details</div>
            
            <div class="info-item">
                <span class="info-label">Newsletter Title:</span>
                <span class="info-value">{{ $newsletter->title ?? 'N/A' }}</span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Sent Date:</span>
                <span class="info-value">{{ $newsletter->sent_at ?? now()->format('F j, Y \a\t g:i A') }}</span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Newsletter Type:</span>
                <span class="info-value">{{ $newsletter->newsletter_type ?? 'Regular' }}</span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Category:</span>
                <span class="info-value">{{ $newsletter->catagorie ?? 'General' }}</span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Author:</span>
                <span class="info-value">{{ $author->name ?? 'Unknown' }}</span>
            </div>
        </div>
        
        <!-- Statistics -->
        <div class="stats-section">
            <div class="stats-title">📊 Delivery Statistics</div>
            <div class="stats-grid">
                <div class="stat-card">
                    <span class="stat-number">{{ $stats['total_recipients'] ?? '0' }}</span>
                    <div class="stat-label">Total Recipients</div>
                </div>
                <div class="stat-card">
                    <span class="stat-number">{{ $stats['sent_successfully'] ?? '0' }}</span>
                    <div class="stat-label">Sent Successfully</div>
                </div>
                <div class="stat-card">
                    <span class="stat-number">{{ $stats['failed_deliveries'] ?? '0' }}</span>
                    <div class="stat-label">Failed Deliveries</div>
                </div>
                <div class="stat-card">
                    <span class="stat-number">{{ number_format((($stats['sent_successfully'] ?? 0) / max($stats['total_recipients'] ?? 1, 1)) * 100, 1) }}%</span>
                    <div class="stat-label">Success Rate</div>
                </div>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="action-section">
            <a href="{{ route('dashboard.newsletter') }}" class="btn">
                📧 View All Newsletters
            </a>
            <a href="{{ route('newsletter.create') }}" class="btn btn-outline">
                ✏️ Create New Newsletter
            </a>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <div class="footer-brand">
                &lt;TechExpo/&gt;
            </div>
            
            <div class="footer-links">
                <a href="{{ route('dashboard.newsletter') }}">Dashboard</a>
                <a href="{{ route('profile.show', $author->id ?? 1) }}">Profile</a>
            </div>
            
            <div class="footer-text">
                <p>This is an automated notification from your newsletter management system.</p>
                <p>&copy; {{ date('Y') }} TechExpo. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>