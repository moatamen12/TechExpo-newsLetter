{{-- filepath: c:\xampp\htdocs\laravel-newsletter\newsletter\app\Mail\newsletter-email.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $newsletter->title ?? 'Tech Expo Newsletter' }}</title>
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
        
        /* Header - Updated to match your site colors */
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
        
        /* Newsletter Title */
        .newsletter-title {
            background-color: #ffffff;
            padding: 30px 20px 20px;
            text-align: center;
            border-bottom: 3px solid #229799;
        }
        
        .newsletter-title h1 {
            font-size: 28px;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .newsletter-meta {
            color: #6c757d;
            font-size: 14px;
        }
        
        /* Featured Image */
        .featured-image {
            text-align: center;
            padding: 20px;
            background-color: #fafaff;
        }
        
        .featured-image img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        /* Content */
        .content {
            padding: 30px 20px;
            background-color: #ffffff;
        }
        
        .content h2 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 24px;
        }
        
        .content h3 {
            color: #34495e;
            margin-bottom: 12px;
            font-size: 20px;
        }
        
        .content p {
            margin-bottom: 15px;
            color: #5a6c7d;
            font-size: 16px;
            line-height: 1.7;
        }
        
        .content ul, .content ol {
            margin: 15px 0;
            padding-left: 30px;
        }
        
        .content li {
            margin-bottom: 8px;
            color: #5a6c7d;
        }
        
        .content a {
            color: #229799;
            text-decoration: none;
        }
        
        .content a:hover {
            text-decoration: underline;
        }
        
        /* Author Section */
        .author-section {
            background-color: #fafaff;
            padding: 25px 20px;
            border-top: 1px solid #e9ecef;
        }
        
        .author-info {
            display: table;
            width: 100%;
        }
        
        .author-avatar {
            display: table-cell;
            width: 60px;
            vertical-align: top;
            padding-right: 15px;
        }
        
        .author-avatar img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .author-details {
            display: table-cell;
            vertical-align: top;
        }
        
        .author-name {
            font-weight: bold;
            color: #2c3e50;
            font-size: 16px;
            margin-bottom: 5px;
        }
        
        .author-title {
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .author-bio {
            color: #5a6c7d;
            font-size: 14px;
            line-height: 1.5;
        }
        
        /* Call to Action */
        .cta-section {
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
            padding: 30px 20px;
            text-align: center;
        }
        
        .footer-brand {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #e2ebf4;
        }
        
        .footer-links {
            margin: 20px 0;
        }
        
        .footer-links a {
            color: #e2ebf4;
            text-decoration: none;
            margin: 0 15px;
            font-size: 14px;
        }
        
        .footer-links a:hover {
            color: #229799;
        }
        
        .footer-social {
            margin: 20px 0;
        }
        
        .social-link {
            display: inline-block;
            margin: 0 10px;
            color: #e2ebf4;
            font-size: 18px;
            text-decoration: none;
        }
        
        .social-link:hover {
            color: #229799;
        }
        
        .footer-text {
            font-size: 12px;
            color: #95a5a6;
            margin-top: 20px;
            line-height: 1.5;
        }
        
        /* More Articles Sections */
        .more-articles-section,
        .more-category-section {
            background-color: #fafaff;
            padding: 30px 20px;
            margin: 0;
            border-top: 1px solid #e9ecef;
        }
        
        .more-category-section {
            background-color: #f8f9fa;
        }
        
        .section-header {
            text-align: center;
            margin-bottom: 25px;
        }
        
        .section-header h3 {
            color: #2c3e50;
            font-size: 20px;
            margin-bottom: 8px;
        }
        
        .section-header p {
            color: #6c757d;
            font-size: 14px;
            margin: 0;
        }
        
        /* Articles Grid */
        .articles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }
        
        .article-card {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .article-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .article-meta {
            color: #229799;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        
        .article-card h4 {
            color: #2c3e50;
            font-size: 16px;
            margin-bottom: 10px;
            line-height: 1.4;
        }
        
        .article-card p {
            color: #5a6c7d;
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 15px;
        }
        
        .read-more {
            color: #229799;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
        }
        
        .read-more:hover {
            text-decoration: underline;
        }
        
        /* Category Articles */
        .category-articles {
            margin-bottom: 25px;
        }
        
        .category-article {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #ffffff;
            padding: 15px 20px;
            margin-bottom: 10px;
            border-radius: 6px;
            border: 1px solid #e9ecef;
            transition: background-color 0.2s;
        }
        
        .category-article:hover {
            background-color: #f8f9fa;
        }
        
        .category-article-content {
            flex: 1;
        }
        
        .category-article h4 {
            color: #2c3e50;
            font-size: 16px;
            margin-bottom: 5px;
            line-height: 1.4;
        }
        
        .category-article p {
            color: #5a6c7d;
            font-size: 14px;
            margin: 0;
            line-height: 1.4;
        }
        
        .category-link {
            color: #229799;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            margin-left: 15px;
        }
        
        .category-link:hover {
            text-decoration: underline;
        }
        
        /* Section CTA */
        .section-cta {
            text-align: center;
        }
        
        .section-btn {
            display: inline-block;
            padding: 10px 25px;
            background-color: #229799;
            color: #ffffff;
            text-decoration: none;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
            transition: background-color 0.2s, transform 0.2s;
        }
        
        .section-btn:hover {
            background-color: #1a7a7c;
            transform: translateY(-1px);
            color: #ffffff;
            text-decoration: none;
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
            
            .newsletter-title {
                padding: 20px 15px 15px;
            }
            
            .newsletter-title h1 {
                font-size: 24px;
            }
            
            .content {
                padding: 20px 15px;
            }
            
            .btn {
                display: block;
                margin: 10px 0;
            }
            
            .more-articles-section,
            .more-category-section {
                padding: 20px 15px;
            }
            
            .articles-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .category-article {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .category-link {
                margin-left: 0;
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
                Tech And Science, All in Your Inbox!
            </div>
        </div>
        
        <!-- Newsletter Title -->
        <div class="newsletter-title">
            <h1>{{ $newsletter->title ?? 'Weekly Tech Digest' }}</h1>
            <div class="newsletter-meta">
                {{ $newsletter->created_at ?? now()->format('F j, Y') }} • 
                {{ $newsletter->newsletter_type ?? 'Weekly' }} Edition
            </div>
        </div>
        
        <!-- Featured Image -->
        @if($newsletter->featured_image ?? true)
        <div class="featured-image">
            <img src="{{ asset('storage/' . ($newsletter->featured_image ?? 'default-newsletter.jpg')) }}" 
                 alt="Newsletter Featured Image">
        </div>
        @endif
        
        <!-- Content -->
        <div class="content">
            {!! $newsletter->content ?? '<h2>Latest Tech Insights</h2>
            <p>Welcome to this week\'s edition of TechExpo Newsletter! We\'ve curated the most exciting developments in technology and science just for you.</p>
            
            <h3>This Week\'s Highlights</h3>
            <ul>
                <li>Revolutionary AI breakthroughs changing the industry</li>
                <li>New sustainable tech innovations</li>
                <li>Latest developments in quantum computing</li>
                <li>Emerging trends in cybersecurity</li>
            </ul>
            
            <p>Stay ahead of the curve with our expertly curated content, designed to keep you informed about the rapidly evolving world of technology.</p>' !!}
        </div>
        
        <!-- More Articles by Author Section -->
        <div class="more-articles-section">
            <div class="section-header">
                <h3>📝 More Articles by {{ $author->name ?? 'This Author' }}</h3>
                <p>Discover more insightful content from our expert author</p>
            </div>
            
            <div class="articles-grid">
                <div class="article-card">
                    <div class="article-meta">Tech Insights • 5 min read</div>
                    <h4>The Future of Artificial Intelligence in 2024</h4>
                    <p>Exploring the latest breakthroughs and what they mean for the industry...</p>
                    <a href="{{ route('articles') }}?author={{ $author->id ?? 1 }}" class="read-more">Read More →</a>
                </div>
                
                <div class="article-card">
                    <div class="article-meta">Innovation • 3 min read</div>
                    <h4>Sustainable Technology Solutions</h4>
                    <p>How green technology is reshaping our approach to development...</p>
                    <a href="{{ route('articles') }}?author={{ $author->id ?? 1 }}" class="read-more">Read More →</a>
                </div>
                
                <div class="article-card">
                    <div class="article-meta">Research • 7 min read</div>
                    <h4>Quantum Computing Breakthroughs</h4>
                    <p>Latest developments in quantum technology and their implications...</p>
                    <a href="{{ route('articles') }}?author={{ $author->id ?? 1 }}" class="read-more">Read More →</a>
                </div>
            </div>
            
            <div class="section-cta">
                <a href="{{ route('profile.show', $author->id ?? 1) }}" class="section-btn">View All Articles by {{ $author->name ?? 'Author' }}</a>
            </div>
        </div>
        
        <!-- More Articles in Same Category Section -->
        <div class="more-category-section">
            <div class="section-header">
                <h3>🔍 More {{ ucfirst($newsletter->catagorie ?? 'Technology') }} Articles</h3>
                <p>Explore related topics and stay informed about the latest trends</p>
            </div>
            
            <div class="category-articles">
                <div class="category-article">
                    <div class="category-article-content">
                        <div class="article-meta">{{ ucfirst($newsletter->catagorie ?? 'Technology') }} • Today</div>
                        <h4>Breaking: New Cybersecurity Framework Released</h4>
                        <p>Industry experts reveal new standards for digital security...</p>
                    </div>
                    <a href="{{ route('articles') }}?q={{ $newsletter->catagorie ?? 'tech' }}" class="category-link">Read →</a>
                </div>
                
                <div class="category-article">
                    <div class="category-article-content">
                        <div class="article-meta">{{ ucfirst($newsletter->catagorie ?? 'Technology') }} • Yesterday</div>
                        <h4>Machine Learning in Healthcare</h4>
                        <p>How AI is revolutionizing medical diagnosis and treatment...</p>
                    </div>
                    <a href="{{ route('articles') }}?q={{ $newsletter->catagorie ?? 'tech' }}" class="category-link">Read →</a>
                </div>
                
                <div class="category-article">
                    <div class="category-article-content">
                        <div class="article-meta">{{ ucfirst($newsletter->catagorie ?? 'Technology') }} • 2 days ago</div>
                        <h4>The Rise of Edge Computing</h4>
                        <p>Understanding the shift towards decentralized processing...</p>
                    </div>
                    <a href="{{ route('articles') }}?q={{ $newsletter->catagorie ?? 'tech' }}" class="category-link">Read →</a>
                </div>
            </div>
            
            <div class="section-cta">
                <a href="{{ route('articles') }}?q={{ $newsletter->catagorie ?? 'tech' }}" class="section-btn">Explore All {{ ucfirst($newsletter->catagorie ?? 'Technology') }} Articles</a>
            </div>
        </div>
        
        <!-- Author Section -->
        <div class="author-section">
            <div class="author-info">
                <div class="author-avatar">
                    <img src="{{ asset('storage/' . ($author->userProfile->profile_photo ?? 'default-avatar.jpg')) }}" 
                         alt="{{ $author->name ?? 'Tech Expert' }}">
                </div>
                <div class="author-details">
                    <div class="author-name">{{ $author->name ?? 'Tech Expert' }}</div>
                    <div class="author-title">{{ $author->userProfile->title ?? 'Technology Writer & Expert' }}</div>
                    <div class="author-bio">
                        {{ $author->userProfile->bio ?? 'Passionate about technology and innovation, bringing you the latest insights from the tech world.' }}
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Call to Action -->
        <div class="cta-section">
            <a href="{{ route('profile.show', $author->id ?? 1) }}" class="btn">
                View Author Profile
            </a>
            <a href="{{ route('articles') }}?q={{ $newsletter->catagorie ?? 'tech' }}" class="btn btn-outline">
                Read More about this subject
            </a>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <div class="footer-brand">
                &lt;TechExpo/&gt;
            </div>
            
            <div class="footer-links">
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('articles') }}">Articles</a>
                <a href="{{ route('about') }}">About</a>
                <a href="{{ route('contact') }}">Contact</a>
            </div>
            
            <div class="footer-social">
                <a href="#" class="social-link">📧</a>
                <a href="#" class="social-link">🐦</a>
                <a href="#" class="social-link">💼</a>
                <a href="#" class="social-link">🔗</a>
            </div>
            
            <div class="footer-text">
                <p>You're receiving this email because you subscribed to TechExpo Newsletter.</p>
                <p>
                    <a href="#" style="color: #95a5a6;">Unsubscribe</a> | 
                    <a href="#" style="color: #95a5a6;">Update Preferences</a> | 
                    <a href="{{ route('home') }}" style="color: #95a5a6;">Visit Website</a>
                </p>
                <p>&copy; {{ date('Y') }} TechExpo. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>