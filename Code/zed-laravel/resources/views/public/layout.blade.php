<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- home Page start  seo  -->
    <?php if(isset($home_seo)&& isset($home_seo['home_seo_title'])){?>
    <title><?php echo $home_seo['home_seo_title']?> | {{ site_text('home_title_suffix') }}</title>
    <?php } ?>
    <?php 
    if(isset($home_seo)&& isset($home_seo['home_seo_keywords'])){
    echo '<meta name="keywords" content="'.$home_seo['home_seo_keywords'].'">';
    }
    if(isset($home_seo)&& isset($home_seo['home_seo_description'])){
    echo '<meta name="description" content="'.$home_seo['home_seo_description'].'">';
    }
    ?>
    <!-- home page end seo -->
    <!-- Page start SEO -->
    <?php   if(isset($details)&& isset($details->page_seo_title)){?>
    <title><?php echo $details->page_seo_title ?> | {{ site_text('site_title_suffix') }}</title>
    <?php } ?>
    <?php 
    if(isset($details)&& isset($details->page_seo_keywords)){
    echo '<meta name="keywords" content="'.$details->page_seo_keywords.'">';
    }
    if(isset($details)&& isset($details->page_seo_description)){
    echo '<meta name="description" content="'.$details->page_seo_description.'">';
    }
    ?>
    <!-- page end seo -->
    <!-- post start seo  -->
    <?php   if(isset($details)&& isset($details->post_seo_title)){?>
    <title><?php echo $details->post_seo_title ?> | {{ site_text('site_title_suffix') }}</title>
    <?php } ?>
    <?php 
    if(isset($details)&& isset($details->post_seo_keywords)){
    echo '<meta name="keywords" content="'.$details->post_seo_keywords.'">';
    }
    if(isset($details)&& isset($details->post_seo_description)){
    echo '<meta name="description" content="'.$details->post_seo_description.'">';
    }
    ?>
    <!-- post end seo -->
    <!-- gallery start seo  -->
    <?php if($page=='gallery'){ ?>
    <?php if(isset($gallery_seo)&& isset($gallery_seo['gallery_seo_title'])){?>
    <title><?php echo $gallery_seo['gallery_seo_title']?> | {{ site_text('site_title_suffix') }}</title>
    <?php } ?>
    <?php 
    if(isset($gallery_seo)&& isset($gallery_seo['gallery_seo_keywords'])){
    echo '<meta name="keywords" content="'.$gallery_seo['gallery_seo_keywords'].'">';
    }
    if(isset($gallery_seo)&& isset($gallery_seo['gallery_seo_description'])){
    echo '<meta name="description" content="'.$gallery_seo['gallery_seo_description'].'">';
    }
    }
    ?>
    <!-- start gallery end seo -->
    <!-- gallery Month -->
    <?php if($page=='individual-gallery'){ ?>
    <?php if(isset($gallery_seo)&& isset($gallery_seo['gallery_seo_title'])){?>
    <title>Gallery | <?php echo e($month); ?> | <?php echo e($year);?> | {{ site_text('site_title_suffix') }}</title>
    <?php } ?>
    <?php 
    if(isset($gallery_seo)&& isset($gallery_seo['gallery_seo_keywords'])){
    echo '<meta name="keywords" content="'.$gallery_seo['gallery_seo_keywords'].'">';
    }
    if(isset($gallery_seo)&& isset($gallery_seo['gallery_seo_description'])){
    echo '<meta name="description" content="'.$gallery_seo['gallery_seo_description'].'">';
    }
    ?>
    <?php } ?>
    <!-- end gallery Month -->
    <!-- pages without their own title / description (CMS > Settings > Header and footer) -->
    <?php $hasOwnTitle = isset($home_seo['home_seo_title']) || isset($details->page_seo_title) || isset($details->post_seo_title)
        || ($page == 'gallery' && isset($gallery_seo['gallery_seo_title'])) || $page == 'individual-gallery'; ?>
    @unless ($hasOwnTitle)
    <title>{{ ['contactus' => 'Contact Us', 'news' => 'News', 'video' => 'Videos', 'search' => 'Search', 'gallery' => 'Gallery'][$page] ?? 'ZED' }} | {{ site_text('site_title_suffix') }}</title>
    @if (site_text('site_meta_description') !== '')<meta name="description" content="{{ site_text('site_meta_description') }}">@endif
    @endunless
    <link href="{{ asset('assets/images') }}/fav.png" rel="shortcut icon" />
    <!-- Plugin css -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css') }}/font-awesome.min.css" media="all" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/fonts') }}/flaticon.css" media="all" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css') }}/bootstrap.min.css" media="all" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css') }}/animate.css" media="all" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css') }}/swiper.min.css" media="all" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css') }}/lightcase.css" media="all" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css') }}/jquery.nstSlider.css" media="all" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css') }}/flexslider.css" media="all" />
    <!-- own style css -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css') }}/style.css?v=3" media="all" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css') }}/responsive.css" media="all" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css') }}/dataTables.bootstrap.min.css">
    <script type="text/javascript" src="{{ asset('assets/js') }}/jquery.min.js"></script>
    <?php echo view('public.partials.csrf-js')->render(); ?>
    <script type="text/javascript" src="{{ asset('assets/js') }}/ajax_actions.js"></script>
    <script type="text/javascript" src="{{ asset('assets/js') }}/jquery.isInViewport.js"></script>
    <link rel="stylesheet" href="{{ asset('assets/css') }}/owl.theme.default.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css') }}/owl.carousel.min.css">
    <!-- accessbility sytles -->
    <link rel="stylesheet" id="colorConLink"  type="text/css" href="" media="all" />
    <link rel="stylesheet" id="textSizeLink"  type="text/css" href="" media="all" />
    <script src="{{ asset('assets/js') }}/owl.carousel.min.js"></script>
</head>
<body id="page-top" data-spy="scroll">
<section class="accesbility-tab">
			<ul>
                <li id="colorCon" title="Toggle High contrast"> 
                    <img src="/assets/images/svgicons/text-color.svg"/>
                </li>
				<li id="colorConSelect" class="active" title="Toggle High contrast" style="display:none">
                    <img  src="/assets/images/svgicons/text-color-contrast.svg"/>
                </li>
				<li id="textSmall" title="Toggle Small Font size">
                    <img class="show-img p-4-8" src="/assets/images/svgicons/text-small.svg"/>  
                    <img class="hide-img p-4-8" style="display:none" src="/assets/images/svgicons/text-small-contrast.svg"/>
                </li>
				<li id="textSmallSelect" class="active" title="Toggle Small Font size" style="display:none">
                    <img class="show-img p-4-8" src="/assets/images/svgicons/text-small.svg"/>
                    <img class="hide-img p-4-8" style="display:none" src="/assets/images/svgicons/text-small-contrast.svg"/>
                </li>
				<li id="textMedium" title="Normal Font size">
                    <img class="show-img" src="/assets/images/svgicons/text-medium.svg"/>
                    <img class="hide-img" style="display:none" src="/assets/images/svgicons/text-medium-contrast.svg"/>
                </li> 
                <li id="textLargeSelect" class="active" title="Toggle Large Font size"  style="display:none;border-bottom:1px solid transparent">
                    <img class="show-img" src="/assets/images/svgicons/text-large.svg"/>
                    <img class="hide-img" style="display:none" src="/assets/images/svgicons/text-large-contrast.svg"/>
                </li> 
				<li id="textLarge" title="Toggle Large Font size">
                    <img class="show-img" src="/assets/images/svgicons/text-large.svg"/>
                    <img class="hide-img" style="display:none" src="/assets/images/svgicons/text-large-contrast.svg"/>
                </li>  
			</ul>
</section>

<script>
        // accessbility contrast click
        $(document).ready(function(){ 
            $("#colorCon").click(function(){ 
                $('#colorConLink').attr('href','{{ url('') }}/assets/css/contrast-style.css?v=3') ; 
                $('#colorCon').hide();
                $('#colorConSelect').show();
                $('.show-img').hide();
                $('.hide-img').show();
            }); 
            $("#colorConSelect").click(function(){ 
                $('#colorConLink').attr('href',' ') ; 
                $('#colorConSelect').hide(); 
                $('#colorCon').show();
                $('.show-img').show();
                $('.hide-img').hide();
            }); 
        });
        // accessbility text decrease click
        $(document).ready(function(){ 
            $("#textSmall").click(function(){ 
                $('#textSizeLink').attr('href','{{ url('') }}/assets/css/font-dec.css?v=3') ; 
                $('#textSmall').hide();
                $('#textLargeSelect').hide();
                $('#textLarge').show();
                $('#textSmallSelect').show();
            }); 
            $("#textSmallSelect").click(function(){ 
                $('#textSizeLink').attr('href',' ') ; 
                $('#textSmallSelect').hide(); 
                $('#textSmall').show();
            }); 
        });
        // accessbility text increase click
        $(document).ready(function(){ 
            $("#textLarge").click(function(){ 
                $('#textSizeLink').attr('href','{{ url('') }}/assets/css/font-incr.css?v=3') ; 
                $('#textLarge').hide();
                $('#textSmallSelect').hide(); 
                $('#textLargeSelect').show();
                $('#textSmall').show();
            }); 
            $("#textLargeSelect").click(function(){ 
                $('#textSizeLink').attr('href',' ') ; 
                $('#textLargeSelect').hide(); 
                $('#textLarge').show();
            }); 
        });
           // accessbility text normal click
           $(document).ready(function(){ 
            $("#textMedium").click(function(){ 
                $('#textSizeLink').attr('href',' ') ; 
                $('#textSmall').show(); 
                $('#textLarge').show(); 
                $('#textSmallSelect').hide(); 
                $('#textLargeSelect').hide(); 
               
            }); 
        });
</script>
    <!-- fb page script -->
    <div id="fb-root"></div>
    <!-- <script>(function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return;
      js = d.createElement(s); js.id = id;
      js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.9";
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script> -->
    <!-- fb page script -->
    <?php echo $__env->make('public.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render();?>
    <?php echo $__env->make('public.'.$page, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render();?>
    <?php echo $__env->make('public.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render();?>
    <script type="text/javascript" src="{{ asset('assets/js') }}/bootstrap.min.js"></script>
    <script type="text/javascript" src="{{ asset('assets/js') }}/jquery.easing.1.3.js"></script>
    <script type="text/javascript" src="{{ asset('assets/js') }}/jquery.waypoints.min.js"></script>
    <script type="text/javascript" src="{{ asset('assets/js') }}/jquery.counterup.min.js"></script>
    <script type="text/javascript" src="{{ asset('assets/js') }}/swiper.min.js"></script>
    <script type="text/javascript" src="{{ asset('assets/js') }}/plugins.isotope.js"></script>
    <script type="text/javascript" src="{{ asset('assets/js') }}/isotope.pkgd.min.js"></script>
    <script type="text/javascript" src="{{ asset('assets/js') }}/lightcase.js"></script>
    <script type="text/javascript" src="{{ asset('assets/js') }}/jquery.nstSlider.js"></script>
    <script type="text/javascript" src="{{ asset('assets/js') }}/jquery.flexslider.js"></script>
    <script type="text/javascript" src="{{ asset('assets/js') }}/custom.isotope.js"></script>
    <script type="text/javascript" src="{{ asset('assets/js') }}/custom.js"></script>
    <script type="text/javascript" src="{{ asset('assets/js') }}/rtd-toasty.js"></script>
    <script type="text/javascript" src="{{ asset('assets/js') }}/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="{{ asset('assets/js') }}/dataTables.bootstrap.min.js"></script>
    <script type="text/javascript">
     function validateform()
     {
      if($('#all-search').val()!='')
      {
       return true;
     }
     else
     {
      $.toasty('Search field is required! ','danger');
      return false;
    }
    }
    var table=$('.datatables').DataTable( {
        "order": [],
        columnDefs: [ { orderable: false, targets: [0] } ]
    } );
    $(function(){

        /*$('.dataTables_filter').addClass('hide');*/
    });
    $(document).on( 'change', '.secotr_filter',function () {
        table.columns(2).search($(this).val()).draw();
    } );
    $(document).on( 'change', '.state_filter',function () {
        table.columns(4).search($(this).val()).draw();
    } );
    $(document).on( 'change', '.size_filter',function () {
        table.columns(5).search($(this).val()).draw();
    } );
    $(document).on( 'change', '.rate_filter',function () {
        table.columns(6).search($(this).val()).draw();
    } );
    table.on( 'order.dt search.dt', function () {
        table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
        } );
    } ).draw();
    </script>
    @if (site_flag('header_show_bhashini'))
    {{-- Bhashini language menu (CMS > Settings > Header and footer); see public/js/bhashini.js --}}
    <script src="{{ asset('js/bhashini.js') }}" nonce="{{ Vite::cspNonce() }}" data-src="{{ config('zed.bhashini_url') }}"></script>
    @endif
</body>
</html>