<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $newsletter['title'] ?? 'Newsletter' }}</title>
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
    <style>
        /* Email Client Reset */
        body, table, td, p, a, li, blockquote {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        
        table, td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
        
        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }
        
        /* Base Styles */
        body {
            height: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, Arial, sans-serif;
            background-color: #f8f9fa;
        }
        
        /* Container */
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        /* Header */
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 30px;
            text-align: center;
        }
        
        .header h1 {
            color: #ffffff;
            font-size: 28px;
            font-weight: 700;
            margin: 0 0 10px 0;
            line-height: 1.2;
        }
        
        .header p {
            color: #ffffff;
            font-size: 16px;
            margin: 0;
            opacity: 0.9;
            line-height: 1.4;
        }
        
        /* Newsletter Type Badge */
        .newsletter-type {
            display: inline-block;
            background-color: rgba(255,255,255,0.2);
            color: #ffffff;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            margin-top: 15px;
            letter-spacing: 0.5px;
        }
        
        /* Featured Image */
        .featured-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
            display: block;
        }
        
        /* Content */
        .content {
            padding: 40px 30px;
        }
        
        .content h2 {
            color: #2c3e50;
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 20px 0;
            line-height: 1.3;
            border-left: 4px solid #667eea;
            padding-left: 15px;
        }
        
        .content h3 {
            color: #34495e;
            font-size: 20px;
            font-weight: 600;
            margin: 30px 0 15px 0;
            line-height: 1.3;
        }
        
        .content p {
            color: #555555;
            font-size: 16px;
            line-height: 1.7;
            margin: 0 0 18px 0;
        }
        
        .content ul {
            margin: 20px 0;
            padding-left: 20px;
        }
        
        .content li {
            color: #555555;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 8px;
        }
        
        .content a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        
        .content a:hover {
            text-decoration: underline;
        }
        
        /* Call to Action Button */
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff !important;
            padding: 14px 28px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 16px;
            margin: 20px 0;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        
        /* Divider */
        .divider {
            height: 2px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            margin: 30px 0;
            border: none;
        }
        
        /* Footer */
        .footer {
            background-color: #2c3e50;
            color: #ecf0f1;
            padding: 40px 30px;
            text-align: center;
        }
        
        .footer h3 {
            color: #ffffff;
            font-size: 20px;
            font-weight: 600;
            margin: 0 0 15px 0;
        }
        
        .footer p {
            font-size: 14px;
            margin: 0 0 15px 0;
            opacity: 0.9;
            line-height: 1.5;
        }
        
        .social-links {
            margin: 25px 0;
        }
        
        .social-links a {
            display: inline-block;
            margin: 0 8px;
            padding: 10px 18px;
            background-color: #34495e;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 500;
        }
        
        .social-links a:hover {
            background-color: #4a6741;
        }
        
        .unsubscribe {
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px solid #34495e;
            font-size: 12px;
            opacity: 0.8;
            line-height: 1.5;
        }
        
        .unsubscribe a {
            color: #3498db !important;
            text-decoration: none;
        }
        
        /* Mobile Responsive */
        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
                margin: 0 !important;
            }
            
            .header {
                padding: 30px 20px !important;
            }
            
            .header h1 {
                font-size: 24px !important;
            }
            
            .content {
                padding: 30px 20px !important;
            }
            
            .content h2 {
                font-size: 22px !important;
            }
            
            .footer {
                padding: 30px 20px !important;
            }
            
            .social-links a {
                display: block !important;
                margin: 8px 0 !important;
            }
            
            .cta-button {
                display: block !important;
                text-align: center !important;
                margin: 20px 0 !important;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header Section -->
        <div class="header">
            <h1>{{ $newsletter['title'] ?? 'Newsletter Title' }}</h1>
            <p>{{ $newsletter['summary'] ?? 'Newsletter preview text' }}</p>
            <div class="newsletter-type">
                @if(isset($newsletter['newsletter_type']))
                    @switch($newsletter['newsletter_type'])
                        @case('weekly')
                            Weekly Digest
                            @break
                        @case('special')
                            Special Edition
                            @break
                        @case('announcement')
                            Announcement
                            @break
                        @default
                            Newsletter
                    @endswitch
                @else
                    Newsletter
                @endif
            </div>
        </div>
        
        <!-- Featured Image -->
        @if(isset($newsletter['featured_image']) && $newsletter['featured_image'])
            <img src="{{ $newsletter['featured_image'] }}" alt="Newsletter Header" class="featured-image">
        @endif
        
        <!-- Content Section -->
        <div class="content">
            <!-- Dynamic Content -->
            {!! $newsletter['content'] ?? 'Newsletter content goes here...' !!}
            
            <div class="divider"></div>
            
            <!-- Footer Message -->
            <p><em>Thank you for being a valued subscriber! We appreciate your continued interest in our updates.</em></p>
            
            @if($subscriber)
                <p><strong>Hello {{ $subscriber->name ?? 'Subscriber' }}!</strong> We hope you enjoyed this newsletter.</p>
            @endif
        </div>
        
        <!-- Footer Section -->
        <div class="footer">
            <h3>Stay Connected With Us</h3>
            <p>Follow us on social media for the latest updates and news.</p>
            
            <div class="social-links">
                <a href="{{ config('app.website_url', '#') }}" target="_blank">🌐 Website</a>
                <a href="{{ config('app.twitter_url', '#') }}" target="_blank">🐦 Twitter</a>
                <a href="{{ config('app.facebook_url', '#') }}" target="_blank">📘 Facebook</a>
                <a href="{{ config('app.linkedin_url', '#') }}" target="_blank">💼 LinkedIn</a>
            </div>
            
            <p>
                <strong>{{ config('app.name', 'Newsletter Team') }}</strong><br>
                📧 {{ config('mail.from.address', 'newsletter@example.com') }}<br>
                📞 {{ config('app.phone', '(555) 123-4567') }}
            </p>
            
            <div class="unsubscribe">
                <p>
                    You're receiving this email because you subscribed to our newsletter.<br>
                    {{-- <a href="{{ $unsubscribeUrl }}">Unsubscribe</a> |  --}}
                    {{-- <a href="{{ $preferencesUrl }}">Update Preferences</a> --}}
                </p>
                <p style="margin-top: 15px;">
                    © {{ $currentYear }} {{ config('app.name', 'Your Company') }}. All rights reserved.
                    @if($subscriber)
                        <br>This email was sent to {{ $subscriber->email }}
                    @endif
                </p>
            </div>
        </div>
    </div>
</body>
</html>