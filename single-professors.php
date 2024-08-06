<?php
get_header();

if (have_posts()) {
    while (have_posts()) {
        the_post(); 
        getBanner();
?>
        <!-- <div class="page-banner">
            <div class="page-banner__bg-image" style="background-image: url(<?php $pageBanner = get_field('page_banner_background_image'); echo $pageBanner['sizes']["pageBanner"]; ?>);"></div>
            <div class="page-banner__content container container--narrow">
                <h1 class="page-banner__title"><?php the_title(); ?></h1>
                <div class="page-banner__intro">
                    <p><?php the_field("page_banner_subtitle") ?></p>
                </div>
            </div>
        </div> -->


        <div class="container container--narrow page-section">
            <div class="generic-content">
                <div class="row group">
                    <div class="one-third">
                        <img src="<?php the_post_thumbnail_url('professorLandscape'); ?>" alt="">
                    </div>
                    <div class="two-thirds">
                        <?php the_content(); ?>
                    </div>
                </div>
            </div>

            <?php
            $relatedPrograms = get_field('related_programs');
            if ($relatedPrograms) {
            ?>
                <hr class="section-break">
                <h3 class="headline headline--medium">
                    Related <?php echo get_the_title(); ?> Program  
                </h3>
                <ul class="link-list min-list">
                    <?php
                    foreach ($relatedPrograms as $program) {
                    ?>
                        <li><a href="<?php echo get_the_permalink($program); ?>"><?php echo get_the_title($program); ?></a></li>
                    <?php
                    }
                    ?>
                </ul>
            <?php
            }
            ?>
        </div>
<?php
    }
}

get_footer()
?>