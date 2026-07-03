<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
     
        <meta name="csrf-token" content="{{ csrf_token() }}" />
         
		<title>@yield('title') - {{$generalsetting->name}}</title>

		<script>
        document.cookie = "screen_width=" + window.innerWidth + "; path=/";
        </script>

        <!-- App favicon -->

        <link rel="shortcut icon" href="{{asset($generalsetting->favicon)}}" alt="{{$generalsetting->name}} Favicon" />
        <meta name="author" content="NextStageSoftware" />
        <link rel="canonical" href="https://nextstagesoftware.com" />
        @stack('seo') 
        @stack('css')
        <link rel="stylesheet" href="{{asset('public/frontEnd/css/bootstrap.min.css')}}" />
        <link rel="stylesheet" href="{{asset('public/frontEnd/css/animate.css')}}" />
        <link rel="stylesheet" href="{{asset('public/frontEnd/css/all.min.css')}}" />
        <link rel="stylesheet" href="{{asset('public/frontEnd/css/owl.carousel.min.css')}}" />
        <link rel="stylesheet" href="{{asset('public/frontEnd/css/owl.theme.default.min.css')}}" />
        <link rel="stylesheet" href="{{asset('public/frontEnd/css/mobile-menu.css')}}" />
        <link rel="stylesheet" href="{{asset('public/frontEnd/css/select2.min.css')}}" />
        <!-- toastr css -->
        <link rel="stylesheet" href="{{asset('public/backEnd/')}}/assets/css/toastr.min.css" />

        <link rel="stylesheet" href="{{asset('public/frontEnd/css/wsit-menu.css')}}" />
        <link rel="stylesheet" href="{{asset('public/frontEnd/css/style.css')}}" />
        <link rel="stylesheet" href="{{asset('public/frontEnd/css/responsive.css')}}" />
        <link rel="stylesheet" href="{{asset('public/frontEnd/css/main.css')}}" />

        <meta name="facebook-domain-verification" content="{{$generalsetting->facebook_verification}}" />
        <meta name="google-site-verification" content="{{$generalsetting->google_verification}}" />
		
		
		
		

        @foreach($pixels as $pixel)
        <!-- Facebook Pixel Code -->
        <script>
            !(function (f, b, e, v, n, t, s) {
                if (f.fbq) return;
                n = f.fbq = function () {
                    n.callMethod ? n.callMethod.apply(n, arguments) : n.queue.push(arguments);
                };
                if (!f._fbq) f._fbq = n;
                n.push = n;
                n.loaded = !0;
                n.version = "2.0";
                n.queue = [];
                t = b.createElement(e);
                t.async = !0;
                t.src = v;
                s = b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t, s);
            })(window, document, "script", "https://connect.facebook.net/en_US/fbevents.js");
            fbq("init", "{{{$pixel->code}}}");
            fbq("track", "PageView");
        </script>
        <noscript>
            <img height="1" width="1" style="display: none;" src="https://www.facebook.com/tr?id={{{$pixel->code}}}&ev=PageView&noscript=1" />
        </noscript>
        <!-- End Facebook Pixel Code -->
        @endforeach
        
        @foreach($gtm_code as $gtm)
        <!-- Google tag (gtag.js) -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-{{ $gtm->code }}');</script>
        <!-- End Google Tag Manager -->
        @endforeach
        <style>
            /* Social float container -- added 2026-05-02 */
            .social-float-container {
                position: fixed;
                bottom: 20px;
                left: 20px;
                z-index: 9990;
                display: flex;
                flex-direction: column-reverse;
                align-items: center;
                gap: 10px;
            }
            .social-float-toggle {
                width: 52px;
                height: 52px;
                border-radius: 50%;
                background: #333;
                color: #fff;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                font-size: 22px;
                box-shadow: 2px 2px 6px rgba(0,0,0,0.35);
                border: none;
                transition: background 0.2s, transform 0.3s;
                flex-shrink: 0;
            }
            .social-float-toggle.open { background: #555; }
            .social-float-buttons {
                display: flex;
                flex-direction: column-reverse;
                align-items: center;
                gap: 10px;
                overflow: hidden;
                max-height: 0;
                transition: max-height 0.35s ease, opacity 0.3s ease;
                opacity: 0;
            }
            .social-float-buttons.open { max-height: 300px; opacity: 1; }
            .whatsapp-float, .messenger-float {
                width: 52px;
                height: 52px;
                border-radius: 50%;
                color: white;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 24px;
                box-shadow: 2px 2px 5px rgba(0,0,0,0.3);
                text-decoration: none;
                transition: box-shadow 0.2s, transform 0.2s;
            }
            .whatsapp-float  { background-color: #25D366; }
            .messenger-float { background: linear-gradient(135deg, #0084FF, #00B2FF); }
            .whatsapp-float:hover,
            .messenger-float:hover { color: white; box-shadow: 2px 2px 10px rgba(0,0,0,0.45); transform: scale(1.08); }
            @media (max-width: 768px) {
                .social-float-container { display: none; }
            }

            /* AI Chat widget -- added 2026-04-15, z-index raised 2026-05-02 */
            .ai-chat-float {
                position: fixed;
                bottom: 80px;
                right: 20px;
                z-index: 99998;
                background: linear-gradient(135deg, #6366f1, #8b5cf6);
                color: white;
                border-radius: 50%;
                width: 56px;
                height: 56px;
                font-size: 22px;
                box-shadow: 2px 2px 8px rgba(0,0,0,0.3);
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                border: none;
                transition: box-shadow 0.2s;
            }
            .ai-chat-float:hover { box-shadow: 2px 2px 14px rgba(99,102,241,0.6); }
            @media (max-width: 768px) { .ai-chat-float { display: none; } }

            #ai-chat-window {
                display: none;
                position: fixed;
                bottom: 145px;
                right: 20px;
                width: 340px;
                max-height: min(480px, calc(100vh - 180px));
                z-index: 99999;
                background: #fff;
                border-radius: 16px;
                box-shadow: 0 8px 32px rgba(0,0,0,0.18);
                flex-direction: column;
                overflow: hidden;
            }
            #ai-chat-window.open { display: flex; }
            .ai-chat-header {
                background: linear-gradient(135deg, #6366f1, #8b5cf6);
                color: #fff;
                padding: 12px 16px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                font-weight: 600;
                font-size: 14px;
            }
            .ai-chat-header span { font-size: 18px; margin-right: 8px; }
            .ai-chat-header button {
                background: none; border: none; color: #fff;
                font-size: 18px; cursor: pointer; line-height: 1;
            }
            #ai-chat-messages {
                flex: 1;
                overflow-y: auto;
                padding: 12px;
                display: flex;
                flex-direction: column;
                gap: 8px;
                background: #f8f9ff;
            }
            .ai-msg, .user-msg {
                max-width: 82%;
                padding: 8px 12px;
                border-radius: 12px;
                font-size: 13px;
                line-height: 1.5;
                word-break: break-word;
            }
            .ai-msg   { background: #ede9fe; color: #3730a3; align-self: flex-start; border-bottom-left-radius: 2px; }
            .user-msg { background: #6366f1; color: #fff;    align-self: flex-end;   border-bottom-right-radius: 2px; }
            .ai-chat-typing { font-size: 12px; color: #888; align-self: flex-start; padding: 4px 8px; }
            .ai-chat-input-row {
                display: flex;
                border-top: 1px solid #e5e7eb;
                background: #fff;
            }
            #ai-chat-input {
                flex: 1;
                border: none;
                padding: 10px 12px;
                font-size: 13px;
                outline: none;
                resize: none;
            }
            #ai-chat-send {
                background: #6366f1;
                color: #fff;
                border: none;
                padding: 0 16px;
                font-size: 18px;
                cursor: pointer;
                transition: background 0.2s;
            }
            #ai-chat-send:hover { background: #4f46e5; }
            .ai-chat-clear {
                text-align: center;
                padding: 4px;
                background: #f8f9ff;
                border-top: 1px solid #e5e7eb;
            }
            .ai-chat-clear a {
                font-size: 11px;
                color: #9ca3af;
                cursor: pointer;
                text-decoration: none;
            }
            .ai-chat-clear a:hover { color: #6366f1; }
            .stock-out-overlay {
                position: absolute;
                top: 50%;
                left: 0;
                transform: translateY(-50%);
                width: 100%;
                background-color: white;
                color: black;
                font-size: 1em;
                opacity:0.8;
                font-weight: bold;
                text-align: center;
                padding: 10px 0;
                overflow: hidden;
                white-space: nowrap;
            }
            /* Facebook icon */
            .social_list .fa-facebook-f {
                padding:5px 8px;
                color:white;
                background-color: #3b5998; 
                
            }
            
            .social_list .fa-facebook-f:hover {
                background-color: #2d4373;  /* Darker Facebook blue on hover */
            }
            
            /* Twitter icon */
            .social_list .fa-twitter {
                padding:5px 8px;
                color:white;
                background-color: #1da1f2;  /* Twitter blue */
            }
            
            .social_list .fa-twitter:hover {
                padding:5px 8px;
                color:white;
                background-color: #0c85d0;  /* Darker Twitter blue on hover */
            }
            
            /* Instagram icon */
            .social_list .fa-instagram {
                padding:5px 8px;
                color:white;
                background-color: #e4405f;  /* Instagram pink */
            }
            
            .social_list .fa-instagram:hover {
                padding:5px 8px;
                color:white;
                background-color: #bc2a8d;  /* Darker Instagram purple-pink on hover */
            }
            
            /* LinkedIn icon */
            .social_list .fa-linkedin {
                padding:5px 8px;
                color:white;
                background-color: #0077b5;  /* LinkedIn blue */
            }
            
            .social_list .fa-linkedin:hover {
                background-color: #005983;  /* Darker LinkedIn blue on hover */
            }
            
            /* WhatsApp icon */
            .social_list .fa-whatsapp {
                padding:5px 8px;
                color:white;
                background-color: #25d366;  /* WhatsApp green */
            }
            
            .social_list .fa-whatsapp:hover {
                background-color: #128c7e;  /* Darker WhatsApp green on hover */
            }
            
            /* YouTube icon */
            .social_list .fa-youtube {
                padding:5px 8px;
                color:white;
                background-color: #ff0000;  /* YouTube red */
            }
            
            .social_list .fa-youtube:hover {
                background-color: #cc0000;  /* Darker YouTube red on hover */
            }

        </style>
        {!! $generalsetting->header_code !!}
    </head>
    <body class="gotop">
       
        @php $subtotal = Cart::instance('shopping')->subtotal(); @endphp
        <div class="mobile-menu">
                <div class="mobile-menu-logo">
                    <div class="logo-image">
                        <img src="{{asset($generalsetting->white_logo)}}" alt="" />
                    </div>
                    <div class="mobile-menu-close">
                        <i class="fa fa-times"></i>
                    </div>
                </div>
                <ul class="first-nav">
                    @foreach($menucategories as $scategory)
                    <li class="parent-category">
                        <a href="{{url('category/'.$scategory->slug)}}" class="menu-category-name">
                            <img src="{{asset($scategory->image)}}" alt="" class="side_cat_img" />
                            {{$scategory->name}}
                        </a>
                        @if($scategory->subcategories->count() > 0)
                        <span class="menu-category-toggle">
                            <i class="fa fa-chevron-down"></i>
                        </span>
                        @endif
                        <ul class="second-nav" style="display: none;">
                            @foreach($scategory->subcategories as $subcategory)
                            <li class="parent-subcategory">
                                <a href="{{url('subcategory/'.$subcategory->slug)}}" class="menu-subcategory-name">{{$subcategory->subcategoryName}}</a>
                                @if($subcategory->childcategories->count() > 0)
                                <span class="menu-subcategory-toggle"><i class="fa fa-chevron-down"></i></span>
                                @endif
                                <ul class="third-nav" style="display: none;">
                                    @foreach($subcategory->childcategories as $childcat)
                                    <li class="childcategory"><a href="{{url('products/'.$childcat->slug)}}" class="menu-childcategory-name">{{$childcat->childcategoryName}}</a></li>
                                    @endforeach
                                </ul>
                            </li>
                            @endforeach
                        </ul>
                    </li>
                    @endforeach
                    <li class="parent-category">
                        <a href="{{ url('customer/order-track') }}" class="menu-category-name">
                            <i class="fa fa-truck" style="margin-right:8px;"></i>
                            Track Order
                        </a>
                    </li>
                </ul>
            </div>
        <header id="navbar_top">
            <div class="top_header" style="background-color:#3c7d17">
                    <div class="container d-flex align-items-center">
                        <!-- Hotline button on the left side -->
                        <a href="tel:{{ $contact->hotline }}" class="text-center bg-light px-2 d-none d-sm-block fw-bold fs-4" style="color:#13027D;min-width:270px;">
                            <i class="fa-solid fa-headset"></i> {{ $contact->hotline }}
                        </a>
                        
                        <!-- Marquee headline -->
                        <div class="d-flex align-items-center flex-grow-1">
                            <marquee direction="left" scrollamount="5" class="text-light fs-6" >
                                {!! str_replace(['\r\n', '\r', '\n', "\r\n", "\n", "\r"], '&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;', $generalsetting->top_headline) !!}
                            </marquee>
                        </div>
                    </div>
                </div>
            <div class="mobile-header sticky">
                <div class="mobile-logo">
                    <div class="menu-bar">
                        <a class="toggle">
                            <i class="fa-solid fa-bars"></i>
                        </a>
                    </div>
                    <div class="menu-logo">
                        <a href="{{route('home')}}"><img src="{{asset($generalsetting->white_logo)}}" alt="" /></a>
                    </div>
                     <div class="menu-bag">
                        <!-- <p class="margin-shopping">
                            <i class="fa-solid fa-cart-shopping"></i>
                            <span class="mobilecart-qty">{{Cart::instance('shopping')->count()}}</span>
                        </p> -->
                    <a href="{{ route('customer.checkout') }}">
                        <p class="margin-shopping" style="color: red;"> {{-- red color helps confirm mobile view --}}
                            <i class="fa-solid fa-cart-shopping"></i> {{-- different icon for mobile --}}
                            <span>{{ Cart::instance('shopping')->count() }}</span>
                        </p>
                    </a>
                    </div> 
                </div>
            </div>

            <div class="mobile-search">
                <form action="{{route('search')}}">
                    <input type="text" placeholder="Search Product ... " value="" class="msearch_keyword msearch_click" name="keyword" />
                    <button><i data-feather="search"></i></button>
                </form>
                <div class="search_result"></div>
            </div>

            

            <div class="main-header">
                
            

                <div class="logo-area">
                    <div class="container">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="logo-header">
                                    <div class="main-logo">
                                        <a href="{{route('home')}}"><img src="{{asset($generalsetting->white_logo)}}" alt="" /></a>
                                    </div>
                                    <div class="main-search">
                                        <form action="{{route('search')}}">
                                            <input type="text" placeholder="Search Product..." class="search_keyword search_click" name="keyword" />
                                            <button>
                                                <i data-feather="search"></i>
                                            </button>
                                        </form>
                                        <div class="search_result"></div>
                                    </div>
                                    <div class="header-list-items">
                                        <ul>
                                            <li class="track_btn">
                                                <a href="{{route('customer.order_track')}}"> <i class="fa fa-truck"></i>Track Order</a>
                                            </li>
                                            @if(Auth::guard('customer')->user())
                                            <li class="for_order">
                                                <p>
                                                    <a href="{{route('customer.account')}}">
                                                        <i class="fa-regular fa-user"></i>

                                                        {{Str::limit(Auth::guard('customer')->user()->name,14)}}
                                                    </a>
                                                </p>
                                            </li>
                                            @else
                                            <li class="for_order">
                                                <p>
                                                    <a href="{{route('customer.login')}}">
                                                        <i class="fa-regular fa-user"></i>
                                                        Login / Sign Up
                                                    </a>
                                                </p>
                                            </li>
                                            @endif

                                            <li class="cart-dialog" id="cart-qty">
                                                <a href="{{route('customer.checkout')}}">
                                                    <p class="margin-shopping">
                                                        <i class="fa-solid fa-cart-shopping"></i>
                                                        <span>{{Cart::instance('shopping')->count()}}</span>
                                                    </p>
                                                </a>
                                                <div class="cshort-summary">
                                                    <ul>
                                                        @foreach(Cart::instance('shopping')->content() as $key=>$value)
                                                        <li>
                                                            <a href=""><img src="{{asset($value->options->image)}}" alt="" /></a>
                                                        </li>
                                                        <li>
                                                            <a href="">{{Str::limit($value->name, 30)}}</a>
                                                            @if($value->options->product_size || $value->options->product_color)
                                                            <small class="d-block text-muted" style="font-size:11px;line-height:1.3">
                                                                @if($value->options->product_size) Size: {{$value->options->product_size}} @endif
                                                                @if($value->options->product_size && $value->options->product_color) &nbsp;|&nbsp; @endif
                                                                @if($value->options->product_color) Color: {{$value->options->product_color}} @endif
                                                            </small>
                                                            @endif
                                                        </li>
                                                        <li>Qty: {{$value->qty}}</li>
                                                        <li>
                                                            <p>৳{{$value->price}}</p>
                                                            <button class="remove-cart cart_remove" data-id="{{$value->rowId}}"><i data-feather="x"></i></button>
                                                        </li>
                                                        @endforeach
                                                    </ul>
                                                    <p><strong>সর্বমোট : ৳{{$subtotal}}</strong></p>
                                                    <a href="{{route('customer.checkout')}}" class="go_cart"> অর্ডার করুন </a>
                                                </div>
                                            </li>

                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="menu-area">
                    <div class="container">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="catagory_menu text-center">
                                    <ul>
                                        @foreach ($menucategories as $scategory)
                                        <li class="cat_bar ">
                                            <a href="{{ url('category/' . $scategory->slug) }}"> 
                                                <span class="cat_head">{{ $scategory->name }}</span>
                                                @if ($scategory->subcategories->count() > 0)
                                                <i class="fa-solid fa-angle-down cat_down"></i>
                                                @endif
                                            </a>
                                            @if($scategory->subcategories->count() > 0)
                                            <ul class="Cat_menu">
                                                @foreach ($scategory->subcategories as $subcat)
                                                <li class="Cat_list cat_list_hover">
                                                    <a href="{{ url('subcategory/' . $subcat->slug) }}">
                                                        <span>{{ Str::limit($subcat->subcategoryName, 25) }}</span>
                                                        @if($subcat->childcategories->count() > 0)<i class="fa-solid fa-chevron-right cat_down"></i>@endif
                                                    </a>
                                                    @if($subcat->childcategories->count() > 0)
                                                    <ul class="child_menu">
                                                        @foreach($subcat->childcategories as $childcat)
                                                        <li class="child_main">
                                                            <a href="{{ url('products/'.$childcat->slug) }}">{{ $childcat->childcategoryName }}</a>
                                                            
                                                        </li>
                                                        @endforeach
                                                    </ul>
                                                    @endif
                                                </li>
                                                @endforeach
                                            </ul>
                                            @endif
                                        </li>
                                        @endforeach
                                    </ul>
                                    <!-- <a href="{{ url('customer/order-track') }}">Track Order</a> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- main-header end -->
        </header>
        <div id="content">
            @yield('content')
        </div>
            <!-- content end -->
        <footer>
            <div class="footer-top">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-4 mb-3 mb-sm-0">
                            <div class="footer-about">
                                <a href="{{route('home')}}">
                                    <img src="{{asset($generalsetting->white_logo)}}" alt="" />
                                </a>
                                <p>{{$contact->address}}</p>
                                <a href="tel:{{$contact->hotline}}" class="footer-hotlint">{{$contact->hotline}}</a>
                            </div>
                        </div>
                        <!-- col end -->
                        <div class="col-sm-3 mb-3 mb-sm-0 col-6">
                            <div class="footer-menu">
                                <ul>
                                    <li class="title"><a>Useful Link</a></li>
                                    <li>
                                        <a href="{{route('contact')}}"> Contact Us</a>
                                    </li>
                                    @foreach($pages as $page)
                                    <li><a href="{{route('page',['slug'=>$page->slug])}}">{{$page->name}}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <!-- col end -->
                        <div class="col-sm-2 mb-3 mb-sm-0 col-6">
                            <div class="footer-menu">
                                <ul>
                                    <li class="title"><a>Link</a></li>
                                     <li>
                                        <a href="{{route('shop')}}">All Products</a>
                                    </li>
                                    @foreach($pagesright as $key=>$value)
                                    <li>
                                        <a href="{{route('page',['slug'=>$value->slug])}}">{{$value->name}}</a>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <!-- col end -->
                        <div class="col-sm-3 mb-3 mb-sm-0">
                            <div class="footer-menu">
                                <ul>
                                    <li class="title stay_conn"><a>Follow Us</a></li>
                                </ul>
                                <ul class="social_link">
                                    @foreach($socialicons as $value)
                                    <li class="social_list">
                                        <a class="mobile-social-link" href="{{$value->link}}"><i class="{{$value->icon}}"></i></a>
                                    </li>
                                    @endforeach
                                </ul>
                               
                            </div>
                        </div>
                        <!-- col end -->
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="copyright">
                                <p>Copyright © {{ date('Y') }} {{$generalsetting->name}}. All rights reserved | <span style="color: white;">Website Designed by: <a href="https://nextstagesoftware.com/"><span style="color: white;">NextStageSoftware</span></a></span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>

        <div class="footer_nav">
            <ul>
                <li>
                    <a class="toggle">
                        <span>
                            <i class="fa-solid fa-bars"></i>
                        </span>
                        <span>Category</span>
                    </a>
                </li>

                <li>
                    <a id="mobile-contact-btn" style="cursor:pointer;">
                        <span><i class="fa-solid fa-comment-dots"></i></span>
                        <span>Contact</span>
                    </a>
                </li>

                <li class="mobile_home">
                    <a href="{{route('home')}}">
                        <span><i class="fa-solid fa-home"></i></span> <span>Home</span>
                    </a>
                </li>

                <li>
                    <a href="{{route('customer.checkout')}}">
                        <span>
                            <i class="fa-solid fa-cart-shopping"></i>
                        </span>
                        <span>Cart (<b class="mobilecart-qty">{{Cart::instance('shopping')->count()}}</b>)</span>
                    </a>
                </li>
                @if(Auth::guard('customer')->user())
                <li>
                    <a href="{{route('customer.account')}}">
                        <span>
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <span>Account</span>
                    </a>
                </li>
                @else
                <li>
                    <a href="{{route('customer.login')}}">
                        <span>
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <span>Login</span>
                    </a>
                </li>
                @endif
            </ul>
        </div>
        
        {{-- Mobile contact sheet (WhatsApp + Messenger) -- added 2026-05-07 --}}
        <div id="mobile-contact-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:100000;"></div>
        <div id="mobile-contact-sheet" style="display:none;position:fixed;bottom:0;left:0;right:0;background:#fff;border-radius:16px 16px 0 0;z-index:100001;padding:20px 24px 32px;box-shadow:0 -4px 20px rgba(0,0,0,.15);">
            <div style="text-align:center;margin-bottom:16px;font-weight:600;font-size:15px;color:#333;">Contact Us</div>
            <div style="display:flex;gap:16px;justify-content:center;">
                @if(!empty($contact->whatsapp))
                <a href="https://wa.me/{{str_replace(['+', ' ', '-'], '', $contact->whatsapp)}}?text=Hello"
                   target="_blank"
                   style="flex:1;display:flex;flex-direction:column;align-items:center;gap:8px;padding:16px 0;border-radius:12px;background:#e9faf0;color:#25D366;text-decoration:none;font-size:13px;font-weight:600;">
                    <i class="fa-brands fa-whatsapp" style="font-size:32px;"></i>
                    WhatsApp
                </a>
                @endif
                @if(!empty($contact->messenger))
                <a href="https://m.me/{{ preg_replace('#^(https?://)?(www\.)?m\.me/#', '', $contact->messenger) }}"
                   target="_blank"
                   style="flex:1;display:flex;flex-direction:column;align-items:center;gap:8px;padding:16px 0;border-radius:12px;background:#e8f4ff;color:#0084FF;text-decoration:none;font-size:13px;font-weight:600;">
                    <i class="fa-brands fa-facebook-messenger" style="font-size:32px;"></i>
                    Messenger
                </a>
                @endif
            </div>
            <button id="mobile-contact-close" style="margin-top:16px;width:100%;padding:10px;border:1px solid #ddd;border-radius:8px;background:#f5f5f5;font-size:14px;cursor:pointer;">Cancel</button>
        </div>
        <script>
        (function(){
            var btn      = document.getElementById('mobile-contact-btn');
            var overlay  = document.getElementById('mobile-contact-overlay');
            var sheet    = document.getElementById('mobile-contact-sheet');
            var closeBtn = document.getElementById('mobile-contact-close');
            function open(){ overlay.style.display='block'; sheet.style.display='block'; }
            function close(){ overlay.style.display='none'; sheet.style.display='none'; }
            if(btn){ btn.addEventListener('click', open); }
            if(closeBtn){ closeBtn.addEventListener('click', close); }
            if(overlay){ overlay.addEventListener('click', close); }
        })();
        </script>

        {{-- Social float container (WhatsApp + Messenger) -- added 2026-05-02 --}}
        @php
            $showWa  = ($contact->whatsapp_float ?? 1) && $contact->whatsapp;
            $showMsg = ($contact->messenger_float ?? 1) && $contact->messenger;
        @endphp
        @if($showWa || $showMsg)
        <div class="social-float-container">
            <button class="social-float-toggle" id="social-float-toggle" title="Contact Us">
                <i class="fa-solid fa-comment-dots"></i>
            </button>
            <div class="social-float-buttons" id="social-float-buttons">
                @if($showWa)
                <a href="https://wa.me/{{str_replace(['+', ' ', '-'], '', $contact->whatsapp)}}?text=Hello, I would like to inquire about..."
                   target="_blank" class="whatsapp-float" title="WhatsApp">
                    <i class="fa-brands fa-whatsapp"></i>
                </a>
                @endif
                @if($showMsg)
                <a href="https://m.me/{{ preg_replace('#^(https?://)?(www\.)?m\.me/#', '', $contact->messenger) }}"
                   target="_blank" class="messenger-float" title="Messenger">
                    <i class="fa-brands fa-facebook-messenger"></i>
                </a>
                @endif
            </div>
        </div>

        <script>
        (function(){
            var btn  = document.getElementById('social-float-toggle');
            var box  = document.getElementById('social-float-buttons');
            btn.addEventListener('click', function(){
                btn.classList.toggle('open');
                box.classList.toggle('open');
            });
        })();
        </script>
        @endif

        {{-- AI Chat Widget -- added 2026-04-15 --}}
        @php $aiChat = \App\Models\AiChatSetting::first(); @endphp
        @if(!$aiChat || $aiChat->status)
        <button class="ai-chat-float" id="ai-chat-toggle" title="Ask AI Assistant">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                <path d="M12 2a10 10 0 0 1 10 10c0 5.52-4.48 10-10 10H2l2.93-2.93A9.96 9.96 0 0 1 2 12 10 10 0 0 1 12 2z"/>
                <circle cx="8" cy="12" r="1" fill="currentColor" stroke="none"/>
                <circle cx="12" cy="12" r="1" fill="currentColor" stroke="none"/>
                <circle cx="16" cy="12" r="1" fill="currentColor" stroke="none"/>
            </svg>
        </button>

        <div id="ai-chat-window">
            <div class="ai-chat-header">
                <div><span>🤖</span> AI Assistant</div>
                <button id="ai-chat-close" title="Close">×</button>
            </div>
            <div id="ai-chat-messages">
                <div class="ai-msg">Hi! I'm the AI assistant for <strong>{{ $generalsetting->name }}</strong>. Ask me anything about our products, orders, or delivery!</div>
            </div>
            <div class="ai-chat-clear">
                <a id="ai-chat-clear-btn">Clear conversation</a>
            </div>
            <div class="ai-chat-input-row">
                <textarea id="ai-chat-input" rows="1" placeholder="Ask something…"></textarea>
                <button id="ai-chat-send">&#10148;</button>
            </div>
        </div>

        <script>
        (function(){
            var toggle  = document.getElementById('ai-chat-toggle');
            var win     = document.getElementById('ai-chat-window');
            var closeBtn= document.getElementById('ai-chat-close');
            var sendBtn = document.getElementById('ai-chat-send');
            var input   = document.getElementById('ai-chat-input');
            var msgs    = document.getElementById('ai-chat-messages');
            var clearBtn= document.getElementById('ai-chat-clear-btn');
            var csrfToken = "{{ csrf_token() }}";

            toggle.addEventListener('click', function(){ win.classList.toggle('open'); });
            closeBtn.addEventListener('click', function(){ win.classList.remove('open'); });

            function scrollBottom(){ msgs.scrollTop = msgs.scrollHeight; }

            function addMsg(text, cls){
                var d = document.createElement('div');
                d.className = cls;
                d.textContent = text;
                msgs.appendChild(d);
                scrollBottom();
            }

            function sendMessage(){
                var text = input.value.trim();
                if(!text) return;
                addMsg(text, 'user-msg');
                input.value = '';
                input.style.height = 'auto';

                var typing = document.createElement('div');
                typing.className = 'ai-chat-typing';
                typing.textContent = 'Typing…';
                msgs.appendChild(typing);
                scrollBottom();

                fetch("{{ route('ai.chat.send') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ message: text })
                })
                .then(function(r){ return r.json(); })
                .then(function(data){
                    msgs.removeChild(typing);
                    addMsg(data.reply || 'No response.', 'ai-msg');
                })
                .catch(function(){
                    msgs.removeChild(typing);
                    addMsg('Something went wrong. Please try again.', 'ai-msg');
                });
            }

            sendBtn.addEventListener('click', sendMessage);

            input.addEventListener('keydown', function(e){
                if(e.key === 'Enter' && !e.shiftKey){
                    e.preventDefault();
                    sendMessage();
                }
            });

            // Auto-grow textarea
            input.addEventListener('input', function(){
                this.style.height = 'auto';
                this.style.height = Math.min(this.scrollHeight, 80) + 'px';
            });

            clearBtn.addEventListener('click', function(){
                fetch("{{ route('ai.chat.clear') }}", {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken }
                });
                // Keep first welcome message only
                while(msgs.children.length > 1){ msgs.removeChild(msgs.lastChild); }
            });
        })();
        </script>
        @endif

        <div class="scrolltop" style="">
            <div class="scroll">
                <i class="fa fa-angle-up"></i>
            </div>
        </div>

        <!-- /. fixed sidebar -->

        <div id="custom-modal"></div>
        <div id="page-overlay"></div>
        <div id="loading"><div class="custom-loader"></div></div>

        <script src="{{asset('public/frontEnd/js/jquery-3.6.3.min.js')}}"></script>
        <script src="{{asset('public/frontEnd/js/bootstrap.min.js')}}"></script>
        <script src="{{asset('public/frontEnd/js/owl.carousel.min.js')}}"></script>
        <script src="{{asset('public/frontEnd/js/mobile-menu.js')}}"></script>
        <script src="{{asset('public/frontEnd/js/wsit-menu.js')}}"></script>
        <script src="{{asset('public/frontEnd/js/mobile-menu-init.js')}}"></script>
        <script src="{{asset('public/frontEnd/js/wow.min.js')}}"></script>
        <script>
            new WOW().init();
        </script>
        <link rel="stylesheet" href="{{asset('public/cdn/css/flatpickr.min.css')}}" />
        <script src="{{asset('public/cdn/js/flatpickr.min.js')}}"></script>

        <!-- feather icon -->
        <script src="{{asset('public/cdn/js/feather.min.js')}}"></script>
        <script>
            feather.replace();
        </script>
        <script src="{{asset('public/backEnd/')}}/assets/js/toastr.min.js"></script>
        {!! Toastr::message() !!} @stack('script')
        <script>
            $(".quick_view").on("click", function () {
                var id = $(this).data("id");
                $("#loading").show();
                if (id) {
                    $.ajax({
                        type: "GET",
                        data: { id: id },
                        url: "{{route('quickview')}}",
                        success: function (data) {
                            if (data) {
                                $("#custom-modal").html(data);
                                $("#custom-modal").show();
                                $("#loading").hide();
                                $("#page-overlay").show();
                            }
                        },
                    });
                }
            });
        </script>
        <!-- quick view end -->
        <!-- cart js start -->
        <script>
            $(".addcartbutton").on("click", function () {
                var id = $(this).data("id");
                var qty = 1;
                if (id) {
                    $.ajax({
                        cache: "false",
                        type: "GET",
                        url: "{{url('add-to-cart')}}/" + id + "/" + qty,
                        dataType: "json",
                        success: function (data) {
                            if (data) {
                                toastr.success('Success', 'Product add to cart successfully');
                                return cart_count() + mobile_cart();
                            }
                        },
                    });
                }
            });
            $(".cart_store").on("click", function () {
                var id = $(this).data("id");
                var qty = $(this).parent().find("input").val();
                if (id) {
                    $.ajax({
                        type: "GET",
                        data: { id: id, qty: qty ? qty : 1 },
                        url: "{{route('cart.store')}}",
                        success: function (data) {
                            if (data) {
                                toastr.success('Success', 'Product add to cart succfully');
                                return cart_count() + mobile_cart();
                            }
                        },
                    });
                }
            });

$(document).on("click", ".cart_remove", function (e) {
    e.preventDefault();
    var id = $(this).data("id");

    if (id) {
        // Optional: show loader before redirect
        $(".page-loader").show();

        // Send AJAX request to remove item
        $.ajax({
            type: "GET",
            url: "{{ route('cart.remove') }}",
            data: { id: id },
            success: function (data) {
                // After deletion, reload page
                location.reload();
            },
            error: function (xhr, status, error) {
                //console.error("Error removing item:", error);
                //alert("Removed Successfully");
                location.reload(); // fallback reload
            }
        });
    }
});

            $(".cart_increment").on("click", function () {
                var id = $(this).data("id");
                if (id) {
                    $.ajax({
                        type: "GET",
                        data: { id: id },
                        url: "{{route('cart.increment')}}",
                        success: function (data) {
                            if (data) {
                                $(".cartlist").html(data);
                                return cart_count() + mobile_cart();
                            }
                        },
                    });
                }
            });

            $(".cart_decrement").on("click", function () {
                var id = $(this).data("id");
                if (id) {
                    $.ajax({
                        type: "GET",
                        data: { id: id },
                        url: "{{route('cart.decrement')}}",
                        success: function (data) {
                            if (data) {
                                $(".cartlist").html(data);
                                return cart_count() + mobile_cart();
                            }
                        },
                    });
                }
            });

            function cart_count() {
                $.ajax({
                    type: "GET",
                    url: "{{route('cart.count')}}",
                    success: function (data) {
                        if (data) {
                            $("#cart-qty").html(data);
                        } else {
                            $("#cart-qty").empty();
                        }
                    },
                });
            }
            function mobile_cart() {
                $.ajax({
                    type: "GET",
                    url: "{{route('mobile.cart.count')}}",
                    success: function (data) {
                        if (data) {
                            $(".mobilecart-qty").html(data);
                        } else {
                            $(".mobilecart-qty").empty();
                        }
                    },
                });
            }
            function cart_summary() {
                $.ajax({
                    type: "GET",
                    url: "{{route('shipping.charge')}}",
                    dataType: "html",
                    success: function (response) {
                        $(".cart-summary").html(response);
                    },
                });
            }
        </script>
        <!-- cart js end -->
        <script>
            $(".search_click").on("keyup change", function () {
                var keyword = $(".search_keyword").val();
                $.ajax({
                    type: "GET",
                    data: { keyword: keyword },
                    url: "{{route('livesearch')}}",
                    success: function (products) {
                        if (products) {
                            $(".search_result").html(products);
                        } else {
                            $(".search_result").empty();
                        }
                    },
                });
            });
            $(".msearch_click").on("keyup change", function () {
                var keyword = $(".msearch_keyword").val();
                $.ajax({
                    type: "GET",
                    data: { keyword: keyword },
                    url: "{{route('livesearch')}}",
                    success: function (products) {
                        if (products) {
                            $("#loading").hide();
                            $(".search_result").html(products);
                        } else {
                            $(".search_result").empty();
                        }
                    },
                });
            });
        </script>
        <!-- search js start -->
        <script></script>
        <script></script>
        <script>
            $(".district").on("change", function () {
                var id = $(this).val();
                $.ajax({
                    type: "GET",
                    data: { id: id },
                    url: "{{route('districts')}}",
                    success: function (res) {
                        if (res) {
                            $(".area").empty();
                            $(".area").append('<option value="">Select..</option>');
                            $.each(res, function (key, value) {
                                $(".area").append('<option value="' + key + '" >' + value + "</option>");
                            });
                        } else {
                            $(".area").empty();
                        }
                    },
                });
            });
        </script>
        <script>
            $(".toggle").on("click", function () {
                $("#page-overlay").show();
                $(".mobile-menu").addClass("active");
            });

            $("#page-overlay").on("click", function () {
                $("#page-overlay").hide();
                $(".mobile-menu").removeClass("active");
                $(".feature-products").removeClass("active");
            });

            $(".mobile-menu-close").on("click", function () {
                $("#page-overlay").hide();
                $(".mobile-menu").removeClass("active");
            });

            $(".mobile-filter-toggle").on("click", function () {
                $("#page-overlay").show();
                $(".feature-products").addClass("active");
            });
        </script>
        <script>
            $(document).ready(function () {
                $(".parent-category").each(function () {
                    const menuCatToggle = $(this).find(".menu-category-toggle");
                    const secondNav = $(this).find(".second-nav");

                    menuCatToggle.on("click", function () {
                        menuCatToggle.toggleClass("active");
                        secondNav.slideToggle("fast");
                        $(this).closest(".parent-category").toggleClass("active");
                    });
                });
                $(".parent-subcategory").each(function () {
                    const menuSubcatToggle = $(this).find(".menu-subcategory-toggle");
                    const thirdNav = $(this).find(".third-nav");

                    menuSubcatToggle.on("click", function () {
                        menuSubcatToggle.toggleClass("active");
                        thirdNav.slideToggle("fast");
                        $(this).closest(".parent-subcategory").toggleClass("active");
                    });
                });
            });
        </script>

        <script>
            var menu = new MmenuLight(document.querySelector("#menu"), "all");

            var navigator = menu.navigation({
                selectedClass: "Selected",
                slidingSubmenus: true,
                // theme: 'dark',
                title: "ক্যাটাগরি",
            });

            var drawer = menu.offcanvas({
                // position: 'left'
            });

            //  Open the menu.
            document.querySelector('a[href="#menu"]').addEventListener("click", (evnt) => {
                evnt.preventDefault();
                drawer.open();
            });
        </script>

        <script>
            // document.addEventListener("DOMContentLoaded", function () {
            //     window.addEventListener("scroll", function () {
            //         if (window.scrollY > 200) {
            //             document.getElementById("navbar_top").classList.add("fixed-top");
            //         } else {
            //             document.getElementById("navbar_top").classList.remove("fixed-top");
            //             document.body.style.paddingTop = "0";
            //         }
            //     });
            // });
            /*=== Main Menu Fixed === */
            // document.addEventListener("DOMContentLoaded", function () {
            //     window.addEventListener("scroll", function () {
            //         if (window.scrollY > 0) {
            //             document.getElementById("m_navbar_top").classList.add("fixed-top");
            //             // add padding top to show content behind navbar
            //             navbar_height = document.querySelector(".navbar").offsetHeight;
            //             document.body.style.paddingTop = navbar_height + "px";
            //         } else {
            //             document.getElementById("m_navbar_top").classList.remove("fixed-top");
            //             // remove padding top from body
            //             document.body.style.paddingTop = "0";
            //         }
            //     });
            // });
            /*=== Main Menu Fixed === */

            $(window).scroll(function () {
                if ($(this).scrollTop() > 50) {
                    $(".scrolltop:hidden").stop(true, true).fadeIn();
                } else {
                    $(".scrolltop").stop(true, true).fadeOut();
                }
            });
            $(function () {
                $(".scroll").click(function () {
                    $("html,body").animate({ scrollTop: $(".gotop").offset().top }, "1000");
                    return false;
                });
            });
        </script>
        <script>
            $(".filter_btn").click(function(){
               $(".filter_sidebar").addClass('active');
               $("body").css("overflow-y", "hidden");
            })
            $(".filter_close").click(function(){
               $(".filter_sidebar").removeClass('active');
               $("body").css("overflow-y", "auto");
            })
        </script>
        <!--search ANIMAtion end-->
        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $gtm->code }}"
        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->
    <script>
    document.addEventListener('error', function(e){
        if(e.target.tagName === 'IMG' && !e.target.dataset.fallback){
            e.target.dataset.fallback = '1';
            e.target.src = '{{ asset('public/fallbacks/no-image.webp') }}';
        }
    }, true);
    </script>
    </body>
</html>
