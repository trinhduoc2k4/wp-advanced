<div class="event-summary">
    <a class="event-summary__date t-center" href="<?php the_permalink(); ?>">
        <?php 
        $eventsDate = new DateTime(get_field('events_date'));
        ?>
        <span class="event-summary__month"><?php echo $eventsDate->format('M'); ?></span>
        <span class="event-summary__day"><?php echo $eventsDate->format('d'); ?></span>
    </a>
    <div class="event-summary__content">
        <h5 class="event-summary__title headline headline--tiny"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
        <p><?php echo wp_trim_words( get_the_content(), 25 ); ?><a href="<?php the_permalink(); ?>" class="nu gray">Read more</a></p>
    </div>
</div>